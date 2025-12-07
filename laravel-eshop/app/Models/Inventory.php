<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Inventory Model - reprezentuje stav zásob pre variant
 *
 * @property float $variant_id
 * @property int $stock_qty
 */
class Inventory extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'variant_id';
    protected $keyType = 'float';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'variant_id',
        'stock_qty',
    ];

    protected $casts = [
        'variant_id' => 'float',
        'stock_qty' => 'integer',
    ];

    /**
     * Relácia: Inventár patrí variantu
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'variant_id');
    }

    /**
     * Kontroluje či je na sklade
     */
    public function isInStock(): bool
    {
        return $this->stock_qty > 0;
    }

    /**
     * Zníži zásoby
     */
    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock_qty >= $quantity) {
            $this->stock_qty -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Zvýši zásoby
     */
    public function increaseStock(int $quantity): void
    {
        $this->stock_qty += $quantity;
        $this->save();
    }
}

