<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'user_id',
        'employee_code',
        'designation',
        'department',
        'qualification',
        'gender',
        'dob',
        'blood_group',
        'joining_date',
        'basic_salary',
        'bank_account_no',
        'bank_name',
        'bank_ifsc',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'joining_date' => 'date',
            'basic_salary' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StaffAttendance::class);
    }

    public function leaveQuotas(): HasMany
    {
        return $this->hasMany(StaffLeaveQuota::class);
    }

    public function leaveApplications(): HasMany
    {
        return $this->hasMany(LeaveApplication::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->user?->name ?? 'Staff Member';
    }
}
