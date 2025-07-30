<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliatorWallet extends Model
{
    protected $guarded = ['id'];

    public function affiliator()
    {
        return $this->belongsTo(Affiliator::class);
    }
    // public function transactions()
    // {
    //     return $this->hasMany(AffiliatorWalletTransaction::class);
    // }
    // public function getBalanceAttribute()
    // {
    //     return $this->transactions()->sum('amount');
    // }
    // public function getTotalDepositAttribute()
    // {
    //     return $this->transactions()->where('type', 'deposit')->sum('amount');
    // }
    // public function getTotalWithdrawAttribute()
    // {
    //     return $this->transactions()->where('type', 'withdraw')->sum('amount');
    // }
    // public function getTotalTransferAttribute()
    // {
    //     return $this->transactions()->where('type', 'transfer')->sum('amount');
    // }
    // public function getTotalCommissionAttribute()
    // {
    //     return $this->transactions()->where('type', 'commission')->sum('amount');
    // }
    // public function getTotalAffiliateBonusAttribute()
    // {
    //     return $this->transactions()->where('type', 'affiliate_bonus')->sum('amount');
    // }
    // public function getTotalAffiliateWithdrawAttribute()
    // {
    //     return $this->transactions()->where('type', 'affiliate_withdraw')->sum('amount');
    // }
  
}
