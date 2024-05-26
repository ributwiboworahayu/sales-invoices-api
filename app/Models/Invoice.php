<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function details(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getStatusAttribute(): string
    {
        return match ($this->attributes['status']) {
            1 => 'Wait',
            2 => 'Approved',
            3 => 'Rejected',
            default => 'Expired',
        };
    }
}
