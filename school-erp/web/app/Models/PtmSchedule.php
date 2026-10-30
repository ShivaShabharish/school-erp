<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PtmSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'school_class_id',
        'section_id',
        'meeting_date',
        'start_time',
        'end_time',
        'venue',
        'agenda',
        'status',
        'created_by',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
