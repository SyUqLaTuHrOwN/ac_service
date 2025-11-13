<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    // Jika tabel kamu bernama 'feedback' (bukan 'feedbacks'), wajib set:
    protected $table = 'feedback';

    // ✅ Tambahkan client_user_id agar tidak dibuang saat create()
    protected $fillable = [
        'report_id',
        'client_user_id',  // <— penting
        'rating',
        'comment',
        // kalau kamu pakai nama lain seperti 'given_by_user', pakai itu di sini
    ];

    public function report()
    {
        return $this->belongsTo(\App\Models\MaintenanceReport::class, 'report_id');
    }

    public function clientUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'client_user_id');
    }
}
