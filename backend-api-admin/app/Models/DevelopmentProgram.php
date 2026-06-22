<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DevelopmentProgram extends Model
{
    protected $fillable = ['school_id', 'name', 'order'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(DevelopmentIndicator::class, 'program_id');
    }
}
