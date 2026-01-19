<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Product Model - reprezentuje produkt v e-shope
 *
 * @property float $product_id
 * @property string $sku_model
 * @property string $name
 * @property string $brand
 * @property string $gender
 * @property float $base_price
 * @property string $description
 * @property int $is_active
 * @property int $category_id
 */
class Product extends Model
{
    /**
     * Názov tabuľky v databáze
     */
    protected $table = 'products';

    /**
     * Primárny kľúč
     */
    protected $primaryKey = 'product_id';

    /**
     * Typ primárneho kľúča (decimal)
     */
    protected $keyType = 'float';

    /**
     * Vypnutie auto-incrementu
     */
    public $incrementing = false;

    /**
     * Vypnutie timestamps (tabuľka nemá created_at, updated_at)
     */
    public $timestamps = false;

    /**
     * Atribúty, ktoré môžu byť hromadne priradené
     */
    protected $fillable = [
        'product_id',
        'sku_model',
        'name',
        'brand',
        'gender',
        'base_price',
        'description',
        'is_active',
        'category_id',
    ];

    /**
     * Castovanie atribútov na správne typy
     */
    protected $casts = [
        'product_id' => 'float',
        'base_price' => 'float',
        'is_active' => 'integer',
        'category_id' => 'integer',
    ];

    /**
     * Relácia: Produkt patrí do kategórie
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Relácia: Produkt má mnoho variantov
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'product_id');
    }

    /**
     * Relácia: Produkt má mnoho obrázkov
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }

    /**
     * Scope: Len aktívne produkty
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope: Vyhľadávanie podľa textu
     */
    public function scopeSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
                ->orWhere('brand', 'LIKE', "%{$term}%")
                ->orWhere('sku_model', 'LIKE', "%{$term}%")
                ->orWhere('description', 'LIKE', "%{$term}%");
        });
    }

    /**
     * Scope: Filter podľa ceny
     */
    public function scopePriceRange($query, $min = null, $max = null)
    {
        if ($min !== null) {
            $query->where('base_price', '>=', $min);
        }
        if ($max !== null) {
            $query->where('base_price', '<=', $max);
        }
        return $query;
    }

    /**
     * Získa hlavný obrázok produktu
     */
    public function getMainImageAttribute(): ?string
    {
        $mainImage = $this->images()->where('is_main', 1)->first();
        if ($mainImage) {
            return $mainImage->image_path;
        }

        $firstImage = $this->images()->orderBy('sort_order')->first();
        if ($firstImage) {
            return $firstImage->image_path;
        }

        return null;
    }

    /**
     * Získa všetky dostupné veľkosti
     */
    public function getAvailableSizesAttribute(): array
    {
        return $this->variants()
            ->where('is_active', 1)
            ->pluck('size_eu')
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Získa všetky dostupné farby
     */
    public function getAvailableColorsAttribute(): array
    {
        return $this->variants()
            ->where('is_active', 1)
            ->pluck('color')
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Generuje nové ID pre produkt
     */
    public static function generateNewId(): float
    {
        $maxId = self::max('product_id') ?? 0;
        return $maxId + 1.00;
    }
}

