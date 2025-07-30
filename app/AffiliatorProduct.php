<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliatorProduct extends Model
{
    protected $guarded = ['id'];

    protected $table = "affiliator_product";

    public function product()
{
    return $this->belongsTo(Product::class);
}
}
