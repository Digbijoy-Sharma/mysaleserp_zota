<?php

namespace App\Http\Controllers\Superadmin;

use App\Business;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dava India — Phase 1 + Phase 5
 *
 * Central landing page for the Dava India super admin.
 * Aggregates cross-store KPIs so the super admin can see the
 * health of all 2800 stores at a glance.
 */
class DashboardController extends Controller
{
    /**
     * Show the super admin dashboard.
     */
    public function index(Request $request)
    {
        $stats = [
            'total_stores' => 0,
            'active_stores' => 0,
            'suspended_stores' => 0,
            'total_central_products' => 0,
            'total_users' => 0,
            'total_superadmins' => User::where('is_superadmin', 1)->count(),
            'today_sales_total' => 0,
            'low_stock_count' => 0,
            'out_of_stock_count' => 0,
        ];

        try {
            $stats['total_stores'] = Business::count();
            $stats['active_stores'] = Business::where('is_suspended', 0)->count();
            $stats['suspended_stores'] = Business::where('is_suspended', 1)->count();
            $stats['total_users'] = User::where('is_superadmin', 0)->count();

            // Central products table is created in Phase 2; tolerate absence.
            if (Schema::hasTable('central_products')) {
                $stats['total_central_products'] = DB::table('central_products')->count();
            }

            // Today's sales (final status) across all stores
            $stats['today_sales_total'] = DB::table('transactions')
                ->whereIn('type', ['sell'])
                ->where('status', 'final')
                ->whereDate('transaction_date', today())
                ->sum('final_total');

            // Low-stock count (qty_available <= alert_quantity)
            $stats['low_stock_count'] = DB::table('variation_location_details as vld')
                ->join('products as p', 'p.id', '=', 'vld.product_id')
                ->whereColumn('vld.qty_available', '<=', 'p.alert_quantity')
                ->where('p.enable_stock', 1)
                ->where('p.is_inactive', 0)
                ->count();

            $stats['out_of_stock_count'] = DB::table('variation_location_details')
                ->where('qty_available', '<=', 0)
                ->count();
        } catch (\Throwable $e) {
            // Tolerate partial schema during phased rollout
        }

        // Top 10 stores by revenue (last 30 days)
        $topStores = collect();
        try {
            $topStores = DB::table('transactions as t')
                ->join('business as b', 'b.id', '=', 't.business_id')
                ->where('t.type', 'sell')
                ->where('t.status', 'final')
                ->whereDate('t.transaction_date', '>=', now()->subDays(30))
                ->groupBy('b.id', 'b.name')
                ->select(
                    'b.id', 'b.name',
                    DB::raw('SUM(t.final_total) as revenue'),
                    DB::raw('COUNT(t.id) as bill_count')
                )
                ->orderByDesc('revenue')
                ->limit(10)
                ->get();
        } catch (\Throwable $e) {
            // ignore
        }

        return view('superadmin.dashboard', compact('stats', 'topStores'));
    }
}
