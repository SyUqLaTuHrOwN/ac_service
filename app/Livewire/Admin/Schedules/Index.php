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

    // FILTER
    public ?string $search = '';
    public ?int $clientFilter = null;
    public ?int $statusFilter = null;

    // FORM
    public ?int $client_id = null;
    public ?int $location_id = null;
    public ?string $scheduled_at = null;
    public ?int $assigned_user_id = null; // teknisi yang ditugaskan (nullable)
    public string $status = 'menunggu';
    public ?string $notes = null;
    public array $unit_ids = [];

    public ?int $editingId = null;

    protected function rules(): array
    {
        return [
            'client_id'        => ['required', 'exists:clients,id'],
            'location_id'      => ['required', 'exists:locations,id'],
            'scheduled_at'     => ['required', 'date'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'status'           => ['required', 'string'],
            'notes'            => ['nullable', 'string'],
            'unit_ids'         => ['array'],
            'unit_ids.*'       => ['integer', 'exists:unit_acs,id'],
        ];
    }

    public function updatedClientId()
    {
        $this->location_id = null;
        $this->unit_ids = [];
    }

    public function updatedLocationId()
    {
        $this->unit_ids = [];
    }

    public function createNew()
    {
        $this->resetForm();
        $this->editingId = 0;
    }

    public function edit(int $id)
    {
        $this->resetErrorBag();
        $this->editingId = $id;

        $s = MaintenanceSchedule::with('units')->findOrFail($id);

        $this->client_id        = $s->client_id;
        $this->location_id      = $s->location_id;
        $this->scheduled_at     = $s->scheduled_at?->format('Y-m-d\TH:i');
        $this->assigned_user_id = $s->assigned_user_id; // ambil nilai dari db (bisa null)
        $this->status           = $s->status;
        $this->notes            = $s->notes;
        $this->unit_ids         = $s->units->pluck('id')->toArray();
    }

    public function save()
    {
        $data = $this->validate();

        // CEK TEKNISI SEDANG CUTI HANYA JIKA DIPILIH
        if (!empty($data['assigned_user_id'])) {
            $date = Carbon::parse($data['scheduled_at']);

            $isOnLeave = TechnicianLeave::approved()
                ->where('user_id', $data['assigned_user_id'])
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->exists();

            if ($isOnLeave) {
                throw ValidationException::withMessages([
                    'assigned_user_id' => 'Teknisi sedang CUTI pada tanggal tersebut.',
                ]);
            }
        }

        // SIMPAN (update/create)
        if ($this->editingId && $this->editingId > 0) {
            $schedule = MaintenanceSchedule::findOrFail($this->editingId);
            // update with validated fields (works because fillable set)
            $schedule->update([
                'client_id'        => $data['client_id'],
                'location_id'      => $data['location_id'],
                'scheduled_at'     => $data['scheduled_at'],
                'assigned_user_id' => $data['assigned_user_id'] ?? null,
                'status'           => $data['status'],
                'notes'            => $data['notes'] ?? null,
            ]);
        } else {
            $schedule = MaintenanceSchedule::create([
                'client_id'        => $data['client_id'],
                'location_id'      => $data['location_id'],
                'scheduled_at'     => $data['scheduled_at'],
                'assigned_user_id' => $data['assigned_user_id'] ?? null,
                'status'           => $data['status'],
                'notes'            => $data['notes'] ?? null,
            ]);
        }

        // sync units
        $schedule->units()->sync($this->unit_ids);

        session()->flash('ok', $this->editingId ? 'Jadwal diperbarui.' : 'Jadwal dibuat.');

        $this->resetForm();
        $this->editingId = null;
    }

    public function delete(int $id)
    {
        MaintenanceSchedule::findOrFail($id)->delete();
        session()->flash('ok', 'Jadwal dihapus.');
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->reset([
            'client_id',
            'location_id',
            'scheduled_at',
            'assigned_user_id',
            'status',
            'notes',
            'unit_ids'
        ]);

        $this->status = 'menunggu';
        $this->unit_ids = [];
    }

    public function render()
    {
        $clients = Client::orderBy('company_name')->get(['id', 'company_name']);

        $locations = $this->client_id
            ? Location::where('client_id', $this->client_id)->orderBy('name')->get(['id', 'name'])
            : collect();

        // Ambil semua teknisi dengan profil
        $allTechs = User::with('technicianProfile')
            ->where('role', Role::TEKNISI)
            ->orderBy('name')
            ->get();

        // Filter teknisi: harus punya profil, aktif, dan tidak sedang cuti
        $availableTechs = $allTechs->filter(function ($tech) {
            if (!$tech->technicianProfile) return false;
            if (!$tech->technicianProfile->is_active) return false;
            return !$tech->isOnLeave();
        })->values();

        // Jika kita sedang edit dan jadwal ini sudah punya assigned_user_id,
        // pastikan teknisi yang sudah terpasang tetap ada di daftar option
        if ($this->editingId && $this->assigned_user_id) {
            $already = $availableTechs->firstWhere('id', $this->assigned_user_id);
            if (!$already) {
                $current = $allTechs->firstWhere('id', $this->assigned_user_id);
                if ($current) {
                    // tambahkan ke akhir daftar (tetap tampil meskipun cuti/nonaktif)
                    $availableTechs->push($current);
                }
            }
        }

        $units = $this->location_id
            ? UnitAc::where('location_id', $this->location_id)->orderBy('brand')->get()
            : collect();

        $schedules = MaintenanceSchedule::with(['client', 'location', 'technician', 'units'])
            ->when($this->clientFilter, fn($q) => $q->where('client_id', $this->clientFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, fn($q) =>
                $q->where('notes', 'like', '%' . $this->search . '%')
            )
            ->orderBy('scheduled_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.schedules.index', [
            'clients'           => $clients,
            'locations'         => $locations,
            'techs'             => $availableTechs,
            'schedules'         => $schedules,
            'unitsForLocation'  => $units,
        ])->layout('layouts.app', [
            'title'  => 'Jadwal Maintenance',
            'header' => 'Operasional • Jadwal',
        ]);
    }
}
