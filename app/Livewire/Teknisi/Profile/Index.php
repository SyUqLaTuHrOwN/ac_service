<?php

namespace App\Livewire\Teknisi\Profile;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        // Ambil user + relasi profil teknisi
        $user = auth()->user()->load('technicianProfile');

        return view('livewire.teknisi.profile.index', [
            'user' => $user,
        ])->layout('layouts.app', [
            'title'  => 'Profil Teknisi',
            'header' => 'Teknisi • Profil',
        ]);
    }

    // Penting: hilangkan semua method penyimpanan/ubah di komponen ini.
    // Jika sebelumnya ada method save/update, HAPUS.
}
