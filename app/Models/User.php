<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Support\Role;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password','phone','role'];
    protected $hidden   = ['password','remember_token'];

    public function isAdmin(): bool   { return $this->role === Role::ADMIN; }
    public function isTeknisi(): bool { return $this->role === Role::TEKNISI; }
    public function isClient(): bool  { return $this->role === Role::CLIENT; }

    // Relasi ke Client (user_id pada tabel clients)
    public function clientProfile()
    {
        return $this->hasOne(Client::class, 'user_id');
    }
    public function technicianProfile() {
    return $this->hasOne(\App\Models\TechnicianProfile::class);
}

    // Alias agar bisa dipakai whereDoesntHave('client')
    public function client()
    {
        return $this->hasOne(Client::class, 'user_id');
    }
    public function leaves() {
    return $this->hasMany(\App\Models\TechnicianLeave::class);
}

public function onLeave($date = null): bool
{
    $date = $date ? \Illuminate\Support\Carbon::parse($date) : now('Asia/Jakarta');
    return $this->leaves()->approved()
        ->whereDate('start_date','<=',$date->toDateString())
        ->whereDate('end_date','>=',$date->toDateString())
        ->exists();
}
}
