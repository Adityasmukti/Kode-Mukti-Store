<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'invoice_token',
        'name',
        'email',
        'whatsapp',
        'amount',
        'status',
        'reject_reason',
        'verified_at',
        'verified_by',
    ];

    public function paymentProofs(): HasMany
    {
        return $this->hasMany(PaymentProof::class);
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }
}
