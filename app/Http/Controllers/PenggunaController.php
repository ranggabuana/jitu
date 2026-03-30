<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Opd;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $perPage = $request->get('per_page', 10);
        $roleFilter = $request->get('role_filter', 'all');
        $statusFilter = $request->get('status_filter', 'all');

        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $allowedSorts = ['name', 'email', 'role', 'status', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'name';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'asc';

        // Exclude pemohon role from data pengguna
        $query = User::where('role', '!=', 'pemohon')->with('opd');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($roleFilter !== 'all') {
            $query->where('role', $roleFilter);
        }

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $users = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
        $users->appends([
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'per_page' => $perPage,
            'role_filter' => $roleFilter,
            'status_filter' => $statusFilter,
        ]);

        $roles = User::getRoleLabels();

        return view('pengguna.index', compact('users', 'search', 'sortBy', 'sortOrder', 'perPage', 'roleFilter', 'statusFilter', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = User::getRoleLabels();
        $opds = Opd::orderBy('nama_opd')->get();
        return view('pengguna.create', compact('roles', 'opds'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,fo,bo,operator_opd,kepala_opd,verifikator,kadin',
            'opd_id' => 'nullable|exists:opd,id',
            'nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $data = $request->except('password', 'password_confirmation');
        $data['password'] = Hash::make($request->password);

        // Validate OPD for OPD users
        if (in_array($request->role, ['operator_opd', 'kepala_opd']) && !$request->opd_id) {
            return back()->withErrors(['opd_id' => 'OPD harus dipilih untuk role ini.'])->withInput();
        }

        $user = User::create($data);

        // Log activity
        ActivityLog::log(
            'Menambah pengguna baru',
            $user,
            'created',
            [
                'data' => $data,
                'role' => $user->role
            ],
            'user'
        );

        return redirect()->route('pengguna.data.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['opd', 'berita', 'dataSkm'])->findOrFail($id);
        return view('pengguna.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = User::getRoleLabels();
        $opds = Opd::orderBy('nama_opd')->get();
        return view('pengguna.edit', compact('user', 'roles', 'opds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,fo,bo,operator_opd,kepala_opd,verifikator,kadin',
            'opd_id' => 'nullable|exists:opd,id',
            'nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $data = $request->except('password', 'password_confirmation');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Validate OPD for OPD users
        if (in_array($request->role, ['operator_opd', 'kepala_opd']) && !$request->opd_id) {
            return back()->withErrors(['opd_id' => 'OPD harus dipilih untuk role ini.'])->withInput();
        }

        $oldData = $user->toArray();
        $user->update($data);

        // Log activity
        ActivityLog::log(
            'Mengupdate data pengguna',
            $user,
            'updated',
            [
                'old' => $oldData,
                'new' => $data,
                'role' => $user->role
            ],
            'user'
        );

        return redirect()->route('pengguna.data.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus akun Anda sendiri.']);
        }

        // Log activity
        ActivityLog::log(
            'Menghapus pengguna',
            $user,
            'deleted',
            [
                'data' => $user->toArray(),
                'role' => $user->role
            ],
            'user'
        );

        $user->delete();

        return redirect()->route('pengguna.data.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
