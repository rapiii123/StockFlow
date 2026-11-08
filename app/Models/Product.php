<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sku',
        'barcode',
        'name',
        'unit',
        'purchase_price',
        'selling_price',
        'stock',
        'min_stock',
        'location',
        'image',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'stock' => 'integer',
            'min_stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(StockTransactionItem::class);
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    public function getStockStatusAttribute(): array
    {
        if ($this->stock <= 0) {
            return ['label' => 'Habis', 'class' => 'danger', 'icon' => 'bi-x-circle'];
        }
        if ($this->stock <= $this->min_stock) {
            return ['label' => 'Menipis', 'class' => 'warning', 'icon' => 'bi-exclamation-triangle'];
        }
        return ['label' => 'Aman', 'class' => 'success', 'icon' => 'bi-check-circle'];
    }
}
