<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tandon extends Model
{
    protected $fillable = [
        'name',
        'type',
        'building_name',
        'parent_id',
        'cross_section_area',
        'height_max',
        'height_min',
        'height_warning',
    ];

    protected $casts = [
        'cross_section_area' => 'decimal:4',
        'height_max' => 'decimal:3',
        'height_min' => 'decimal:3',
        'height_warning' => 'decimal:3',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Tandon::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Tandon::class, 'parent_id');
    }

    public function readings(): HasMany
    {
        return $this->hasMany(TandonReading::class);
    }
}
