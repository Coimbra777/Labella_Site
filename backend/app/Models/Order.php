<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_SHIPPED = 'shipped';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_STATUS_PENDING = 'pending';

    public const PAYMENT_STATUS_PAID = 'paid';

    public const PAYMENT_STATUS_FAILED = 'failed';

    public const PAYMENT_STATUS_REFUNDED = 'refunded';

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Solicitação recebida',
        self::STATUS_PROCESSING => 'Em atendimento',
        self::STATUS_SHIPPED => 'Enviado / retirada',
        self::STATUS_DELIVERED => 'Concluído',
        self::STATUS_CANCELLED => 'Cancelado',
    ];

    public const PAYMENT_STATUS_LABELS = [
        self::PAYMENT_STATUS_PENDING => 'A definir',
        self::PAYMENT_STATUS_PAID => 'Pago',
        self::PAYMENT_STATUS_FAILED => 'Falhou',
        self::PAYMENT_STATUS_REFUNDED => 'Reembolsado',
    ];

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country',
        'subtotal',
        'shipping_cost',
        'discount',
        'total',
        'status',
        'payment_status',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(10));
            }
        });

        static::deleting(function (Order $order) {
            if (!$order->canBeDeleted()) {
                throw ValidationException::withMessages([
                    'order' => ['Somente solicitações pendentes ou canceladas podem ser excluídas.'],
                ]);
            }
        });
    }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include processing orders.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    /**
     * Scope a query to only include completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    public static function statusOptions(): array
    {
        return self::STATUS_LABELS;
    }

    public static function paymentStatusOptions(): array
    {
        return self::PAYMENT_STATUS_LABELS;
    }

    public static function statusColor(string $status): string
    {
        return match ($status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_SHIPPED => 'info',
            self::STATUS_DELIVERED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'gray',
        };
    }

    public static function paymentStatusColor(string $status): string
    {
        return match ($status) {
            self::PAYMENT_STATUS_PENDING => 'warning',
            self::PAYMENT_STATUS_PAID => 'success',
            self::PAYMENT_STATUS_FAILED => 'danger',
            self::PAYMENT_STATUS_REFUNDED => 'info',
            default => 'gray',
        };
    }

    public function canBeDeleted(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CANCELLED], true);
    }

    public function reservesInventory(?string $status = null): bool
    {
        return in_array($status ?? $this->status, [
            self::STATUS_PROCESSING,
            self::STATUS_SHIPPED,
            self::STATUS_DELIVERED,
        ], true);
    }
}
