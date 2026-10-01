<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'number', 'issued_at', 'due_at', 'status', 'currency'];

    protected $casts = [
        'issued_at' => 'date',
        'due_at' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Total en centimes
    public function getTotalAttribute(): int
    {
        return $this->items->sum(fn ($item) => $item->quantity * $item->unit_price);
    }
}