<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\EmailService;
use App\Mail\AccountActivatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PemohonController extends Controller
{
    /**
     * Display a listing of pemohon users.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'pemohon');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by status_pemohon (perorangan/badan_usaha)
        if ($request->filled('status_pemohon')) {
            $query->where('status_pemohon', $request->status_pemohon);
        }

        $pemohon = $query->orderBy('status', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Statistics
        $totalPemohon = User::where('role', 'pemohon')->count();
        $peroranganCount = User::where('role', 'pemohon')
            ->where('status_pemohon', 'perorangan')
            ->count();
        $badanUsahaCount = User::where('role', 'pemohon')
            ->where('status_pemohon', 'badan_usaha')
            ->count();

        // Log activity
        ActivityLog::log(
            'Melihat daftar pemohon',
            null,
            'viewed',
            [
                'search' => $request->search,
                'status_filter' => $request->status,
                'status_pemohon_filter' => $request->status_pemohon,
            ],
            'pemohon'
        );

        return view('pemohon.index', compact('pemohon', 'totalPemohon', 'peroranganCount', 'badanUsahaCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pemohon.create');
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
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'status_pemohon' => 'required|in:perorangan,badan_usaha',
            'nama_perusahaan' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:50',
            'nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,tidak_aktif',
        ], [
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $data = $request->except('password', 'password_confirmation');
        $data['password'] = Hash::make($request->password);
        $data['role'] = 'pemohon';

        $user = User::create($data);

        // Log activity
        ActivityLog::log(
            'Menambah pemohon baru',
            $user,
            'created',
            [
                'data' => $data,
            ],
            'pemohon'
        );

        return redirect()->route('pemohon.index')
            ->with('success', 'Pemohon berhasil ditambahkan.');
    }

    /**
     * Display the specified pemohon.
     */
    public function show(User $pemohon)
    {
        // Load wilayah relationships
        $pemohon->load(['provinsi', 'kabupaten', 'kecamatan', 'kelurahan']);

        // Log activity
        ActivityLog::log(
            'Melihat detail pemohon',
            $pemohon,
            'viewed',
            [
                'pemohon_name' => $pemohon->name,
                'pemohon_username' => $pemohon->username,
            ],
            'pemohon'
        );

        return view('pemohon.show', compact('pemohon'));
    }

    /**
     * Show the form for editing the specified pemohon.
     */
    public function edit(User $pemohon)
    {
        return view('pemohon.edit', compact('pemohon'));
    }

    /**
     * Update the specified pemohon in storage.
     */
    public function update(Request $request, User $pemohon)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($pemohon->id)],
            'username' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($pemohon->id)],
            'nip' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]{16}$/',
                \Illuminate\Validation\Rule::unique('users')->ignore($pemohon->id),
            ],
            'password' => [
                'nullable',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'status_pemohon' => 'required|in:perorangan,badan_usaha',
            'nama_perusahaan' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'status' => 'required|in:aktif,tidak_aktif',
            'provinsi_id' => 'nullable|exists:provinsis,id',
            'kabupaten_id' => 'nullable|exists:kabupatens,id',
            'kecamatan_id' => 'nullable|exists:kecamatans,id',
            'kelurahan_id' => 'nullable|exists:kelurahans,id',
            'alamat_ktp' => 'nullable|string|max:500',
            'alamat_domisili' => 'nullable|string|max:500',
            'foto_ktp' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ], [
            'nip.required' => 'NIK harus diisi.',
            'nip.size' => 'NIK harus terdiri dari 16 digit.',
            'nip.regex' => 'NIK hanya boleh berisi angka.',
            'nip.unique' => 'NIK sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $data = $request->except('password', 'password_confirmation', 'foto_ktp');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle KTP file upload
        if ($request->hasFile('foto_ktp')) {
            // Delete old file if exists
            if ($pemohon->foto_ktp && file_exists(public_path($pemohon->foto_ktp))) {
                @unlink(public_path($pemohon->foto_ktp));
            }

            $file = $request->file('foto_ktp');
            $extension = $file->getClientOriginalExtension();
            $filename = 'ktp_' . time() . '_' . $request->nip . '.' . $extension;
            $uploadPath = public_path('uploads/register');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $file->move($uploadPath, $filename);
            $data['foto_ktp'] = 'uploads/register/' . $filename;
        }

        $oldData = $pemohon->toArray();
        $pemohon->update($data);

        // Log activity
        ActivityLog::log(
            'Mengupdate data pemohon',
            $pemohon,
            'updated',
            [
                'old' => $oldData,
                'new' => $data,
            ],
            'pemohon'
        );

        return redirect()->route('pemohon.index')
            ->with('success', 'Data pemohon berhasil diperbarui.');
    }

    /**
     * Update the status of pemohon.
     */
    public function updateStatus(Request $request, User $pemohon)
    {
        $request->validate([
            'status' => 'required|in:aktif,tidak_aktif',
        ]);

        $oldStatus = $pemohon->status;
        $pemohon->update([
            'status' => $request->status,
        ]);

        // Send notification email if account is activated
        if ($oldStatus !== 'aktif' && $request->status === 'aktif') {
            EmailService::send(
                to: $pemohon->email,
                name: $pemohon->name,
                mailable: new AccountActivatedNotification($pemohon)
            );
        }

        // Log activity
        ActivityLog::log(
            'Mengubah status pemohon',
            $pemohon,
            'updated',
            [
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'pemohon_name' => $pemohon->name,
            ],
            'pemohon'
        );

        $message = $request->status === 'aktif'
            ? 'Pemohon berhasil diaktifkan.'
            : 'Status pemohon diubah menjadi tidak aktif.';

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'id' => $pemohon->id,
                    'status' => $pemohon->status,
                ]
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified pemohon from storage.
     */
    public function destroy(User $pemohon)
    {
        // Prevent deleting self
        if ($pemohon->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Store pemohon data for logging
        $pemohonData = $pemohon->toArray();

        $pemohon->delete();

        // Log activity
        ActivityLog::log(
            'Menghapus pemohon',
            null,
            'deleted',
            [
                'deleted_data' => $pemohonData,
                'pemohon_name' => $pemohonData['name'],
                'pemohon_username' => $pemohonData['username'],
            ],
            'pemohon'
        );

        return redirect()->route('pemohon.index')->with('success', 'Data pemohon berhasil dihapus.');
    }
}
