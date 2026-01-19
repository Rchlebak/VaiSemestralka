<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Model pre kategórie produktov
 * Fáza 4 - Kategórie
 */
class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    /**
     * Vzťah k produktom
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    /**
     * Scope pre aktívne kategórie
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Automatické generovanie slug pri vytváraní
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
