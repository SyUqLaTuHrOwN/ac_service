<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'report_id',
        'client_user_id',
        'rating',
        'comment',
        'is_public',
        'approved_at',
    ];

    protected $casts = [
        'is_public'   => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function clientUser()
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    public function report()
    {
        return $this->belongsTo(MaintenanceReport::class, 'report_id');
    }

    // memudahkan akses jadwal dari feedback
    public function schedule()
    {
        return $this->hasOneThrough(
            MaintenanceSchedule::class,
            MaintenanceReport::class,
            'id',          // key di reports
            'id',          // key di schedules
            'report_id',   // FK feedback -> reports
            'schedule_id'  // FK reports -> schedules
        );
    }

    // hanya yang disetujui admin
    public function scopePublished($q)
    {
        return $q->where('is_public', true)->whereNotNull('approved_at');
    }
}
