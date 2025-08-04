<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'quantity',
        'image',
        'images',
        'is_visible',
        'is_featured',
        'type',
    ];

    protected $appends = ['stock'];

    protected $casts = [
        'images' => 'array',
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Crée un attribut 'stock' pour l'API à partir de la colonne 'quantity'.
     */
    public function getStockAttribute(): int
    {
        return $this->quantity;
    }
}
