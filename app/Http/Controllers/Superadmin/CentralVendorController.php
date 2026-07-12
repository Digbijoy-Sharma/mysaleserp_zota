<?php

namespace App\Http\Controllers\Superadmin;

use App\Business;
use App\Http\Controllers\Controller;
use App\Models\CentralSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Dava India — Phase 3
 *
 * Central vendor (supplier) master management.
 * Super Admin creates one global vendor; it can be assigned to many stores.
 */
class CentralVendorController extends Controller
{
    public function index(Request $request)
    {
        $query = CentralSupplier::query();
        if ($q = $request->get('search')) {
            $query->where(function ($x) use ($q) {
                $x->where('name', 'like', "%{$q}%")
                  ->orWhere('code', 'like', "%{$q}%")
                  ->orWhere('gstin', 'like', "%{$q}%")
                  ->orWhere('mobile', 'like', "%{$q}%");
            });
        }
        if ($request->get('active') !== null) {
            $query->where('is_active', (int) $request->get('active'));
        }
        $suppliers = $query->orderByDesc('id')->paginate(50);
        return view('superadmin.vendors.index', compact('suppliers'));
    }

    public function create()
    {
        $stores = Business::orderBy('name')->get(['id', 'name', 'store_code']);
        return view('superadmin.vendors.create', compact('stores'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['created_by'] = Auth::id();
        $supplier = CentralSupplier::create($data);
        $this->syncStoreAssignments($supplier, $request);

        return redirect()->route('super.vendors.index')
            ->with('status', ['success' => 1, 'msg' => 'Central supplier created.']);
    }

    public function show($id)
    {
        $supplier = CentralSupplier::findOrFail($id);
        $assigned = DB::table('central_supplier_store')
            ->where('central_supplier_id', $id)->pluck('business_id')->toArray();
        $stores = Business::orderBy('name')->get(['id', 'name', 'store_code']);
        return view('superadmin.vendors.show', compact('supplier', 'stores', 'assigned'));
    }

    public function edit($id)
    {
        $supplier = CentralSupplier::findOrFail($id);
        $stores = Business::orderBy('name')->get(['id', 'name', 'store_code']);
        $assigned = DB::table('central_supplier_store')
            ->where('central_supplier_id', $id)->pluck('business_id')->toArray();
        return view('superadmin.vendors.edit', compact('supplier', 'stores', 'assigned'));
    }

    public function update(Request $request, $id)
    {
        $supplier = CentralSupplier::findOrFail($id);
        $data = $this->validateData($request, $id);
        $data['updated_by'] = Auth::id();
        $supplier->update($data);
        $this->syncStoreAssignments($supplier, $request);

        return redirect()->route('super.vendors.index')
            ->with('status', ['success' => 1, 'msg' => 'Central supplier updated.']);
    }

    public function destroy($id)
    {
        $supplier = CentralSupplier::findOrFail($id);
        $supplier->delete();
        return redirect()->route('super.vendors.index')
            ->with('status', ['success' => 1, 'msg' => 'Central supplier deleted.']);
    }

    protected function validateData(Request $request, $id = null): array
    {
        $unique = $id ? 'unique:central_suppliers,code,' . $id : 'unique:central_suppliers,code';
        return $request->validate([
            'code' => 'nullable|string|max:30|' . $unique,
            'name' => 'required|string|max:191',
            'gstin' => 'nullable|string|max:20',
            'drug_license_no' => 'nullable|string|max:60',
            'fssai_no' => 'nullable|string|max:30',
            'contact_person' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'mobile' => 'nullable|string|max:30',
            'address_line1' => 'nullable|string|max:191',
            'address_line2' => 'nullable|string|max:191',
            'city' => 'nullable|string|max:80',
            'state' => 'nullable|string|max:80',
            'pincode' => 'nullable|string|max:12',
            'country' => 'nullable|string|max:60',
            'payment_terms' => 'nullable|string',
            'lead_time_days' => 'nullable|integer|min:0',
            'credit_limit' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_no' => 'nullable|string|max:30',
            'bank_ifsc' => 'nullable|string|max:15',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]) + ['is_active' => $request->boolean('is_active', 1)];
    }

    protected function syncStoreAssignments(CentralSupplier $supplier, Request $request)
    {
        $storeIds = $request->input('store_ids', []);
        if (config('dava.central_vendors.auto_assign_all_stores') && empty($storeIds)) {
            $storeIds = Business::pluck('id')->toArray();
        }
        $now = now();
        $rows = [];
        foreach ($storeIds as $bid) {
            $rows[] = [
                'central_supplier_id' => $supplier->id,
                'business_id' => (int) $bid,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('central_supplier_store')->where('central_supplier_id', $supplier->id)->delete();
        if ($rows) {
            DB::table('central_supplier_store')->insert($rows);
        }
    }
}
