<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'month',
        'payslip_number',
        'basic_salary',
        'da_amount',
        'hra_amount',
        'special_allowance',
        'gross_salary',
        'pf_deduction',
        'tax_deduction',
        'total_deductions',
        'net_salary',
        'working_days',
        'present_days',
        'leave_days',
        'status',
        'payment_date',
        'payment_method',
    ];

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'da_amount' => 'decimal:2',
            'hra_amount' => 'decimal:2',
            'special_allowance' => 'decimal:2',
            'gross_salary' => 'decimal:2',
            'pf_deduction' => 'decimal:2',
            'tax_deduction' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
