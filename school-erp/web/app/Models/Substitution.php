<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Substitution extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'timetable_id',
        'original_staff_id',
        'substitute_staff_id',
        'status',
        'remarks',
        'assigned_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

    public function originalTeacher(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'original_staff_id');
    }

    public function substituteTeacher(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'substitute_staff_id');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
