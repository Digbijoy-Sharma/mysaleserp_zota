<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreProductOverride extends Model
{
    protected $table = 'store_product_overrides';
    protected $guarded = ['id'];

    public function centralProduct()
    {
        return $this->belongsTo(CentralProduct::class, 'central_product_id');
    }

    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }
}
