<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * ProductVariant Model - reprezentuje variant produktu (farba + veľkosť)
 *
 * @property float $variant_id
 * @property float $product_id
 * @property string $sku
 * @property string $color
 * @property string $size_eu
 * @property int $is_active
 */
class ProductVariant extends Model
{
    protected $table = 'product_variants';
    protected $primaryKey = 'variant_id';
    protected $keyType = 'float';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'variant_id',
        'product_id',
        'sku',
        'color',
        'size_eu',
        'is_active',
    ];

    protected $casts = [
        'variant_id' => 'float',
        'product_id' => 'float',
        'is_active' => 'integer',
    ];

    /**
     * Relácia: Variant patrí produktu
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Relácia: Variant má jeden záznam v inventári
     */
    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class, 'variant_id', 'variant_id');
    }

    /**
     * Získa množstvo na sklade
     */
    public function getStockQtyAttribute(): int
    {
        return $this->inventory?->stock_qty ?? 0;
    }

    /**
     * Scope: Len aktívne varianty
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope: Varianty na sklade
     */
    public function scopeInStock($query)
    {
        return $query->whereHas('inventory', function ($q) {
            $q->where('stock_qty', '>', 0);
        });
    }

    /**
     * Generuje nové ID pre variant
     */
    public static function generateNewId(): float
    {
        $maxId = self::max('variant_id') ?? 0;
        return $maxId + 1.00;
    }

    /**
     * Generuje SKU automaticky
     */
    public static function generateSku(Product $product, string $color, string $size): string
    {
        $prefix = strtoupper(substr($product->brand ?? 'XX', 0, 2));
        $colorCode = strtoupper(substr($color, 0, 3));
        return "{$prefix}-{$product->product_id}-{$colorCode}-{$size}";
    }
}

