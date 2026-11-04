<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Costume extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'code',
        'name',
        'size',
        'gender',
        'rental_price_per_day',
        'deposit_amount',
        'late_fee_per_day',
        'stock_total',
        'stock_available',
        'accessories_included',
        'image_url',
        'description',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rental_price_per_day' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'late_fee_per_day' => 'decimal:2',
            'stock_total' => 'integer',
            'stock_available' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * The category this costume belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The rentals for this costume.
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }
}
