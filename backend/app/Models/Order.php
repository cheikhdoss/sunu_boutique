<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'delivery_address_id',
        'status',
        'payment_status',
        'payment_method',
        'total',
        'notes',
        'invoice_url',
        // Champs additionnels pour compatibilité
        'shipping_first_name',
        'shipping_last_name',
        'shipping_company',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'shipping_phone',
        'billing_first_name',
        'billing_last_name',
        'billing_company',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',
        'billing_phone',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'payment_date',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'admin_notes',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot du modèle pour générer automatiquement le numéro de commande
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(DeliveryAddress::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $lastOrder = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastOrder ? (int)substr($lastOrder->order_number, -3) + 1 : 1;
        
        return 'CMD-' . $year . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Scope pour les commandes en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les commandes livrées
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope pour les commandes payées
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Vérifier si la commande peut être annulée
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['en_attente', 'pending', 'processing']);
    }

    /**
     * Vérifier si la commande est livrée
     */
    public function isDelivered()
    {
        return in_array($this->status, ['livree', 'delivered']);
    }

    /**
     * Vérifier si la commande est payée
     */
    public function isPaid()
    {
        return in_array($this->payment_status, ['paye', 'paid']);
    }

    /**
     * Obtenir l'adresse de livraison complète
     */
    public function getShippingAddressAttribute()
    {
        if ($this->deliveryAddress) {
            return $this->deliveryAddress->address . ', ' . 
                   $this->deliveryAddress->city . ' ' . 
                   $this->deliveryAddress->postal_code . ', ' . 
                   $this->deliveryAddress->country;
        }

        $address = $this->shipping_address_line_1;
        
        if ($this->shipping_address_line_2) {
            $address .= ', ' . $this->shipping_address_line_2;
        }
        
        $address .= ', ' . $this->shipping_city;
        
        if ($this->shipping_state) {
            $address .= ', ' . $this->shipping_state;
        }
        
        $address .= ' ' . $this->shipping_postal_code;
        $address .= ', ' . $this->shipping_country;
        
        return $address;
    }

    /**
     * Obtenir le nom complet de livraison
     */
    public function getShippingFullNameAttribute()
    {
        if ($this->deliveryAddress) {
            return $this->deliveryAddress->first_name . ' ' . $this->deliveryAddress->last_name;
        }
        
        return $this->shipping_first_name . ' ' . $this->shipping_last_name;
    }

    /**
     * Calculer le nombre total d'articles
     */
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }
}