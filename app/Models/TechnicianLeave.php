<?php
// app/Models/TechnicianLeave.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class TechnicianLeave extends Model
{
    protected $table = 'technician_leaves';

    protected $fillable = [
        'user_id','start_date','end_date','reason',
        'proof_path','status','decided_at','decided_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'decided_at' => 'datetime',
    ];

    public function user(): BelongsTo   { return $this->belongsTo(User::class); }
    public function decider(): BelongsTo { return $this->belongsTo(User::class, 'decided_by'); }

    /** scope: hanya yang disetujui */
    public function scopeApproved($q) { return $q->where('status','approved'); }

    /** scope: tanggal t masuk di rentang cuti */
    public function scopeOverlaps($q, $date)
    {
        $d = $date instanceof Carbon ? $date : Carbon::parse($date);
        return $q->whereDate('start_date','<=',$d->toDateString())
                 ->whereDate('end_date','>=',$d->toDateString());
    }
}
