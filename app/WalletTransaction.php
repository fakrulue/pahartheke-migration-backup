<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'affiliator_wallet_id',
        'amount',
        'type',
        'description',
    ];

    public function wallet()
    {
        return $this->belongsTo(AffiliatorWallet::class, 'affiliator_wallet_id');
    }
    public function getTypeAttribute($value)
    {
        return $value === 'credit' ? 'Credit' : 'Debit';
    }
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }
 
}
