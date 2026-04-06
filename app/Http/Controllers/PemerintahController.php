<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Opd;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PemerintahController extends Controller
{
    /**
     * Government role.
     */
    private $pemerintahRole = 'pemerintah';

    /**
     * Display a listing of pemerintah users.
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $perPage = $request->get('per_page', 10);
        $statusFilter = $request->get('status_filter', 'all');

        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        $allowedSorts = ['name', 'email', 'role', 'status', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'name';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'asc';

        $query = User::where('role', $this->pemerintahRole)->with('opd');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
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
            'status_filter' => $statusFilter,
        ]);

        // Statistics
        $totalPemerintah = User::where('role', $this->pemerintahRole)->count();
        $aktifCount = User::where('role', $this->pemerintahRole)->where('status', 'aktif')->count();

        return view('pemerintah.index', compact('users', 'search', 'sortBy', 'sortOrder', 'perPage', 'statusFilter', 'totalPemerintah', 'aktifCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $opds = Opd::orderBy('nama_opd')->get();
        return view('pemerintah.create', compact('opds'));
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
            'opd_id' => 'required|exists:opd,id',
            'nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $data = $request->except('password', 'password_confirmation');
        $data['password'] = Hash::make($request->password);
        $data['role'] = $this->pemerintahRole;

        $user = User::create($data);

        // Log activity
        ActivityLog::log(
            'Menambah pengguna pemerintah baru',
            $user,
            'created',
            [
                'data' => $data,
                'role' => $user->role
            ],
            'pemerintah'
        );

        return redirect()->route('pemerintah.index')
            ->with('success', 'Pengguna pemerintah berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if ($user->role !== $this->pemerintahRole) {
            abort(403, 'User bukan role pemerintah.');
        }

        $user->load('opd');
        return view('pemerintah.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        if ($user->role !== $this->pemerintahRole) {
            abort(403, 'User bukan role pemerintah.');
        }

        $opds = Opd::orderBy('nama_opd')->get();
        return view('pemerintah.edit', compact('user', 'opds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if ($user->role !== $this->pemerintahRole) {
            abort(403, 'User bukan role pemerintah.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'opd_id' => 'nullable|exists:opd,id',
            'nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $data = $request->except('password', 'password_confirmation');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Ensure role stays as pemerintah
        $data['role'] = $this->pemerintahRole;

        $oldData = $user->toArray();
        $user->update($data);

        // Log activity
        ActivityLog::log(
            'Mengupdate data pengguna pemerintah',
            $user,
            'updated',
            [
                'old' => $oldData,
                'new' => $data,
                'role' => $user->role
            ],
            'pemerintah'
        );

        return redirect()->route('pemerintah.index')
            ->with('success', 'Pengguna pemerintah berhasil diperbarui.');
    }

    /**
     * Update the status of pemerintah user.
     */
    public function updateStatus(Request $request, User $user)
    {
        if ($user->role !== $this->pemerintahRole) {
            abort(403, 'User bukan role pemerintah.');
        }

        $request->validate([
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $oldStatus = $user->status;
        $user->update([
            'status' => $request->status,
        ]);

        // Log activity
        ActivityLog::log(
            'Mengubah status pengguna pemerintah',
            $user,
            'updated',
            [
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'user_name' => $user->name,
            ],
            'pemerintah'
        );

        $message = $request->status === 'aktif'
            ? 'Pengguna pemerintah berhasil diaktifkan.'
            : 'Status pengguna pemerintah diubah menjadi tidak aktif.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'id' => $user->id,
                    'status' => $user->status,
                ]
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->role !== $this->pemerintahRole) {
            abort(403, 'User bukan role pemerintah.');
        }

        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $userData = $user->toArray();
        $user->delete();

        // Log activity
        ActivityLog::log(
            'Menghapus pengguna pemerintah',
            null,
            'deleted',
            [
                'deleted_data' => $userData,
                'user_name' => $userData['name'],
                'user_role' => $userData['role'],
            ],
            'pemerintah'
        );

        return redirect()->route('pemerintah.index')->with('success', 'Pengguna pemerintah berhasil dihapus.');
    }
}
