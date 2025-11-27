<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicianProfile extends Model
{
    protected $fillable = [
        'user_id', 'team_name', 'phone',
        'member_1_name','member_2_name',
        'status','is_active','address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsActive()
{
    $this->update([
        'status' => 'aktif',
        'is_busy' => false
    ]);
}


    public function markAsBusy()
{
    $this->update([
        'is_busy' => true
    ]);
}


    public function markAsOnLeave(): void
    {
        $this->update(['status' => 'cuti']);
    }

    public function getAutoStatusAttribute(): string
    {
        if (! $this->is_active) {
            return 'nonaktif';
        }

        return $this->status ?? 'aktif';
    }
}
