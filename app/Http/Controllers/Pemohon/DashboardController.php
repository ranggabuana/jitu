<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the pemohon dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Statistics - placeholder values (will be updated when applications table exists)
        $stats = [
            'total' => 0,
            'in_progress' => 0,
            'needs_fix' => 0,
            'completed' => 0,
        ];

        // Sample messages (placeholder)
        $messages = [];

        // Empty applications (no applications table yet)
        $recentApplications = collect();

        return view('pemohon.dashboard.index', compact(
            'user',
            'stats',
            'recentApplications',
            'messages'
        ));
    }

    /**
     * Show application details.
     */
    public function showApplication($id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Fitur ini akan tersedia segera. Tabel aplikasi belum dibuat.'
        ]);
    }
}
