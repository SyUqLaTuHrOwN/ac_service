<?php

namespace App\Livewire\Teknisi\Feedbacks;

use Livewire\Component;
use App\Models\Feedback;

class Index extends Component
{
    public function render()
    {
        $uid = auth()->id();

        $items = Feedback::with(['clientUser','report.schedule.client','report.schedule.location'])
            ->whereHas('report.schedule', fn($q)=> $q->where('assigned_user_id', $uid))
            ->latest()
            ->get();

        return view('livewire.teknisi.feedbacks.index', compact('items'))
            ->layout('layouts.app', [
                'title'=>'Ulasan Pelanggan',
                'header'=>'Teknisi • Ulasan Pelanggan',
            ]);
    }
}
