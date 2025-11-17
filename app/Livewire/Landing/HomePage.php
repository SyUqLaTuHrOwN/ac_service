<?php

namespace App\Livewire\Landing;

use Livewire\Component;
use App\Models\Feedback;

class HomePage extends Component
{
    public function render()
    {
        // Ambil testimoni yang sudah disetujui admin
        $testimonials = Feedback::with(['report.schedule.client.user'])
            ->whereNotNull('approved_at')
            ->latest('approved_at')
            ->limit(12)
            ->get();

        $avgRating   = (float) (Feedback::whereNotNull('approved_at')->avg('rating') ?? 0);
        $countRating = (int) Feedback::whereNotNull('approved_at')->count();

        return view('livewire.landing.home-page', [
            'testimonials' => $testimonials,
            'avgRating'    => $avgRating,
            'countRating'  => $countRating,
        ])->layout('layouts.guest', [
            'title' => 'Beranda',
        ]);
    }
}
