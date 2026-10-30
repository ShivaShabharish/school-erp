<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_no',
        'student_id',
        'academic_year_id',
        'amount_paid',
        'concession_applied',
        'payment_method',
        'transaction_reference',
        'receipt_date',
        'collected_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
            'concession_applied' => 'decimal:2',
            'receipt_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FeeReceiptItem::class);
    }
}
