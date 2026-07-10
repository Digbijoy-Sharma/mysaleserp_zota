<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Dava India — Phase 2
 *
 * CentralProduct is the global product master managed by Super Admin.
 * Every store can transact against it; per-store stock is tracked
 * through variation_location_details (existing).
 */
class CentralProduct extends Model
{
    use SoftDeletes;

    protected $table = 'central_products';
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_banned' => 'boolean',
        'is_discontinued' => 'boolean',
        'enable_stock' => 'boolean',
        'prescription_required' => 'boolean',
    ];

    public function variations()
    {
        return $this->hasMany(CentralProductVariation::class, 'central_product_id');
    }

    public function stores()
    {
        return $this->belongsToMany(\App\Business::class, 'central_product_store', 'central_product_id', 'business_id')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function overrides()
    {
        return $this->hasMany(StoreProductOverride::class, 'central_product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeForStore($query, $business_id)
    {
        return $query->whereHas('stores', function ($q) use ($business_id) {
            $q->where('business_id', $business_id)->where('is_active', 1);
        });
    }

    public function getOverrideFor($business_id)
    {
        return $this->overrides()->where('business_id', $business_id)->first();
    }

    /**
     * Effective sell price for a given store (override > default).
     */
    public function effectiveSellPrice($business_id = null)
    {
        if ($business_id) {
            $override = $this->getOverrideFor($business_id);
            if ($override && $override->sell_price !== null) {
                return (float) $override->sell_price;
            }
        }
        return (float) $this->default_sell_price;
    }

    /**
     * Effective alert quantity.
     */
    public function effectiveAlertQuantity($business_id = null)
    {
        if ($business_id) {
            $override = $this->getOverrideFor($business_id);
            if ($override && $override->alert_quantity !== null) {
                return (float) $override->alert_quantity;
            }
        }
        return (float) $this->default_alert_quantity;
    }
}
