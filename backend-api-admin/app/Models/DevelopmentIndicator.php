<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DevelopmentIndicator extends Model
{
    protected $fillable = ['program_id', 'name', 'order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(DevelopmentProgram::class, 'program_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'indicator_id');
    }
}
