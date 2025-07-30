<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Affiliator extends Model
{
    protected $guarded = ['id'];


    public function socialLinks()
    {
        return $this->hasMany(AfiliatorSocialLink::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function wallet()
    {
        return $this->hasOne(AffiliatorWallet::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('has_variant','variant_name','commission', 'discount')
            ->withTimestamps();
    }

    public function affiliateOrders()
    {
        return $this->hasMany(AffiliatorOrder::class);
    }
}
