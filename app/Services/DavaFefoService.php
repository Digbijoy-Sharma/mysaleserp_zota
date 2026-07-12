<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Dava India — Phase 2
 *
 * FEFO (First-Expiry-First-Out) helper for pharmacy batch picking.
 *
 * For a given (business_id, location_id, variation_id) and a desired
 * quantity, returns the purchase_line batches in expiry-ascending order
 * so the POS can deduct from the earliest-expiring batch first.
 *
 * The actual stock deduction still happens through the existing
 * `transaction_sell_lines_purchase_lines` table — this service only
 * provides the picking order.
 */
class DavaFefoService
{
    /**
     * Return available batches for a variation, ordered by expiry ASC.
     *
     * @param int $business_id
     * @param int $location_id
     * @param int $variation_id
     * @param int|null $limit  optional limit on number of batches returned
     * @return \Illuminate\Support\Collection of {purchase_line_id, lot_number, exp_date, qty_available}
     */
    public function availableBatches(int $business_id, int $location_id, int $variation_id, int $limit = null)
    {
        $query = DB::table('purchase_lines as pl')
            ->join('transactions as t', 't.id', '=', 'pl.transaction_id')
            ->where('t.business_id', $business_id)
            ->where('pl.variation_id', $variation_id)
            ->where('pl.location_id', $location_id)
            ->where('t.status', 'received')
            ->where('t.deleted_at', null)
            ->select(
                'pl.id as purchase_line_id',
                'pl.lot_number',
                'pl.exp_date',
                DB::raw('(pl.quantity - COALESCE(pl.quantity_sold,0) - COALESCE(pl.quantity_adjusted,0) - COALESCE(pl.quantity_returned,0)) as qty_available')
            )
            ->whereRaw('(pl.quantity - COALESCE(pl.quantity_sold,0) - COALESCE(pl.quantity_adjusted,0) - COALESCE(pl.quantity_returned,0)) > 0')
            ->orderByRaw('COALESCE(pl.exp_date, "9999-12-31") ASC')
            ->orderBy('pl.id', 'ASC');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Plan the consumption of $quantity units from available batches
     * using FEFO. Returns an array of [purchase_line_id, quantity]
     * describing which batches to consume and how much from each.
     */
    public function planFefoConsumption(int $business_id, int $location_id, int $variation_id, float $quantity): array
    {
        $plan = [];
        $remaining = $quantity;
        $batches = $this->availableBatches($business_id, $location_id, $variation_id);
        foreach ($batches as $b) {
            if ($remaining <= 0) break;
            $take = min($remaining, (float) $b->qty_available);
            $plan[] = [
                'purchase_line_id' => $b->purchase_line_id,
                'lot_number' => $b->lot_number,
                'exp_date' => $b->exp_date,
                'quantity' => $take,
            ];
            $remaining -= $take;
        }
        return [
            'plan' => $plan,
            'fully_fulfilled' => $remaining <= 0,
            'shortfall' => max(0, $remaining),
        ];
    }

    /**
     * Return batches that are expired as of today (for warning / blocking).
     */
    public function expiredBatches(int $business_id, int $location_id = null)
    {
        $q = DB::table('purchase_lines as pl')
            ->join('transactions as t', 't.id', '=', 'pl.transaction_id')
            ->where('t.business_id', $business_id)
            ->where('t.status', 'received')
            ->whereNotNull('pl.exp_date')
            ->whereDate('pl.exp_date', '<', today())
            ->select('pl.id', 'pl.lot_number', 'pl.exp_date', 'pl.variation_id', 'pl.location_id', 'pl.quantity');

        if ($location_id) {
            $q->where('pl.location_id', $location_id);
        }
        return $q->get();
    }
}
