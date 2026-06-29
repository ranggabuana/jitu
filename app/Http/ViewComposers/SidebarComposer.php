<?php

namespace App\Http\ViewComposers;

use App\Models\DataPerijinan;
use App\Models\PerijinanValidationFlow;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SidebarComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        $user = Auth::user();

        // Count based on user role and access
        if (!$user) {
            $countDalamProses = 0;
            $countPerluPerbaikan = 0;
            $countDitolak = 0;
            $countSelesai = 0;
        } elseif ($user->role === 'admin') {
            // Admin sees all
            $countDalamProses = DataPerijinan::whereNotIn('status', ['approved', 'diperbaiki', 'diperpanjang', 'completed', 'rejected', 'perbaikan'])->count();
            $countPerluPerbaikan = DataPerijinan::where('status', 'perbaikan')->count();
            $countDitolak = DataPerijinan::where('status', 'rejected')->count();
            $countSelesai = DataPerijinan::whereIn('status', ['approved', 'diperbaiki'])->count();
        } else {
            // All other roles (FO, BO, Verifikator, Kadin, Opd, etc.): 
            // Count only perijinan where user is SPECIFICALLY assigned in validation flow
            $accessiblePerijinanIds = PerijinanValidationFlow::where('assigned_user_id', $user->id)
                ->where('is_active', true)
                ->pluck('perijinan_id')
                ->unique();

            if ($accessiblePerijinanIds->isEmpty()) {
                $countDalamProses = 0;
                $countPerluPerbaikan = 0;
                $countDitolak = 0;
                $countSelesai = 0;
            } else {
                $countDalamProses = DataPerijinan::whereIn('perijinan_id', $accessiblePerijinanIds)
                    ->whereNotIn('status', ['approved', 'diperbaiki', 'diperpanjang', 'completed', 'rejected'])
                    ->where('status', '!=', 'perbaikan')
                    ->count();

                $countPerluPerbaikan = DataPerijinan::whereIn('perijinan_id', $accessiblePerijinanIds)
                    ->where('status', 'perbaikan')
                    ->count();

                $countDitolak = DataPerijinan::whereIn('perijinan_id', $accessiblePerijinanIds)
                    ->where('status', 'rejected')
                    ->count();

                $countSelesai = DataPerijinan::whereIn('perijinan_id', $accessiblePerijinanIds)
                    ->whereIn('status', ['approved', 'diperbaiki'])
                    ->count();
            }
        }

        $view->with('countDalamProses', $countDalamProses);
        $view->with('countPerluPerbaikan', $countPerluPerbaikan);
        $view->with('countDitolak', $countDitolak);
        $view->with('countSelesai', $countSelesai);
    }
}
