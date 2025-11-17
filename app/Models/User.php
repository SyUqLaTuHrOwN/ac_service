<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Support\Role;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name','email','password','phone','role'
    ];

    protected $hidden = [
        'password','remember_token'
    ];

    public function isAdmin(): bool
    {
        return $this->role === Role::ADMIN;
    }

    public function isTeknisi(): bool
    {
        return $this->role === Role::TEKNISI;
    }

    public function isClient(): bool
    {
        return $this->role === Role::CLIENT;
    }

    /* ===========================
       RELATION
    ============================ */

    public function clientProfile()
    {
        return $this->hasOne(Client::class, 'user_id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'user_id');
    }

    public function technicianProfile()
    {
        return $this->hasOne(TechnicianProfile::class, 'user_id');
    }

    public function leaves()
    {
        return $this->hasMany(TechnicianLeave::class, 'user_id');
    }

    public function approvedLeaves()
    {
        return $this->leaves()->approved();
    }

    /* ===========================
       LOGIC: CEK CUTI HARI INI / TGL TERTENTU
    ============================ */

    public function isOnLeave($date = null): bool
    {
        $d = $date
            ? Carbon::parse($date)->toDateString()
            : now('Asia/Jakarta')->toDateString();

        return $this->approvedLeaves()
            ->whereDate('start_date', '<=', $d)
            ->whereDate('end_date', '>=', $d)
            ->exists();
    }

    /* ===========================
       ACCESSOR: STATUS LIVE
       Digunakan di tabel teknisi
    ============================ */

    public function getLeaveStatusAttribute(): string
    {
        if ($this->isOnLeave()) {
            // jika ingin mendeteksi jenis cuti (izin/sakit/cuti), bisa tambahkan di sini
            return 'cuti';
        }

        $active = optional($this->technicianProfile)->is_active;

        return $active === 0 ? 'nonaktif' : 'aktif';
    }
}