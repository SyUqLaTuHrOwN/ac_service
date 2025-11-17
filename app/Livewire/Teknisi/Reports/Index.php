<?php

namespace App\Livewire\Teknisi\Reports;

use Livewire\Component;
use App\Models\MaintenanceReport;
use Carbon\Carbon;

class Index extends Component
{
    public string $from_date;
    public string $to_date;
    public ?string $status = null; // "draft" | "submitted" | "disetujui" | "revisi" | null

    public function mount(): void
    {
        $this->from_date = now('Asia/Jakarta')->startOfMonth()->format('Y-m-d');
        $this->to_date   = now('Asia/Jakarta')->endOfMonth()->format('Y-m-d');
    }

    public function monthThis(): void
    {
        $this->from_date = now('Asia/Jakarta')->startOfMonth()->format('Y-m-d');
        $this->to_date   = now('Asia/Jakarta')->endOfMonth()->format('Y-m-d');
    }

    protected function range(): array
    {
        return [
            Carbon::parse($this->from_date, 'Asia/Jakarta')->startOfDay(),
            Carbon::parse($this->to_date, 'Asia/Jakarta')->endOfDay(),
        ];
    }

    /** Ambil laporan teknisi + client, lokasi, dan UNIT AC dari schedule */
    protected function getReports()
    {
        [$from, $to] = $this->range();
        $techId = auth()->id();

        return MaintenanceReport::with([
                // relasi yang akan dipakai di tabel
                'schedule.client:id,company_name',
                'schedule.location:id,name',
                'schedule.units:id,location_id,brand,model,serial_number',
            ])
            ->where('technician_id', $techId)
            // filter berdasarkan rentang jadwal (bukan created_at), supaya konsisten dgn tampilan
            ->whereHas('schedule', fn($q) => $q->whereBetween('scheduled_at', [$from, $to]))
            // filter status opsional
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->latest('finished_at')
            ->get();
    }

    public function render()
    {
        $reports = $this->getReports();

        return view('livewire.teknisi.reports.index', compact('reports'))
            ->layout('layouts.app', [
                'title'  => 'Laporan',
                'header' => 'Teknisi • Laporan',
            ]);
    }
}
