<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaduanHandler;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaduanHandlerController extends Controller
{
    /**
     * Display pengaduan handlers management page.
     */
    public function index()
    {
        // Only admin can manage handlers
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengelola petugas pengaduan.');
        }

        $handlers = PengaduanHandler::getActiveHandlers();
        $users = User::where('role', '!=', 'admin')
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        return view('settings.pengaduan-handlers', compact('handlers', 'users'));
    }

    /**
     * Assign user as pengaduan handler.
     */
    public function assign(Request $request)
    {
        // Only admin can assign handlers
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat menetapkan petugas pengaduan.'
            ], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->user_id;
        $assignedBy = Auth::id();

        // Check if user is already assigned
        $existingHandler = PengaduanHandler::where('user_id', $userId)->first();
        if ($existingHandler && $existingHandler->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'User ini sudah ditunjuk sebagai petugas pengaduan.'
            ], 422);
        }

        // Assign handler
        $handler = PengaduanHandler::assignHandler($userId, $assignedBy);

        $user = User::find($userId);

        // Log activity with subject
        ActivityLog::log(
            'Menunjuk petugas pengaduan baru',
            $handler,
            'created',
            [
                'assigned_user_id' => $userId,
                'assigned_user_name' => $user->name,
                'assigned_by' => Auth::user()->name,
            ],
            'pengaduan_handler'
        );

        return response()->json([
            'success' => true,
            'message' => 'Petugas pengaduan berhasil ditunjuk.'
        ]);
    }

    /**
     * Remove user as pengaduan handler.
     */
    public function remove($userId)
    {
        // Only admin can remove handlers
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat menghapus petugas pengaduan.'
            ], 403);
        }

        $handler = PengaduanHandler::where('user_id', $userId)->first();
        if (!$handler) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan sebagai petugas pengaduan.'
            ], 404);
        }

        $userName = $handler->user->name ?? 'Unknown';

        PengaduanHandler::removeHandler($userId);

        // Log activity with subject
        ActivityLog::log(
            'Menghapus petugas pengaduan',
            $handler,
            'deleted',
            [
                'removed_user_id' => $userId,
                'removed_user_name' => $userName,
                'removed_by' => Auth::user()->name,
            ],
            'pengaduan_handler'
        );

        return response()->json([
            'success' => true,
            'message' => 'Petugas pengaduan berhasil dihapus.'
        ]);
    }
}
