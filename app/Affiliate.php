<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Affiliate extends Model
{
    protected $fillable = ['user_id', 'affiliate_code', 'clicks', 'sales', 'commission'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
