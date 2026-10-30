<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mark extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_schedule_id',
        'student_id',
        'marks_obtained',
        'grade',
        'grade_point',
        'is_absent',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'marks_obtained' => 'decimal:2',
            'grade_point' => 'decimal:1',
            'is_absent' => 'boolean',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ExamSchedule::class, 'exam_schedule_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
