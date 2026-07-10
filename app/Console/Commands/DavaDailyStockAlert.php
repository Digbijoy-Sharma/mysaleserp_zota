<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Dava India — Phase 5
 *
 * Daily command: send a stock-alert digest to all super admins.
 * Usage:  php artisan dava:daily-stock-alert
 * Schedule at 09:00 IST (config('dava.reports.stock_alert_digest_time'))
 */
class DavaDailyStockAlert extends Command
{
    protected $signature = 'dava:daily-stock-alert';
    protected $description = 'Send daily low-stock digest to all Dava India super admins';

    public function handle()
    {
        $this->info('Gathering low-stock rows...');

        $rows = DB::table('variation_location_details as vld')
            ->join('products as p', 'p.id', '=', 'vld.product_id')
            ->join('business as b', 'b.id', '=', 'p.business_id')
            ->where('p.enable_stock', 1)
            ->where('p.is_inactive', 0)
            ->where('b.is_suspended', 0)
            ->whereColumn('vld.qty_available', '<=', 'p.alert_quantity')
            ->select(
                'b.name as store_name', 'b.store_code', 'b.state',
                'p.name as product_name', 'p.sku', 'p.alert_quantity',
                'vld.qty_available'
            )
            ->orderBy('vld.qty_available', 'asc')
            ->limit(200)
            ->get();

        $totals = DB::table('variation_location_details as vld')
            ->join('products as p', 'p.id', '=', 'vld.product_id')
            ->join('business as b', 'b.id', '=', 'p.business_id')
            ->where('p.enable_stock', 1)
            ->where('b.is_suspended', 0)
            ->selectRaw('SUM(CASE WHEN vld.qty_available <= 0 THEN 1 ELSE 0 END) as out_of_stock,
                         SUM(CASE WHEN vld.qty_available > 0 AND vld.qty_available <= p.alert_quantity THEN 1 ELSE 0 END) as low_stock')
            ->first();

        $superadmins = User::where('is_superadmin', 1)->get();
        if ($superadmins->isEmpty()) {
            $this->warn('No super admins found.');
            return 0;
        }

        $body = $this->buildBody($rows, $totals);

        foreach ($superadmins as $sa) {
            if (! $sa->email) continue;
            try {
                Mail::raw($body, function ($msg) use ($sa) {
                    $msg->to($sa->email)
                        ->subject('[Dava India] Daily stock alert digest — ' . today()->format('d M Y'));
                });
                $this->info("Sent to {$sa->email}");
            } catch (\Throwable $e) {
                $this->error("Failed to send to {$sa->email}: " . $e->getMessage());
            }
        }

        return 0;
    }

    protected function buildBody($rows, $totals): string
    {
        $out = "Dava India — Daily Stock Alert Digest\n";
        $out .= "Date: " . today()->format('d M Y') . "\n\n";
        $out .= "Summary:\n";
        $out .= "  Out of stock items: " . ($totals->out_of_stock ?? 0) . "\n";
        $out .= "  Low stock items:    " . ($totals->low_stock ?? 0) . "\n\n";

        if ($rows->isEmpty()) {
            $out .= "All stores are at or above alert quantities. ✓\n";
            return $out;
        }

        $out .= "Top 200 alert items:\n";
        $out .= str_repeat('-', 80) . "\n";
        $out .= sprintf("%-30s %-15s %-25s %10s %10s\n", 'Store', 'SKU', 'Product', 'Qty', 'Alert');
        $out .= str_repeat('-', 80) . "\n";
        foreach ($rows as $r) {
            $out .= sprintf("%-30s %-15s %-25s %10s %10s\n",
                substr($r->store_name, 0, 28),
                substr($r->sku, 0, 13),
                substr($r->product_name, 0, 23),
                $r->qty_available,
                $r->alert_quantity
            );
        }
        $out .= "\nFor full details, visit: " . url('/super/reports/stock-alerts') . "\n";
        return $out;
    }
}
