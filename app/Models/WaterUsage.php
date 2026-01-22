<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaterUsage extends Model
{
    protected $fillable = [
        'tandon_id',
        'usage_date',
        'volume_used',
    ];

    protected $casts = [
        'volume_used' => 'decimal:3',
        'usage_date' => 'date',
    ];

    public function tandon(): BelongsTo
    {
        return $this->belongsTo(Tandon::class);
    }
}
