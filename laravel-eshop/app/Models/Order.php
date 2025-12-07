<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Order Model - objednávka
 *
 * @property float $order_id
 * @property float|null $user_id
 * @property string $email
 * @property string $status
 * @property float $total_amount
 * @property string $ship_name
 * @property string $ship_street
 * @property string $ship_city
 * @property string $ship_zip
 * @property string $ship_country
 */
class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    protected $keyType = 'float';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'user_id',
        'email',
        'status',
        'total_amount',
        'ship_name',
        'ship_street',
        'ship_city',
        'ship_zip',
        'ship_country',
    ];

    protected $casts = [
        'order_id' => 'float',
        'user_id' => 'float',
        'total_amount' => 'float',
    ];

    /**
     * Statusy objednávky
     */
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Relácia: Objednávka má mnoho položiek
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Relácia: Objednávka má platbu
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id');
    }

    /**
     * Relácia: Objednávka patrí používateľovi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Generuje nové ID
     */
    public static function generateNewId(): float
    {
        $maxId = self::max('order_id') ?? 0;
        return $maxId + 1.00;
    }

    /**
     * Prepočíta celkovú sumu
     */
    public function calculateTotal(): float
    {
        return $this->items()->sum('line_total');
    }
}

