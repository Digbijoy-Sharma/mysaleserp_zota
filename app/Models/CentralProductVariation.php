<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentralProductVariation extends Model
{
    protected $table = 'central_product_variations';
    protected $guarded = ['id'];

    public function centralProduct()
    {
        return $this->belongsTo(CentralProduct::class, 'central_product_id');
    }
}
