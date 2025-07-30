<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $table = 'withdraws';
    protected $fillable = ['affiliator_id', 'amount', 'status'];

    public function affiliator()
    {
        return $this->belongsTo(Affiliator::class, 'affiliator_id', 'id');
    }
}
