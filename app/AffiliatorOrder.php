<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliatorOrder extends Model
{
    protected $fillable = ['affiliator_id', 'order_id', 'commission_amount', 'status'];

    public function affiliator()
    {
        return $this->belongsTo(Affiliator::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
