<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'department',
    ];

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    public function examSchedules(): HasMany
    {
        return $this->hasMany(ExamSchedule::class);
    }

    public function homework(): HasMany
    {
        return $this->hasMany(Homework::class);
    }
}
