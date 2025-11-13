<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    protected $fillable = [
        'client_id',
        'location_id',
        'scheduled_at',
        'assigned_user_id',
        'technician_id',
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

    

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
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

    
    public function getHasPendingRescheduleAttribute(): bool
    {
        return !is_null($this->client_requested_date) && is_null($this->client_response);
    }
}
