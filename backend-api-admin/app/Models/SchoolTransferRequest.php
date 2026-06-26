<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolTransferRequest extends Model
{
    protected $fillable = [
        'school_id',
        'from_user_id',
        'to_email',
        'token',
        'status',
        'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }
}
