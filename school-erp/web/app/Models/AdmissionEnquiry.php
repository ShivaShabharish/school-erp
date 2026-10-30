<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionEnquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'enquiry_no',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'school_class_id',
        'parent_name',
        'phone',
        'email',
        'address',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
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
}
