<?php

namespace App\Livewire\Teknisi\Tasks;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceReport;
use Illuminate\Support\Facades\Storage;
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

    public function mount()
    {
        // default: bulan ini
        $start = now()->startOfMonth();
        $end   = now()->endOfMonth();
        $this->from_date = $start->format('Y-m-d');
        $this->to_date   = $end->format('Y-m-d');
    }

    public function monthThis()
    {
        $this->from_date = now()->startOfMonth()->format('Y-m-d');
        $this->to_date   = now()->endOfMonth()->format('Y-m-d');
    }

    public function getSchedules()
    {
        $techId = auth()->id();

        return MaintenanceSchedule::with(['client','location','report'])
            ->where('technician_id', $techId)
            // Jadwal yang belum selesai / dibatalkan saja
            ->whereNotIn('status', ['selesai_servis','dibatalkan_oleh_klien','dibatalkan_admin'])
            ->whereBetween('scheduled_at', [
                Carbon::parse($this->from_date)->startOfDay(),
                Carbon::parse($this->to_date)->endOfDay(),
            ])
            ->orderBy('scheduled_at')
            ->get();
    }

    public function canStart(MaintenanceSchedule $s): bool
    {
        // hanya boleh mulai pada tanggal pelaksanaan (zona Jakarta)
        return $s->scheduled_at?->isSameDay(now('Asia/Jakarta'));
    }

    public function start(int $scheduleId)
    {
        $s = MaintenanceSchedule::findOrFail($scheduleId);

        // tag mulai (tanpa foto pun boleh; foto bisa menyusul)
        $report = MaintenanceReport::firstOrCreate(
            ['schedule_id' => $s->id],
            ['technician_id' => auth()->id(), 'status' => 'draft', 'started_at' => now()]
        );

        if (!$report->started_at) $report->update(['started_at' => now()]);
        $this->dispatch('toast', message:'Pekerjaan dimulai.', type:'ok');
    }

    public function finish(int $scheduleId)
    {
        $s = MaintenanceSchedule::with('report')->findOrFail($scheduleId);

        // pastikan report ada
        $report = MaintenanceReport::firstOrCreate(
            ['schedule_id' => $s->id],
            ['technician_id' => auth()->id()]
        );

        $report->update([
            'finished_at' => now(),
            'status'      => 'submitted', // teknisi selesai; menunggu verifikasi admin
            'notes'       => $this->note ?: $report->notes,
        ]);

        $s->update(['status' => 'selesai_servis']);

        // bersih-bersih form
        $this->reset(['selectedScheduleId','start_photo','end_photo','receipt_file','note']);

        $this->dispatch('toast', message:'Tugas ditandai selesai.', type:'ok');
    }

    /* ---------- Upload berkas ---------- */

    public function openUpload(int $scheduleId)
    {
        $this->selectedScheduleId = $scheduleId;
        $this->reset(['start_photo','end_photo','receipt_file','note']);
        $this->dispatch('open-upload'); // Alpine modal
    }

    public function saveStartPhoto()
    {
        $this->validate(['start_photo' => 'required|image|max:4096']);

        $s = MaintenanceSchedule::findOrFail($this->selectedScheduleId);
        $path = $this->start_photo->store('reports', 'public');

        $report = MaintenanceReport::firstOrCreate(
            ['schedule_id' => $s->id],
            ['technician_id' => auth()->id()]
        );

        $report->update([
            'start_photo_path' => $path,
            'started_at'       => $report->started_at ?: now(),
        ]);

        $this->dispatch('toast', message:'Foto mulai tersimpan.', type:'ok');
        $this->reset('start_photo');
        $this->dispatch('close-upload');
    }

    public function saveEndPhoto()
    {
        $this->validate(['end_photo' => 'required|image|max:4096']);

        $s = MaintenanceSchedule::findOrFail($this->selectedScheduleId);
        $path = $this->end_photo->store('reports', 'public');

        $report = MaintenanceReport::firstOrCreate(
            ['schedule_id' => $s->id],
            ['technician_id' => auth()->id()]
        );

        $report->update([
            'end_photo_path' => $path,
        ]);

        $this->dispatch('toast', message:'Foto selesai tersimpan.', type:'ok');
        $this->reset('end_photo');
        $this->dispatch('close-upload');
    }

    public function saveReceipt()
    {
        $this->validate(['receipt_file' => 'required|image|max:4096']);

        $s = MaintenanceSchedule::findOrFail($this->selectedScheduleId);
        $path = $this->receipt_file->store('reports', 'public');

        $report = MaintenanceReport::firstOrCreate(
            ['schedule_id' => $s->id],
            ['technician_id' => auth()->id()]
        );

        $report->update(['receipt_path' => $path]);

        $this->dispatch('toast', message:'Nota/struk tersimpan.', type:'ok');
        $this->reset('receipt_file');
        $this->dispatch('close-upload');
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
