<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    // Status
    public const ST_WAIT   = 'menunggu';
    public const ST_RUN    = 'dalam_proses';
    public const ST_DONE   = 'selesai_servis';
    public const ST_FINAL  = 'selesai';
    public const ST_CANCEL_CLIENT = 'dibatalkan_oleh_klien';
    public const ST_CANCEL_ADMIN  = 'dibatalkan_admin';

    protected $fillable = [
        'client_id',
        'location_id',
        'scheduled_at',
        'assigned_user_id',
        'status',
        'notes',
        'client_response',
        'client_response_at',
        'client_requested_date',
        'client_response_note',
    ];

    protected $casts = [
        'scheduled_at'          => 'datetime',
        'client_response_at'    => 'datetime',
        'client_requested_date' => 'datetime',
    ];

    /* ===================== RELATIONS ===================== */

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /** Relasi teknisi utama memakai assigned_user_id */
    public function technician()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function report()
    {
        return $this->hasOne(\App\Models\MaintenanceReport::class, 'schedule_id');
    }

    public function units()
    {
        return $this->belongsToMany(
            UnitAc::class,
            'maintenance_schedule_units',
            'schedule_id',
            'unit_ac_id'
        )->withTimestamps();
    }

    /* ===================== ACCESSORS ===================== */

    public function getHasPendingRescheduleAttribute(): bool
    {
        return !is_null($this->client_requested_date) && is_null($this->client_response);
    }

    /* ===================== SCOPES ===================== */

    public function scopeForTechnician($q, int $userId)
    {
        return $q->where('assigned_user_id', $userId);
    }

    public function scopeActive($q)
    {
        return $q->whereNotIn('status', [
            self::ST_DONE, self::ST_FINAL, self::ST_CANCEL_CLIENT, self::ST_CANCEL_ADMIN
        ]);
    }

    public function scopeBetweenDateRange($q, Carbon $from, Carbon $to)
    {
        return $q->whereBetween('scheduled_at', [
            $from->copy()->startOfDay(), $to->copy()->endOfDay()
        ]);
    }

    /* ===================== BUSINESS RULE ===================== */

    public function canStart(int $lateHours = 8, ?Carbon $now = null): bool
    {
        if (!$this->scheduled_at) return false;

        if (in_array($this->status, [self::ST_RUN, self::ST_DONE, self::ST_FINAL])) {
            return false;
        }

        if ($this->report && $this->report->started_at) {
            return false;
        }

        $now   = $now ? $now->copy() : now('Asia/Jakarta');
        $sched = $this->scheduled_at->copy()->timezone('Asia/Jakarta');

        return $now->greaterThanOrEqualTo($sched)
            && $now->lt($sched->copy()->addHours($lateHours));
    }
}
