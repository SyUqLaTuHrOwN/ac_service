<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechnicianLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','start_date','end_date','reason','proof_path',
        'status','decided_at','decided_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'decided_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'decided_by'); }

    /* Scopes */
    public function scopeApproved($q) { return $q->where('status','approved'); }
    public function scopeOverlaps($q, $date) {
        $d = \Illuminate\Support\Carbon::parse($date)->toDateString();
        return $q->whereDate('start_date','<=',$d)->whereDate('end_date','>=',$d);
    }
}
