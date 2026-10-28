<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $fillable = [
        'tandon_id',
        'type',
        'message',
        'triggered_at',
        'resolved_at',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function tandon(): BelongsTo
    {
        return $this->belongsTo(Tandon::class);
    }

    /** Stored as Asia/Jakarta wall-clock while the app timezone is UTC; re-label to get the real instant. */
    public function triggeredInstant(): Carbon
    {
        return Carbon::parse($this->triggered_at->format('Y-m-d H:i:s'), 'Asia/Jakarta');
    }
}
