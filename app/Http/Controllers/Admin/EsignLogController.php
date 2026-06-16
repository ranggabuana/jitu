<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EsignLog;
use Illuminate\Http\Request;

class EsignLogController extends Controller
{
    public function index(Request $request)
    {
        $query = EsignLog::with(['user', 'dataPerijinan.perijinan'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('dataPerijinan', function($q) use ($search) {
                $q->where('no_registrasi', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(15)->withQueryString();

        return view('admin.settings.log-tte', compact('logs'));
    }
}
