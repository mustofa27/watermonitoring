<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TandonReading extends Model
{
    protected $fillable = [
        'tandon_id',
        'water_height',
        'water_volume',
        'recorded_at',
    ];

    protected $casts = [
        'water_height' => 'decimal:3',
        'water_volume' => 'decimal:3',
        'recorded_at' => 'datetime',
    ];

    public function tandon(): BelongsTo
    {
        return $this->belongsTo(Tandon::class);
    }

    /** Readings are stored as Asia/Jakarta wall-clock but the app timezone is UTC, so re-label to get the real instant. */
    public function recordedInstant(): Carbon
    {
        return Carbon::parse($this->recorded_at->format('Y-m-d H:i:s'), 'Asia/Jakarta');
    }
}
