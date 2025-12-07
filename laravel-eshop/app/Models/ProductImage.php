<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ProductImage Model - obrázky produktu
 *
 * @property int $image_id
 * @property float $product_id
 * @property string $image_path
 * @property int $is_main
 * @property int $sort_order
 */
class ProductImage extends Model
{
    protected $table = 'product_images';
    protected $primaryKey = 'image_id';
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'image_path',
        'is_main',
        'sort_order',
    ];

    protected $casts = [
        'product_id' => 'float',
        'is_main' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Relácia: Obrázok patrí produktu
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Scope: Len hlavné obrázky
     */
    public function scopeMain($query)
    {
        return $query->where('is_main', 1);
    }
}

