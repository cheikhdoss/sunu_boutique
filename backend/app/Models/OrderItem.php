<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'unit_price',
        'quantity',
        'total_price',
        'product_attributes',
    ];

    protected $casts = [
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

    /**
     * Relation avec la commande
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relation avec le produit
     */
    public function product()
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