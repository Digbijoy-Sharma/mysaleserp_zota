<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Dava India — Phase 6
 *
 * POS guard that enforces pharmacy rules at sale time:
 *  - Drug schedule warning (H/H1/X) when no prescription number given
 *  - Hard block on expired batches
 *
 * The two checks are independent so the same service can be called
 * either as a "validate before save" or as a "soft warning" depending
 * on the runtime flag in config('dava.pharmacy.*').
 */
class DavaPosGuard
{
    /** @var DavaFefoService */
    protected $fefo;

    public function __construct(DavaFefoService $fefo)
    {
        $this->fefo = $fefo;
    }

    /**
     * Run all enabled guards. Return an array of guard issues:
     *   [
     *     'errors'   => [...],   // hard blockers
     *     'warnings' => [...],   // soft warnings
     *   ]
     *
     * @param  int  $business_id
     * @param  int  $location_id
     * @param  \Illuminate\Support\Collection|array  $sell_lines
     * @param  string|null  $prescription_no  (Rx number on the sale header)
     * @return array
     */
    public function check($business_id, $location_id, $sell_lines, $prescription_no = null): array
    {
        $errors = [];
        $warnings = [];

        if (empty($sell_lines)) {
            return ['errors' => $errors, 'warnings' => $warnings];
        }

        $blockExpired = (bool) config('dava.pharmacy.block_expired_sale', true);
        $warnSchedule = (bool) config('dava.pharmacy.pos_schedule_warning', true);

        foreach ($sell_lines as $line) {
            // Look up central product via SKU of the local product
            $localProduct = \App\Product::find($line->product_id);
            if (empty($localProduct)) {
                continue;
            }

            $central = DB::table('central_products')
                ->where('sku', $localProduct->sku)
                ->first();

            // Drug schedule warning
            if ($warnSchedule && $central && in_array($central->drug_schedule, ['H', 'H1', 'X'], true)) {
                if ($central->prescription_required && empty($prescription_no)) {
                    $warnings[] = [
                        'line_id'     => $line->id,
                        'product_id'  => $line->product_id,
                        'product'     => $localProduct->name,
                        'sku'         => $localProduct->sku,
                        'drug_schedule' => $central->drug_schedule,
                        'msg'         => "Schedule {$central->drug_schedule} drug '{$localProduct->name}' requires a prescription. Please capture the Rx number on the bill.",
                    ];
                }
            }

            // Expired-batch block
            if ($blockExpired) {
                $expired = DB::table('purchase_lines as pl')
                    ->join('transactions as t', 't.id', '=', 'pl.transaction_id')
                    ->where('t.business_id', $business_id)
                    ->where('pl.variation_id', $line->variation_id)
                    ->where('pl.location_id', $location_id)
                    ->where('t.status', 'received')
                    ->whereNotNull('pl.exp_date')
                    ->whereDate('pl.exp_date', '<', today())
                    ->whereRaw('(pl.quantity - COALESCE(pl.quantity_sold,0) - COALESCE(pl.quantity_adjusted,0) - COALESCE(pl.quantity_returned,0)) > 0')
                    ->select('pl.lot_number', 'pl.exp_date', 'pl.quantity')
                    ->get();

                if ($expired->count() > 0) {
                    foreach ($expired as $e) {
                        $errors[] = [
                            'line_id'    => $line->id,
                            'product_id' => $line->product_id,
                            'product'    => $localProduct->name,
                            'lot_number' => $e->lot_number,
                            'exp_date'   => $e->exp_date,
                            'quantity'   => $e->quantity,
                            'msg'        => "Expired batch found: {$localProduct->name} (Lot {$e->lot_number}, exp {$e->exp_date}). Mark stock as damaged/adjusted before selling.",
                        ];
                    }
                }
            }
        }

        return [
            'errors'   => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Convenience: if any errors are present, throw a RuntimeException
     * that the controller can catch and return as a JSON error.
     */
    public function assertCanSell($business_id, $location_id, $sell_lines, $prescription_no = null): array
    {
        $result = $this->check($business_id, $location_id, $sell_lines, $prescription_no);
        if (! empty($result['errors'])) {
            $msg = $result['errors'][0]['msg'];
            // If multiple, join with semicolons
            if (count($result['errors']) > 1) {
                $msg = count($result['errors']) . ' stock issues: ' . collect($result['errors'])->pluck('msg')->implode(' / ');
            }
            throw new \RuntimeException($msg);
        }
        return $result;
    }
}
