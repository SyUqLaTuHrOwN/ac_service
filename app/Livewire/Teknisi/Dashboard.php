<?php

namespace App\Livewire\Teknisi;

use Livewire\Component;
use App\Models\MaintenanceSchedule;

class Dashboard extends Component
{
    public function render()
    {
        $uid = auth()->id();

        // aktif: menunggu / dalam_proses
        $activeTasks = MaintenanceSchedule::with(['client','location'])
            ->where(function ($q) use ($uid) {
                $q->where('assigned_user_id', $uid)
                  ->orWhere('technician_id', $uid); // kompatibel data lama
            })
            ->whereIn('status', ['menunggu','dalam_proses'])
            ->orderBy('scheduled_at')
            ->get();

        // riwayat: selesai_servis / selesai
        $history = MaintenanceSchedule::with(['client','location'])
            ->where(function ($q) use ($uid) {
                $q->where('assigned_user_id', $uid)
                  ->orWhere('technician_id', $uid); // kompatibel data lama
            })
            ->whereIn('status', ['selesai_servis','selesai'])
            ->latest('scheduled_at')
            ->limit(5)
            ->get();

        return view('livewire.teknisi.dashboard', compact('activeTasks','history'))
            ->layout('layouts.app', [
                'title'  => 'Dashboard Teknisi',
                'header' => 'Dashboard Teknisi',
            ]);
    }
}
