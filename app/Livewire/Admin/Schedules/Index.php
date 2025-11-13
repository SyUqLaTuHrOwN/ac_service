<?php

namespace App\Livewire\Admin\Schedules;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MaintenanceSchedule;
use App\Models\Client;
use App\Models\Location;
use App\Models\UnitAc;
use App\Models\User;
use App\Support\Role;
use App\Models\TechnicianLeave;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;

class Index extends Component
{
    use WithPagination;

    // Filter list
    public ?int $clientFilter = null;
    public ?int $statusFilter = null; // 1: menunggu, 2: dalam_proses, 3: selesai_servis, 4: selesai
    public string $search = '';

    // Form state
    public ?int $editingId = null;
    public ?int $client_id = null;
    public ?int $location_id = null;
    public ?string $scheduled_at = null;
    public ?int $assigned_user_id = null;
    public ?int $technician_id = null;   // ⬅️ BARU
    public string $status = 'menunggu';
    public ?string $notes = null;

    // pilih banyak unit
    public array $unit_ids = [];

    protected function rules(): array
    {
        return [
            'client_id'        => ['required','exists:clients,id'],
            'location_id'      => ['required','exists:locations,id'],
            'scheduled_at'     => ['required','date'],
            'assigned_user_id' => ['nullable','exists:users,id'],
            'technician_id'    => ['nullable','exists:users,id'], // ⬅️ BARU (opsional)
            'status'           => ['required','string','max:50'],
            'notes'            => ['nullable','string'],
            'unit_ids'         => ['array'],
            'unit_ids.*'       => ['integer','exists:unit_acs,id'],
        ];
    }

    public function updatedClientId(): void
    {
        $this->location_id = null;
        $this->unit_ids = [];
    }

    public function updatedLocationId(): void
    {
        $this->unit_ids = [];
    }

    public function createNew(): void
    {
        $this->resetForm();
        $this->editingId = 0;
    }

    public function edit(int $id): void
    {
        $this->resetErrorBag();
        $this->editingId = $id;

        $s = MaintenanceSchedule::with('units')->findOrFail($id);

        $this->client_id        = $s->client_id;
        $this->location_id      = $s->location_id;
        $this->scheduled_at     = $s->scheduled_at?->format('Y-m-d\TH:i');
        $this->assigned_user_id = $s->assigned_user_id;
        $this->technician_id    = $s->technician_id;   // ⬅️ BARU
        $this->status           = $s->status;
        $this->notes            = $s->notes;

        $this->unit_ids = $s->units->pluck('id')->toArray();
    }

 public function save(): void
{
    $data = $this->validate();

    // ❗ Blokir jadwal pada hari cuti teknisi
    if (!empty($data['assigned_user_id']) && !empty($data['scheduled_at'])) {
        $overlap = TechnicianLeave::approved()
            ->where('user_id', $data['assigned_user_id'])
            ->overlaps(\Carbon\Carbon::parse($data['scheduled_at']))
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'assigned_user_id' => 'Teknisi sedang cuti pada tanggal tersebut.',
            ]);
        }
    }

    // lanjut proses simpan seperti sebelumnya...
    if ($this->editingId && $this->editingId > 0) {
        $schedule = \App\Models\MaintenanceSchedule::findOrFail($this->editingId);
        $schedule->update($data);
    } else {
        $schedule = \App\Models\MaintenanceSchedule::create($data);
    }
    $schedule->units()->sync($this->unit_ids);

    session()->flash('ok', $this->editingId ? 'Jadwal diperbarui.' : 'Jadwal dibuat.');
    $this->resetForm();
    $this->editingId = null;
}
    public function delete(int $id): void
    {
        MaintenanceSchedule::findOrFail($id)->delete();
        session()->flash('ok','Jadwal dihapus.');
        $this->resetPage();
    }

    public function approveReschedule(int $id): void
    {
        $s = MaintenanceSchedule::findOrFail($id);

        if (!$s->has_pending_reschedule) {
            session()->flash('err', 'Tidak ada permintaan reschedule yang menunggu.');
            return;
        }

        $s->scheduled_at          = $s->client_requested_date;
        $s->client_response       = 'confirmed';
        $s->client_response_at    = now();
        $s->client_response_note  = null;
        $s->client_requested_date = null;
        $s->status                = 'menunggu';
        $s->save();

        session()->flash('ok', 'Permintaan jadwal ulang disetujui & jadwal diperbarui.');
    }

    public function rejectReschedule(int $id): void
    {
        $s = MaintenanceSchedule::findOrFail($id);

        if (!$s->has_pending_reschedule) {
            session()->flash('err', 'Tidak ada permintaan reschedule yang menunggu.');
            return;
        }

        $s->client_response       = null;
        $s->client_response_at    = now();
        $s->client_response_note  = null;
        $s->client_requested_date = null;
        $s->status                = 'menunggu';
        $s->save();

        session()->flash('ok', 'Usulan jadwal ulang ditolak. Jadwal tetap.');
    }

    private function resetForm(): void
    {
        $this->reset([
            'client_id','location_id','scheduled_at','assigned_user_id',
            'technician_id', // ⬅️ BARU
            'status','notes','unit_ids'
        ]);
        $this->status = 'menunggu';
        $this->unit_ids = [];
    }

    public function render()
    {
        $clients = Client::orderBy('company_name')->get(['id','company_name']);

        $locationsForForm = collect();
        if ($this->client_id) {
            $locationsForForm = Location::where('client_id', $this->client_id)
                ->orderBy('name')->get(['id','name']);
        }

        $techs = User::where('role', Role::TEKNISI)->orderBy('name')->get(['id','name']); // ⬅️ dipakai dropdown

        $unitsForLocation = collect();
        if ($this->location_id) {
            $unitsForLocation = UnitAc::where('location_id', $this->location_id)
                ->orderBy('brand')->orderBy('model')
                ->get(['id','brand','model','serial_number']);
        }

        $q = MaintenanceSchedule::with(['client','location','technician','units'])
            ->when($this->clientFilter, fn($qq)=>$qq->where('client_id',$this->clientFilter))
            ->when($this->statusFilter, function($qq){
                $map = [1=>'menunggu',2=>'dalam_proses',3=>'selesai_servis',4=>'selesai'];
                $status = $map[$this->statusFilter] ?? null;
                if ($status) $qq->where('status', $status);
            })
            ->when($this->search, fn($qq)=>$qq->where('notes','like',"%{$this->search}%"))
            ->orderBy('scheduled_at','desc');

        $schedules = $q->paginate(10);

        return view('livewire.admin.schedules.index', [
            'clients'          => $clients,
            'locations'        => $locationsForForm,
            'techs'            => $techs,
            'schedules'        => $schedules,
            'unitsForLocation' => $unitsForLocation,
        ])->layout('layouts.app', [
            'title'=>'Jadwal Maintenance',
            'header'=>'Operasional • Jadwal',
        ]);
    }
}
