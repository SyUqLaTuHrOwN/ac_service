<?php

namespace App\Livewire\Teknisi\Reports;

use Livewire\Component;
use App\Models\MaintenanceReport;
use Carbon\Carbon;

class Index extends Component
{
    public string $from_date;
    public string $to_date;
    public string $statusFilter = '';

    public function mount()
    {
        $this->from_date = now()->startOfMonth()->format('Y-m-d');
        $this->to_date   = now()->endOfMonth()->format('Y-m-d');
    }

    public function monthThis()
    {
        $this->from_date = now()->startOfMonth()->format('Y-m-d');
        $this->to_date   = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $q = MaintenanceReport::with(['schedule.client','schedule.location'])
            ->where('technician_id', auth()->id())
            ->whereBetween('created_at', [
                Carbon::parse($this->from_date)->startOfDay(),
                Carbon::parse($this->to_date)->endOfDay(),
            ])
            ->orderByDesc('created_at');

        if ($this->statusFilter) $q->where('status', $this->statusFilter);

        $reports = $q->get();

        return view('livewire.teknisi.reports.index', compact('reports'))
            ->layout('layouts.app', ['title'=>'Laporan Saya','header'=>'Teknisi • Laporan']);
    }
}
