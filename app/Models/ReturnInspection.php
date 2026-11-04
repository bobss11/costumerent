<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnInspection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'rental_id',
        'checked_by_user_id',
        'actual_return_date',
        'late_days',
        'late_fee_amount',
        'condition_status',
        'inspection_notes',
        'damage_penalty_fee',
        'total_deduction',
        'deposit_refunded',
        'extra_charge',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'actual_return_date' => 'date',
            'late_days' => 'integer',
            'late_fee_amount' => 'decimal:2',
            'damage_penalty_fee' => 'decimal:2',
            'total_deduction' => 'decimal:2',
            'deposit_refunded' => 'decimal:2',
            'extra_charge' => 'decimal:2',
        ];
    }

    /**
     * The rental this inspection belongs to.
     */
    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    /**
     * The staff/admin user who performed the inspection.
     */
    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by_user_id');
    }
}
