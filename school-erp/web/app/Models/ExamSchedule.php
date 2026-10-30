<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'school_class_id',
        'subject_id',
        'exam_date',
        'start_time',
        'end_time',
        'max_marks',
        'pass_marks',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }
}
