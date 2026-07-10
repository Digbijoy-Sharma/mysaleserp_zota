<?php

namespace App\Http\Controllers\Superadmin;

use App\Business;
use App\Http\Controllers\Controller;
use App\Models\CentralProduct;
use App\Models\StoreProductOverride;
use App\Services\DavaFefoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Dava India — Phase 2
 *
 * Central product master management.
 * Super Admin can create / edit / disable / import / export the
 * global product catalog and assign it to stores.
 */
class CentralProductController extends Controller
{
    public function index(Request $request)
    {
        $query = CentralProduct::query();
        if ($q = $request->get('search')) {
            $query->where(function ($x) use ($q) {
                $x->where('name', 'like', "%{$q}%")
                  ->orWhere('sku', 'like', "%{$q}%")
                  ->orWhere('barcode', 'like', "%{$q}%")
                  ->orWhere('manufacturer', 'like', "%{$q}%");
            });
        }
        if ($sched = $request->get('drug_schedule')) {
            $query->where('drug_schedule', $sched);
        }
        if ($cat = $request->get('category')) {
            $query->where('category', $cat);
        }
        if ($request->has('is_active')) {
            $query->where('is_active', (int) $request->get('is_active'));
        }

        $products = $query->orderByDesc('id')->paginate(50);

        return view('superadmin.products.index', compact('products'));
    }

