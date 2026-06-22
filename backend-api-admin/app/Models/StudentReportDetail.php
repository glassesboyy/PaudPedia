<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentReportDetail extends Model
{
    protected $fillable = ['student_report_id', 'program_id', 'narrative'];

    public function report(): BelongsTo
    {
        return $this->belongsTo(StudentReport::class, 'student_report_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(DevelopmentProgram::class, 'program_id');
    }
}
