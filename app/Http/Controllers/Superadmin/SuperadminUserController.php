<?php

namespace App\Http\Controllers\Superadmin;

use App\Business;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Dava India — Phase 4
 *
 * Cross-store user management. Super Admin creates users and binds
 * them to exactly one store. A non-superadmin user is permanently
 * tied to that single business_id.
 */
class SuperadminUserController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query()->where('is_superadmin', 0);
        if ($term = $request->get('search')) {
            $q->where(function ($x) use ($term) {
                $x->where('username', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('first_name', 'like', "%{$term}%");
            });
        }
        if ($bid = $request->get('business_id')) $q->where('business_id', $bid);
        $users = $q->orderByDesc('id')->paginate(50);
        $stores = Business::orderBy('name')->get(['id', 'name']);
        return view('superadmin.users.index', compact('users', 'stores'));
    }

    public function create()
    {
        $stores = Business::orderBy('name')->get(['id', 'name', 'store_code']);
        $roles = $this->availableRoles();
        return view('superadmin.users.create', compact('stores', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'nullable|string|max:191',
            'username' => 'required|string|max:191|unique:users,username',
            'email' => 'nullable|email|max:100|unique:users,email',
            'password' => 'required|string|min:6',
            'business_id' => 'required|integer|exists:business,id',
            'role' => 'required|string',
        ]);

        $user = User::create([
            'surname' => '',
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? '',
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'language' => 'en',
            'is_superadmin' => 0,
            'created_by_superadmin' => 1,
            'business_id' => $data['business_id'],
            'allow_login' => 1,
        ]);

        $user->assignRole($data['role']);

        DB::table('activity_log')->insert([
            'log_name' => 'superadmin',
            'description' => "User created: {$user->username} (store #{$user->business_id})",
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'causer_type' => User::class,
            'causer_id' => Auth::id(),
            'event' => 'user.created',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return redirect()->route('super.users.index')
            ->with('status', ['success' => 1, 'msg' => "User {$user->username} created and assigned to store #{$user->business_id}."]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $stores = Business::orderBy('name')->get(['id', 'name', 'store_code']);
        $roles = $this->availableRoles();
        return view('superadmin.users.edit', compact('user', 'stores', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:100|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'business_id' => 'required|integer|exists:business,id',
            'role' => 'required|string',
            'allow_login' => 'nullable|boolean',
        ]);

        $user->fill([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? '',
            'email' => $data['email'] ?? $user->email,
            'business_id' => $data['business_id'],
            'allow_login' => $request->boolean('allow_login', 1),
        ]);
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();
        $user->syncRoles([$data['role']]);

        return redirect()->route('super.users.index')
            ->with('status', ['success' => 1, 'msg' => 'User updated.']);
    }

    public function assignStore(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'business_id' => 'required|integer|exists:business,id',
        ]);
        $old = $user->business_id;
        $user->business_id = $data['business_id'];
        $user->save();

        // Re-assign the Admin#<business_id> role for the new business
        $newRole = 'Admin#' . $data['business_id'];
        if (! Role::where('name', $newRole)->exists()) {
            $r = Role::create(['name' => $newRole, 'guard_name' => 'web']);
            $r->syncPermissions(\Spatie\Permission\Models\Permission::all());
        }
        $user->syncRoles([$newRole]);

        DB::table('activity_log')->insert([
            'log_name' => 'superadmin',
            'description' => "User moved: {$user->username} from store #{$old} to store #{$user->business_id}",
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'causer_type' => User::class,
            'causer_id' => Auth::id(),
            'event' => 'user.store_changed',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return back()->with('status', ['success' => 1, 'msg' => 'User moved to new store.']);
    }

    protected function availableRoles(): array
    {
        // Generic roles for Dava India
        $roles = [
            'Admin#' => 'Store Admin',
            'Cashier#' => 'Cashier',
            'Pharmacist#' => 'Pharmacist',
            'Manager#' => 'Store Manager',
        ];
        $businesses = Business::pluck('id');
        $names = [];
        foreach ($roles as $base => $label) {
            foreach ($businesses as $bid) {
                $names[$base . $bid] = "{$label} (Store #{$bid})";
            }
        }
        return $names;
    }
}