    public function create()
    {
        $stores = Business::orderBy('name')->get(['id', 'name', 'store_code']);
        return view('superadmin.products.create', compact('stores'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['created_by'] = Auth::id();

        $product = CentralProduct::create($data);

        // Auto-assign to stores
        $this->syncStoreAssignments($product, $request);

        Cache::forget('central_products_active');

        return redirect()
            ->route('super.products.index')
            ->with('status', ['success' => 1, 'msg' => 'Central product created.']);
    }

    public function edit($id)
    {
        $product = CentralProduct::findOrFail($id);
        $stores = Business::orderBy('name')->get(['id', 'name', 'store_code']);
        $assigned = DB::table('central_product_store')
            ->where('central_product_id', $id)
            ->pluck('business_id')
            ->toArray();
        return view('superadmin.products.edit', compact('product', 'stores', 'assigned'));
    }

    public function update(Request $request, $id)
    {
        $product = CentralProduct::findOrFail($id);
        $data = $this->validateData($request, $product->id);
        $data['updated_by'] = Auth::id();

        $product->update($data);
        $this->syncStoreAssignments($product, $request);

        Cache::forget('central_products_active');

        return redirect()
            ->route('super.products.index')
            ->with('status', ['success' => 1, 'msg' => 'Central product updated.']);
    }

    public function destroy($id)
    {
        $product = CentralProduct::findOrFail($id);
        $product->delete();
        Cache::forget('central_products_active');

        return redirect()
            ->route('super.products.index')
            ->with('status', ['success' => 1, 'msg' => 'Central product deleted.']);
    }

    public function import()
    {
        return view('superadmin.products.import');
    }

    public function processImport(Request $request)
    {
        $request->validate(['csv' => 'required|file|mimes:csv,txt']);
        $path = $request->file('csv')->getRealPath();
        $rows = array_map('str_getcsv', file($path));
        $header = array_shift($rows);

        $count = 0;
        $errors = [];
        $idx = array_flip($header);
        foreach ($rows as $i => $row) {
            try {
                if (count($row) < 3) continue;
                $sku = trim($row[$idx['sku']] ?? '');
                if (! $sku) continue;
                $payload = [
                    'name' => trim($row[$idx['name']] ?? ''),
                    'sku' => $sku,
                    'barcode' => trim($row[$idx['barcode']] ?? '') ?: null,
                    'category' => trim($row[$idx['category']] ?? '') ?: null,
                    'brand' => trim($row[$idx['brand']] ?? '') ?: null,
                    'unit' => trim($row[$idx['unit']] ?? '') ?: null,
                    'default_mrp' => (float) ($row[$idx['default_mrp']] ?? 0),
                    'default_sell_price' => (float) ($row[$idx['default_sell_price']] ?? 0),
                    'default_purchase_price' => (float) ($row[$idx['default_purchase_price']] ?? 0),
                    'default_gst_percent' => (float) ($row[$idx['default_gst_percent']] ?? 0),
                    'hsn_code' => trim($row[$idx['hsn_code']] ?? '') ?: null,
                    'composition' => trim($row[$idx['composition']] ?? '') ?: null,
                    'drug_schedule' => trim($row[$idx['drug_schedule']] ?? 'OTC') ?: 'OTC',
                    'prescription_required' => (int) ($row[$idx['prescription_required']] ?? 0),
                    'manufacturer' => trim($row[$idx['manufacturer']] ?? '') ?: null,
                    'package_form' => trim($row[$idx['package_form']] ?? 'strip') ?: 'strip',
                    'default_alert_quantity' => (float) ($row[$idx['default_alert_quantity']] ?? 0),
                    'is_active' => 1,
                    'created_by' => Auth::id(),
                ];
                CentralProduct::updateOrCreate(['sku' => $sku], $payload);
                $count++;
            } catch (\Throwable $e) {
                $errors[] = "Row " . ($i + 2) . ": " . $e->getMessage();
            }
        }

        Cache::forget('central_products_active');

        return redirect()
            ->route('super.products.index')
            ->with('status', [
                'success' => 1,
                'msg' => "Imported {$count} products." . (count($errors) ? ' Errors: ' . count($errors) : ''),
            ]);
    }

    public function export(Request $request)
    {
        $rows = CentralProduct::orderBy('id')->get([
            'name', 'sku', 'barcode', 'category', 'brand', 'unit',
            'default_mrp', 'default_sell_price', 'default_purchase_price',
            'default_gst_percent', 'hsn_code', 'composition', 'drug_schedule',
            'prescription_required', 'manufacturer', 'package_form', 'default_alert_quantity',
        ]);

        $filename = 'dava_central_products_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = [
            'name', 'sku', 'barcode', 'category', 'brand', 'unit',
            'default_mrp', 'default_sell_price', 'default_purchase_price',
            'default_gst_percent', 'hsn_code', 'composition', 'drug_schedule',
            'prescription_required', 'manufacturer', 'package_form', 'default_alert_quantity',
        ];

        $callback = function () use ($rows, $columns) {
            $f = fopen('php://output', 'w');
            fputcsv($f, $columns);
            foreach ($rows as $r) {
                fputcsv($f, $r->only($columns));
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function validateData(Request $request, $id = null): array
    {
        $unique = $id
            ? 'unique:central_products,sku,' . $id
            : 'unique:central_products,sku';

        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'sku' => 'required|string|max:191|' . $unique,
            'barcode' => 'nullable|string|max:100',
            'type' => 'required|in:single,variable',
            'category' => 'nullable|string|max:100',
            'sub_category' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:30',
            'default_mrp' => 'nullable|numeric|min:0',
            'default_purchase_price' => 'nullable|numeric|min:0',
            'default_sell_price' => 'nullable|numeric|min:0',
            'default_sell_price_inc_tax' => 'nullable|numeric|min:0',
            'default_gst_percent' => 'nullable|numeric|min:0|max:100',
            'hsn_code' => 'nullable|string|max:20',
            'tax_type' => 'nullable|in:inclusive,exclusive',
            'enable_stock' => 'nullable|boolean',
            'default_alert_quantity' => 'nullable|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
            'composition' => 'nullable|string',
            'drug_schedule' => 'required|in:none,H,H1,X,OTC',
            'prescription_required' => 'nullable|boolean',
            'manufacturer' => 'nullable|string|max:191',
            'marketed_by' => 'nullable|string|max:191',
            'storage_condition' => 'required|in:ambient,cold,dry,frozen',
            'package_form' => 'required|in:strip,bottle,injection,tube,drops,sachet,other',
            'units_per_pack' => 'nullable|integer|min:1',
            'is_banned' => 'nullable|boolean',
            'is_discontinued' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
        ]);

        $validated['enable_stock'] = $request->boolean('enable_stock', 1);
        $validated['prescription_required'] = $request->boolean('prescription_required');
        $validated['is_banned'] = $request->boolean('is_banned');
        $validated['is_discontinued'] = $request->boolean('is_discontinued');
        $validated['is_active'] = $request->boolean('is_active', 1);

        return $validated;
    }

    protected function syncStoreAssignments(CentralProduct $product, Request $request)
    {
        $storeIds = $request->input('store_ids', []);
        if (config('dava.central_catalog.auto_assign_all_stores') && empty($storeIds)) {
            $storeIds = Business::pluck('id')->toArray();
        }

        // Sync: only write the rows for stores that should have the product
        $now = now();
        $rows = [];
        foreach ($storeIds as $bid) {
            $rows[] = [
                'central_product_id' => $product->id,
                'business_id' => (int) $bid,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('central_product_store')->where('central_product_id', $product->id)->delete();
        if ($rows) {
            DB::table('central_product_store')->insert($rows);
        }
    }

    /**
     * JSON helper used by the POS (Dava India) to fetch FEFO batches.
     * Route: /super/api/fefo/{variation_id}
     *   ?business_id=  &location_id=  &quantity=
     */
    public function fefo(\Illuminate\Http\Request $request, \App\Variation $variation)
    {
        $businessId = (int) $request->get('business_id', 0);
        $locationId = (int) $request->get('location_id', 0);
        $quantity = (float) $request->get('quantity', 0);

        if (! $businessId || ! $locationId) {
            return response()->json(['error' => 'business_id and location_id required'], 400);
        }

        $svc = new DavaFefoService();
        $batches = $svc->availableBatches($businessId, $locationId, $variation->id);
        $plan = $quantity > 0
            ? $svc->planFefoConsumption($businessId, $locationId, $variation->id, $quantity)
            : null;

        return response()->json([
            'variation_id' => $variation->id,
            'batches' => $batches,
            'fefo_plan' => $plan,
        ]);
    }
}
