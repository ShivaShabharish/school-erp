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
        'address',
        'city',
        'state',
        'pincode',
        'father_name',
        'mother_name',
        'father_occupation',
        'mother_occupation',
        'parent_phone',
        'parent_email',
        'previous_school',
        'tc_number',
        'documents',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'admission_date' => 'date',
            'documents' => 'array',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getAdmissionNumberAttribute(): string
    {
        return $this->admission_no ?? '';
    }

    public function getDateOfBirthAttribute(): ?string
    {
        return $this->dob?->toDateString();
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

    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function feeReceipts(): HasMany
    {
        return $this->hasMany(FeeReceipt::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(StudentPromotion::class);
    }
}
