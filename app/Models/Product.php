<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'price',
        'tax_percentage',
        'stock_on_hand',
    ];

    /**
     * The attributes that should be cast.
     *
     * Decimal columns are kept as fixed-precision strings so money
     * calculations never pass through floating point.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'stock_on_hand' => 'integer',
    ];

    /**
     * Order lines that reference this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Limit the query to products at or below the given stock level.
     */
    public function scopeLowStock(Builder $query, int $threshold): Builder
    {
        return $query->where('stock_on_hand', '<=', $threshold);
    }

    /**
     * Limit the query to products whose name or code matches the term.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term === null || $term === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('code', 'like', "%{$term}%");
        });
    }

    /**
     * Limit the query to a stock status.
     *
     * out_of_stock = 0, low_stock = 1..threshold, in_stock = above threshold.
     */
    public function scopeStockStatus(Builder $query, ?string $status, int $threshold): Builder
    {
        switch ($status) {
            case 'out_of_stock':
                return $query->where('stock_on_hand', 0);
            case 'low_stock':
                return $query->whereBetween('stock_on_hand', [1, $threshold]);
            case 'in_stock':
                return $query->where('stock_on_hand', '>', $threshold);
            default:
                return $query;
        }
    }
}
