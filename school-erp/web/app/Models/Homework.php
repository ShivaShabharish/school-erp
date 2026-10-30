<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Homework extends Model
{
    use HasFactory;

    protected $table = 'homework';

    protected $fillable = [
        'school_class_id',
        'section_id',
        'subject_id',
        'staff_id',
        'title',
        'description',
        'assigned_date',
        'due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'assigned_date' => 'date',
            'due_date' => 'date',
        ];
    }

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
}
