<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * OrderItem Model - položka objednávky
 *
 * @property float $order_item_id
 * @property float $order_id
 * @property float $variant_id
 * @property int $qty
 * @property float $unit_price
 * @property float $line_total
 */
class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'order_item_id';
    protected $keyType = 'float';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'order_item_id',
        'order_id',
        'variant_id',
        'qty',
        'unit_price',
        'line_total',
    ];

    protected $casts = [
        'order_item_id' => 'float',
        'order_id' => 'float',
        'variant_id' => 'float',
        'qty' => 'integer',
        'unit_price' => 'float',
        'line_total' => 'float',
    ];

    /**
     * Relácia: Položka patrí objednávke
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Relácia: Položka odkazuje na variant
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'variant_id');
    }

    /**
     * Generuje nové ID
     */
    public static function generateNewId(): float
    {
        $maxId = self::max('order_item_id') ?? 0;
        return $maxId + 1.00;
    }

    /**
     * Prepočíta line_total
     */
    public function calculateLineTotal(): float
    {
        return $this->qty * $this->unit_price;
    }
}

