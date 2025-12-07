<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Payment Model - platba
 *
 * @property float $payment_id
 * @property float $order_id
 * @property string $provider
 * @property string $status
 * @property float $amount
 * @property string $reference
 */
class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    protected $keyType = 'float';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'payment_id',
        'order_id',
        'provider',
        'status',
        'amount',
        'reference',
    ];

    protected $casts = [
        'payment_id' => 'float',
        'order_id' => 'float',
        'amount' => 'float',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    /**
     * Relácia: Platba patrí objednávke
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Generuje nové ID
     */
    public static function generateNewId(): float
    {
        $maxId = self::max('payment_id') ?? 0;
        return $maxId + 1.00;
    }
}

