<?php

namespace App\Livewire\Teknisi\Tasks;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceReport;
use Carbon\Carbon;

class Index extends Component
{
    use WithFileUploads;

    public string $from_date;
    public string $to_date;

    // modal upload
    public ?int $selectedScheduleId = null;
    public $start_photo;
    public $end_photo;
    public $receipt_file;
    public string $note = '';

    public function mount(): void
    {
        // default ke bulan ini
        $this->from_date = now('Asia/Jakarta')->startOfMonth()->format('Y-m-d');
        $this->to_date   = now('Asia/Jakarta')->endOfMonth()->format('Y-m-d');
    }

    public function monthThis(): void
    {
        $this->from_date = now('Asia/Jakarta')->startOfMonth()->format('Y-m-d');
        $this->to_date   = now('Asia/Jakarta')->endOfMonth()->format('Y-m-d');
    }

    /** Rentang tanggal dgn zona waktu Jakarta */
    protected function range(): array
    {
        return [
            Carbon::parse($this->from_date, 'Asia/Jakarta')->startOfDay(),
            Carbon::parse($this->to_date, 'Asia/Jakarta')->endOfDay(),
        ];
    }

    /** Ambil daftar tugas teknisi (aktif) + relasi yang dibutuhkan */
    public function getSchedules()
    {
        [$from, $to] = $this->range();

        return MaintenanceSchedule::with([
                'client:id,company_name',
                'location:id,name',
                'report:id,schedule_id,status,started_at,finished_at,notes',
                'units:id,location_id,brand,model,serial_number',
            ])
            ->forTechnician(auth()->id())   // scope di Model
            ->active()                      // scope di Model
            ->betweenDateRange($from, $to)  // scope di Model
            ->orderBy('scheduled_at')
            ->get();
    }

    /** Mulai pekerjaan – hanya saat waktunya (guard via Model::canStart) */
    public function start(int $scheduleId): void
    {
        $s = MaintenanceSchedule::with('report')->findOrFail($scheduleId);

        if (!$s->canStart()) {
            $this->dispatch('toast', message: 'Belum waktu pelaksanaan.', type: 'err');
            return;
        }

        $report = MaintenanceReport::firstOrCreate(
            ['schedule_id' => $s->id],
            ['technician_id' => auth()->id(), 'status' => 'draft']
        );

        if (!$report->started_at) {
            $report->started_at = now('Asia/Jakarta');
            $report->save();
        }

        if ($s->status !== \App\Models\MaintenanceSchedule::ST_RUN) {
            $s->status = \App\Models\MaintenanceSchedule::ST_RUN; // "dalam_proses"
            $s->save();
        }

        $this->dispatch('toast', message: 'Pekerjaan dimulai.', type: 'ok');
    }

    /** Selesaikan pekerjaan – butuh minimal sudah start */
    public function finish(int $scheduleId): void
    {
        $s = MaintenanceSchedule::with('report')->findOrFail($scheduleId);

        $report = MaintenanceReport::firstOrCreate(
            ['schedule_id' => $s->id],
            ['technician_id' => auth()->id(), 'status' => 'draft', 'started_at' => now('Asia/Jakarta')]
        );

        if (!$report->started_at) {
            $this->dispatch('toast', message: 'Belum memulai pekerjaan.', type: 'err');
            return;
        }

        $report->update([
            'finished_at' => now('Asia/Jakarta'),
            'status'      => 'submitted', // menunggu verifikasi admin
            'notes'       => trim($this->note) ?: $report->notes,
        ]);

        $s->update(['status' => \App\Models\MaintenanceSchedule::ST_DONE]); // "selesai_servis"

        // bersihkan form/modal
        $this->reset(['selectedScheduleId','start_photo','end_photo','receipt_file','note']);

        $this->dispatch('toast', message: 'Tugas ditandai selesai.', type: 'ok');
    }

    /* ================= Upload berkas ================= */

    public function openUpload(int $scheduleId): void
    {
        $this->selectedScheduleId = $scheduleId;
        $this->reset(['start_photo','end_photo','receipt_file','note']);
        $this->dispatch('open-upload'); // dibaca Alpine di Blade
    }

    public function saveStartPhoto(): void
    {
        $this->validate(['start_photo' => 'required|image|max:4096']);

        $s = MaintenanceSchedule::findOrFail($this->selectedScheduleId);
        $path = $this->start_photo->store('reports', 'public');

        $report = MaintenanceReport::firstOrCreate(
            ['schedule_id' => $s->id],
            ['technician_id' => auth()->id(), 'status' => 'draft']
        );

        $report->update([
            'start_photo_path' => $path,
            'started_at'       => $report->started_at ?: now('Asia/Jakarta'),
        ]);

        $this->reset('start_photo');
        $this->dispatch('close-upload');
        $this->dispatch('toast', message: 'Foto mulai tersimpan.', type: 'ok');
    }

    public function saveEndPhoto(): void
    {
        $this->validate(['end_photo' => 'required|image|max:4096']);

        $s = MaintenanceSchedule::findOrFail($this->selectedScheduleId);
        $path = $this->end_photo->store('reports', 'public');

        $report = MaintenanceReport::firstOrCreate(
            ['schedule_id' => $s->id],
            ['technician_id' => auth()->id(), 'status' => 'draft']
        );

        $report->update(['end_photo_path' => $path]);

        $this->reset('end_photo');
        $this->dispatch('close-upload');
        $this->dispatch('toast', message: 'Foto selesai tersimpan.', type: 'ok');
    }

    public function saveReceipt(): void
    {
        $this->validate(['receipt_file' => 'required|image|max:4096']);

        $s = MaintenanceSchedule::findOrFail($this->selectedScheduleId);
        $path = $this->receipt_file->store('reports', 'public');

        $report = MaintenanceReport::firstOrCreate(
            ['schedule_id' => $s->id],
            ['technician_id' => auth()->id(), 'status' => 'draft']
        );

        $report->update(['receipt_path' => $path]);

        $this->reset('receipt_file');
        $this->dispatch('close-upload');
        $this->dispatch('toast', message: 'Nota/struk tersimpan.', type: 'ok');
    }

    public function render()
    {
        $schedules = $this->getSchedules();

        return view('livewire.teknisi.tasks.index', compact('schedules'))
            ->layout('layouts.app', [
                'title'  => 'Tugas Saya',
                'header' => 'Teknisi • Tugas Bulanan',
            ]);
    }
}
