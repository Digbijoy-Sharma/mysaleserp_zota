<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSupplierOverride extends Model
{
    protected $table = 'store_supplier_overrides';
    protected $guarded = ['id'];

    public function centralSupplier()
    {
        return $this->belongsTo(CentralSupplier::class, 'central_supplier_id');
    }

    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }
}
