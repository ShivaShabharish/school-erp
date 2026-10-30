<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Timetable extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_class_id',
        'section_id',
        'subject_id',
        'staff_id',
        'day_of_week',
        'period_number',
        'start_time',
        'end_time',
        'room_no',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function substitutions(): HasMany
    {
        return $this->hasMany(Substitution::class);
    }
}
