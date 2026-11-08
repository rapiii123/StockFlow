<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'type',
        'transaction_date',
        'supplier_id',
        'recipient',
        'user_id',
        'total_items',
        'total_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'total_items' => 'integer',
            'total_amount' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockTransactionItem::class);
    }

    public function scopeIn($query)
    {
        return $query->where('type', 'in');
    }

    public function scopeOut($query)
    {
        return $query->where('type', 'out');
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'in' ? 'Barang Masuk' : 'Barang Keluar';
    }

    public function getTypeBadgeAttribute(): string
    {
        return $this->type === 'in' ? 'success' : 'danger';
    }

    public function getTypeIconAttribute(): string
    {
        return $this->type === 'in' ? 'bi-box-arrow-in-down' : 'bi-box-arrow-up-right';
    }
}
