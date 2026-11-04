<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rental extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'rental_code',
        'user_id',
        'costume_id',
        'qty',
        'start_date',
        'end_date',
        'total_days',
        'rental_subtotal',
        'deposit_total',
        'grand_total',
        'guarantee_card_held',
        'pickup_condition_notes',
        'status',
        'payment_status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'qty' => 'integer',
            'total_days' => 'integer',
            'rental_subtotal' => 'decimal:2',
            'deposit_total' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    /**
     * The user who created this rental.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The costume being rented.
     */
    public function costume(): BelongsTo
    {
        return $this->belongsTo(Costume::class);
    }

    /**
     * The return inspection for this rental.
     */
    public function returnInspection(): HasOne
    {
        return $this->hasOne(ReturnInspection::class);
    }

    /**
     * The invoice for this rental.
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
