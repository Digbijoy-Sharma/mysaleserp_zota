<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Dava India — Phase 2 + 6
 *
 * Hooks into the existing sell pipeline and, when Dava India FEFO mode
 * is on, re-allocates the purchase_line → sell_line mapping to use
 * First-Expiry-First-Out batch picking instead of the default
 * transaction_date-based FIFO/LIFO.
 *
 * It is intentionally a thin wrapper so the original
 * `TransactionUtil::mapPurchaseSell()` does not need to be modified
 * (which would risk breaking the classic-mode flow).
 *
 * Usage in the sell controller:
 *   $this->transactionUtil->mapPurchaseSell($business, $sell_lines, 'purchase');
 *   app(\App\Services\DavaFefoHook::class)
 *       ->applyFefoAfterMapping($business['id'], $business['location_id'], $sell_lines);
 */
class DavaFefoHook
{
    /** @var DavaFefoService */
    protected $fefo;

    public function __construct(DavaFefoService $fefo)
    {
        $this->fefo = $fefo;
    }

    /**
     * Should we re-allocate using FEFO?
     */
    public function isEnabled(): bool
    {
        if (! function_exists('config')) {
            return false;
        }
        return (bool) config('dava.pharmacy.fefo', false);
    }

    /**
     * After mapPurchaseSell() has done its standard FIFO/LIFO allocation,
     * re-do the allocation using FEFO. Safe to call when there is no
     * expiry tracking (just a no-op).
     *
     * @param  int  $business_id
     * @param  int  $location_id
     * @param  \Illuminate\Support\Collection|array  $sell_lines  sell line models
     * @return array  { 'reallocated' => int, 'shortfalls' => array }
     */
    public function applyFefoAfterMapping($business_id, $location_id, $sell_lines): array
    {
        if (! $this->isEnabled()) {
            return ['reallocated' => 0, 'shortfalls' => []];
        }
        if (empty($sell_lines)) {
            return ['reallocated' => 0, 'shortfalls' => []];
        }

        $reallocated = 0;
        $shortfalls = [];

        foreach ($sell_lines as $line) {
            $product = \App\Product::find($line->product_id);
            if (empty($product) || $product->enable_stock != 1) {
                continue;
            }

            $plan = $this->fefo->planFefoConsumption(
                (int) $business_id,
                (int) $location_id,
                (int) $line->variation_id,
                (float) $line->quantity
            );

            if (empty($plan['plan'])) {
                continue;   // No batches available; leave existing mapping as-is
            }

            // Wipe the existing mapping rows for this sell line, restore sold counts
            $existing = DB::table('transaction_sell_lines_purchase_lines')
                ->where('sell_line_id', $line->id)
                ->get();

            foreach ($existing as $ex) {
                DB::table('purchase_lines')
                    ->where('id', $ex->purchase_line_id)
                    ->decrement('quantity_sold', $ex->quantity);
            }
            DB::table('transaction_sell_lines_purchase_lines')
                ->where('sell_line_id', $line->id)
                ->delete();

            // Insert the FEFO plan
            $now = now();
            foreach ($plan['plan'] as $p) {
                DB::table('transaction_sell_lines_purchase_lines')->insert([
                    'sell_line_id'    => $line->id,
                    'purchase_line_id' => $p['purchase_line_id'],
                    'quantity'        => $p['quantity'],
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]);
                DB::table('purchase_lines')
                    ->where('id', $p['purchase_line_id'])
                    ->increment('quantity_sold', $p['quantity']);
                $reallocated++;
            }

            if (! $plan['fully_fulfilled'] && $plan['shortfall'] > 0) {
                $shortfalls[] = [
                    'sell_line_id' => $line->id,
                    'variation_id' => $line->variation_id,
                    'product_id'   => $line->product_id,
                    'shortfall'    => $plan['shortfall'],
                ];
            }
        }

        if (! empty($shortfalls)) {
            Log::warning('Dava India FEFO: stock shortfalls detected', [
                'business_id' => $business_id,
                'location_id' => $location_id,
                'shortfalls'  => $shortfalls,
            ]);
        }

        return [
            'reallocated' => $reallocated,
            'shortfalls'  => $shortfalls,
        ];
    }
}
