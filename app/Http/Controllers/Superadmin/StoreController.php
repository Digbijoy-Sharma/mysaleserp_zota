<?php

namespace App\Http\Controllers\Superadmin;

use App\Business;
use App\Http\Controllers\Controller;
use App\Services\DavaStoreService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Dava India — Phase 4
 *
 * Store lifecycle: create, edit, suspend, activate, bulk CSV import.
 * Each store is one `business` row with one auto-managed `business_locations`.
 */
class StoreController extends Controller
{
    public function __construct(protected DavaStoreService $storeService)
    {
    }

    public function index(Request $request)
    {
        $q = Business::query();
        if ($term = $request->get('search')) {
            $q->where(function ($x) use ($term) {
                $x->where('name', 'like', "%{$term}%")
                  ->orWhere('store_code', 'like', "%{$term}%")
                  ->orWhere('gstin', 'like', "%{$term}%")
                  ->orWhere('drug_license_no', 'like', "%{$term}%");
            });
        }
        if ($state = $request->get('state')) $q->where('state', $state);
        if ($request->has('is_suspended')) $q->where('is_suspended', (int) $request->get('is_suspended'));

        $stores = $q->orderByDesc('id')->paginate(50);
        return view('superadmin.stores.index', compact('stores'));
    }

    public function create()
    {
        return view('superadmin.stores.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateStoreData($request);

        try {
            $result = $this->storeService->createStore($data);
            $createdBy = Auth::id();
            DB::table('activity_log')->insert([
                'log_name' => 'superadmin',
                'description' => 'Store created: ' . $result['business']->name,
                'subject_type' => Business::class,
                'subject_id' => $result['business']->id,
                'causer_type' => User::class,
                'causer_id' => $createdBy,
                'event' => 'store.created',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Store creation failed: ' . $e->getMessage()]);
        }

        return redirect()->route('super.stores.index')
            ->with('status', [
                'success' => 1,
                'msg' => "Store created. Admin username: {$result['admin']->username}.",
            ]);
    }

    public function edit($id)
    {
        $store = Business::findOrFail($id);
        $admin = User::where('business_id', $store->id)->where('is_superadmin', 0)->orderBy('id')->first();
        return view('superadmin.stores.edit', compact('store', 'admin'));
    }

    public function update(Request $request, $id)
    {
        $store = Business::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'store_code' => 'nullable|string|max:30|unique:business,store_code,' . $store->id,
            'gstin' => 'nullable|string|max:20',
            'drug_license_no' => 'nullable|string|max:60',
            'state' => 'nullable|string|max:80',
            'district' => 'nullable|string|max:80',
            'pincode' => 'nullable|string|max:12',
            'mobile' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
        ]);
        $store->update($data);

        return redirect()->route('super.stores.index')
            ->with('status', ['success' => 1, 'msg' => 'Store updated.']);
    }

    public function suspend($id)
    {
        $store = Business::findOrFail($id);
        $store->is_suspended = 1;
        $store->suspended_at = now();
        $store->suspended_reason = request('reason');
        $store->save();

        DB::table('activity_log')->insert([
            'log_name' => 'superadmin',
            'description' => 'Store suspended: ' . $store->name,
            'subject_type' => Business::class,
            'subject_id' => $store->id,
            'causer_type' => User::class,
            'causer_id' => Auth::id(),
            'event' => 'store.suspended',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return redirect()->route('super.stores.index')
            ->with('status', ['success' => 1, 'msg' => 'Store suspended.']);
    }

    public function activate($id)
    {
        $store = Business::findOrFail($id);
        $store->is_suspended = 0;
        $store->suspended_at = null;
        $store->suspended_reason = null;
        $store->save();

        DB::table('activity_log')->insert([
            'log_name' => 'superadmin',
            'description' => 'Store activated: ' . $store->name,
            'subject_type' => Business::class,
            'subject_id' => $store->id,
            'causer_type' => User::class,
            'causer_id' => Auth::id(),
            'event' => 'store.activated',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return redirect()->route('super.stores.index')
            ->with('status', ['success' => 1, 'msg' => 'Store activated.']);
    }

    public function import()
    {
        return view('superadmin.stores.import');
    }

    public function processImport(Request $request)
    {
        $request->validate(['csv' => 'required|file|mimes:csv,txt']);
        $path = $request->file('csv')->getRealPath();
        $rows = array_map('str_getcsv', file($path));
        $header = array_shift($rows);
        $idx = array_flip($header);

        $success = 0;
        $errors = [];
        foreach ($rows as $i => $row) {
            try {
                if (count($row) < 3) continue;
                $payload = [
                    'name' => trim($row[$idx['name']] ?? ''),
                    'store_code' => trim($row[$idx['store_code']] ?? '') ?: null,
                    'gstin' => trim($row[$idx['gstin']] ?? '') ?: null,
                    'drug_license_no' => trim($row[$idx['drug_license_no']] ?? '') ?: null,
                    'state' => trim($row[$idx['state']] ?? '') ?: null,
                    'district' => trim($row[$idx['district']] ?? '') ?: null,
                    'pincode' => trim($row[$idx['pincode']] ?? '') ?: null,
                    'city' => trim($row[$idx['city']] ?? '') ?: null,
                    'country' => trim($row[$idx['country']] ?? 'India') ?: 'India',
                    'admin_username' => trim($row[$idx['admin_username']] ?? '') ?: null,
                    'admin_email' => trim($row[$idx['admin_email']] ?? '') ?: null,
                    'admin_password' => trim($row[$idx['admin_password']] ?? 'Dava@Store#2026') ?: 'Dava@Store#2026',
                    'currency_id' => 134,
                    'time_zone' => 'Asia/Kolkata',
                ];
                if (! $payload['name']) continue;
                $this->storeService->createStore($payload);
                $success++;
            } catch (\Throwable $e) {
                $errors[] = "Row " . ($i + 2) . ": " . $e->getMessage();
            }
        }

        return redirect()->route('super.stores.index')
            ->with('status', [
                'success' => 1,
                'msg' => "Imported {$success} stores. " . (count($errors) ? 'Errors: ' . implode('; ', array_slice($errors, 0, 5)) : ''),
            ]);
    }

    public function export()
    {
        $rows = Business::orderBy('id')->get([
            'id', 'name', 'store_code', 'gstin', 'drug_license_no',
            'state', 'district', 'pincode', 'is_suspended',
        ]);
        $filename = 'dava_stores_' . date('Ymd_His') . '.csv';
        $columns = ['id', 'name', 'store_code', 'gstin', 'drug_license_no', 'state', 'district', 'pincode', 'is_suspended'];

        return response()->stream(function () use ($rows, $columns) {
            $f = fopen('php://output', 'w');
            fputcsv($f, $columns);
            foreach ($rows as $r) fputcsv($f, $r->only($columns));
            fclose($f);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    protected function validateStoreData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:191',
            'store_code' => 'nullable|string|max:30|unique:business,store_code',
            'gstin' => 'nullable|string|max:20',
            'drug_license_no' => 'nullable|string|max:60',
            'state' => 'nullable|string|max:80',
            'district' => 'nullable|string|max:80',
            'city' => 'nullable|string|max:80',
            'pincode' => 'nullable|string|max:12',
            'country' => 'nullable|string|max:60',
            'landmark' => 'nullable|string|max:191',
            'mobile' => 'nullable|string|max:30',
            'alternate_number' => 'nullable|string|max:30',
            'website' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:100',
            'time_zone' => 'nullable|string|max:191',
            'admin_username' => 'nullable|string|max:191',
            'admin_email' => 'nullable|email|max:100',
            'admin_password' => 'nullable|string|min:6',
            'admin_first_name' => 'nullable|string|max:191',
            'admin_last_name' => 'nullable|string|max:191',
            'currency_id' => 'nullable|integer',
        ]);
    }
}
