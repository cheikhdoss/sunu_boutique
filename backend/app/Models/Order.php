<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
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
        'total',
        'status',
        'payment_method',
        'payment_status',
        'payment_date',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'notes',
        'admin_notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'payment_date' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
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

    /**
     * Générer un numéro de commande unique
     */
    public static function generateOrderNumber()
    {
        do {
            $orderNumber = 'CMD-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les articles de la commande
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
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
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Vérifier si la commande est livrée
     */
    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    /**
     * Vérifier si la commande est payée
     */
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Obtenir l'adresse de livraison complète
     */
    public function getShippingAddressAttribute()
    {
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
     * Obtenir l'adresse de facturation complète
     */
    public function getBillingAddressAttribute()
    {
        $address = $this->billing_address_line_1;
        
        if ($this->billing_address_line_2) {
            $address .= ', ' . $this->billing_address_line_2;
        }
        
        $address .= ', ' . $this->billing_city;
        
        if ($this->billing_state) {
            $address .= ', ' . $this->billing_state;
        }
        
        $address .= ' ' . $this->billing_postal_code;
        $address .= ', ' . $this->billing_country;
        
        return $address;
    }

    /**
     * Obtenir le nom complet de livraison
     */
    public function getShippingFullNameAttribute()
    {
        return $this->shipping_first_name . ' ' . $this->shipping_last_name;
    }

    /**
     * Obtenir le nom complet de facturation
     */
    public function getBillingFullNameAttribute()
    {
        return $this->billing_first_name . ' ' . $this->billing_last_name;
    }

    /**
     * Calculer le nombre total d'articles
     */
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }
}