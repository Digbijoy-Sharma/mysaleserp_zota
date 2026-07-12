<?php

namespace App\Http\Controllers\Superadmin;

use App\Business;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Dava India — Phase 5
 *
 * Cross-store stock report and stock alert report for the super admin.
 * Aggregates variation_location_details across all stores with rich
 * filters and CSV export.
 */
class StockReportController extends Controller
{
    /**
     * Cross-store stock report.
     */
    public function index(Request $request)
    {
        $rows = $this->buildStockQuery($request)->paginate(50);
        $stores = Business::orderBy('name')->get(['id', 'name', 'state', 'store_code']);
        $totals = $this->buildStockQuery($request)
            ->selectRaw('SUM(vld.qty_available) as total_qty, COUNT(*) as line_count')
            ->first();
        return view('superadmin.reports.stock', compact('rows', 'stores', 'totals'));
    }

    public function alerts(Request $request)
    {
        $query = DB::table('variation_location_details as vld')
            ->join('products as p', 'p.id', '=', 'vld.product_id')
            ->join('business as b', 'b.id', '=', 'p.business_id')
            ->where('p.enable_stock', 1)
            ->where('p.is_inactive', 0)
            ->where('b.is_suspended', 0)
            ->whereColumn('vld.qty_available', '<=', 'p.alert_quantity');

        if ($b = $request->get('business_id')) $query->where('b.id', $b);
        if ($b = $request->get('state')) $query->where('b.state', $b);

        $query->select(
            'b.id as business_id', 'b.name as store_name', 'b.state', 'b.store_code',
            'p.id as product_id', 'p.name as product_name', 'p.sku', 'p.alert_quantity',
            'vld.qty_available', 'vld.location_id'
        )->orderBy('vld.qty_available', 'asc');

        $rows = $query->paginate(50);
        return view('superadmin.reports.stock_alerts', compact('rows'));
    }

    public function export(Request $request)
    {
        $rows = $this->buildStockQuery($request)->get();
        $filename = 'dava_stock_report_' . date('Ymd_His') . '.csv';
        $columns = ['store_name', 'store_code', 'state', 'sku', 'product_name', 'qty_available', 'alert_quantity', 'selling_price'];

        return response()->stream(function () use ($rows, $columns) {
            $f = fopen('php://output', 'w');
            fputcsv($f, $columns);
            foreach ($rows as $r) {
                fputcsv($f, [
                    $r->store_name,
                    $r->store_code,
                    $r->state,
                    $r->sku,
                    $r->product_name,
                    $r->qty_available,
                    $r->alert_quantity,
                    $r->selling_price,
                ]);
            }
            fclose($f);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    protected function buildStockQuery(Request $request)
    {
        $query = DB::table('variation_location_details as vld')
            ->join('products as p', 'p.id', '=', 'vld.product_id')
            ->join('business as b', 'b.id', '=', 'p.business_id')
            ->leftJoin('variations as v', 'v.id', '=', 'vld.variation_id')
            ->leftJoin('business_locations as bl', 'bl.id', '=', 'vld.location_id')
            ->where('p.enable_stock', 1)
            ->where('p.is_inactive', 0)
            ->where('b.is_suspended', 0);

        if ($b = $request->get('business_id')) $query->where('b.id', $b);
        if ($st = $request->get('state')) $query->where('b.state', $st);
        if ($sku = $request->get('sku')) $query->where('p.sku', 'like', "%{$sku}%");
        if ($name = $request->get('product_name')) $query->where('p.name', 'like', "%{$name}%");

        if ($request->get('low_stock') === '1') {
            $query->whereColumn('vld.qty_available', '<=', 'p.alert_quantity');
        }
        if ($request->get('out_of_stock') === '1') {
            $query->where('vld.qty_available', '<=', 0);
        }

        return $query->select(
            'b.id as business_id', 'b.name as store_name', 'b.state', 'b.store_code',
            'p.id as product_id', 'p.name as product_name', 'p.sku',
            'p.alert_quantity', 'v.default_sell_price as selling_price',
            'vld.qty_available', 'vld.location_id', 'bl.name as location_name'
        )->orderBy('b.name')->orderBy('p.name');
    }
}
