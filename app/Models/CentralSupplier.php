<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CentralSupplier extends Model
{
    use SoftDeletes;

    protected $table = 'central_suppliers';
    protected $guarded = ['id'];

    public function stores()
    {
        return $this->belongsToMany(\App\Business::class, 'central_supplier_store', 'central_supplier_id', 'business_id')
            ->withPivot('is_active')->withTimestamps();
    }

    public function overrides()
    {
        return $this->hasMany(StoreSupplierOverride::class, 'central_supplier_id');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', 1);
    }
}
