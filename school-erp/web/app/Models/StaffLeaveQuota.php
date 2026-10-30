<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffLeaveQuota extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'leave_type_id',
        'academic_year_id',
        'allocated_days',
        'used_days',
    ];

    protected function casts(): array
    {
        return [
            'allocated_days' => 'decimal:1',
            'used_days' => 'decimal:1',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function getRemainingDaysAttribute(): float
    {
        return max(0, (float) $this->allocated_days - (float) $this->used_days);
    }
}
