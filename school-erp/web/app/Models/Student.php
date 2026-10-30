<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admission_no',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'school_class_id',
        'section_id',
        'parent_id',
        'roll_number',
        'admission_date',
        'status',
        'blood_group',
        'emergency_contact',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'admission_date' => 'date',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function feeReceipts(): HasMany
    {
        return $this->hasMany(FeeReceipt::class);
    }
}
