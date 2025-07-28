<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_description',
        'product_sku',
        'product_image',
        'quantity',
        'unit_price',
        'total_price',
        'product_attributes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'product_attributes' => 'array',
    ];

    /**
     * Boot du modèle pour calculer automatiquement le prix total
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($orderItem) {
            $orderItem->total_price = $orderItem->unit_price * $orderItem->quantity;
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Obtenir le prix total formaté
     */
    public function getFormattedTotalPriceAttribute()
    {
        return number_format($this->total_price, 0, ',', ' ') . ' XOF';
    }

    /**
     * Obtenir le prix unitaire formaté
     */
    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 0, ',', ' ') . ' XOF';
    }

    /**
     * Vérifier si le produit a des attributs spéciaux
     */
    public function hasAttributes()
    {
        return !empty($this->product_attributes);
    }

    /**
     * Obtenir un attribut spécifique du produit
     */
    public function getAttribute($key)
    {
        return $this->product_attributes[$key] ?? null;
    }
}