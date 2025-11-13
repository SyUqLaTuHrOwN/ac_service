<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceReport extends Model
{
    protected $fillable = [
        'schedule_id',
        'technician_id',
        'units_serviced',
        'notes',
        'photos',               // simpan array path bila pakai multiple
        'invoice_number',
        'status',               // draft|submitted|revisi|disetujui
        'verified_by_admin_id',
        'verified_at',

        // jika kamu sudah menambahkan kolom start/finish & files tunggal
        'started_at',
        'finished_at',
        'start_photo_path',
        'end_photo_path',
        'receipt_path',
    ];

    protected $casts = [
        'photos'      => 'array',
        'verified_at' => 'datetime',
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    /** Laporan milik satu jadwal */
    public function schedule()
    {
        return $this->belongsTo(\App\Models\MaintenanceSchedule::class, 'schedule_id');
    }

    /** Teknisi yang mengerjakan laporan */
    public function technician()
    {
        return $this->belongsTo(\App\Models\User::class, 'technician_id');
    }

    /** Admin yang memverifikasi */
    public function verifier()
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by_admin_id');
    }

      public function feedback()
    {
        // satu laporan punya satu feedback dari client
        return $this->hasOne(\App\Models\Feedback::class, 'report_id');
    }
}
