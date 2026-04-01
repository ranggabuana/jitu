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
        } elseif ($user->role === 'admin') {
            // Admin sees all
            $countDalamProses = DataPerijinan::whereNotIn('status', ['approved', 'completed'])->count();
            $countPerluPerbaikan = DataPerijinan::where('status', 'perbaikan')->count();
            $countDitolak = DataPerijinan::where('status', 'rejected')->count();
        } elseif (in_array($user->role, ['fo', 'bo', 'verifikator', 'kadin'])) {
            // Collective roles: count perijinan with their role in validation flow
            $accessiblePerijinanIds = PerijinanValidationFlow::whereIn('role', ['fo', 'bo', 'verifikator', 'kadin'])
                ->where('is_active', true)
                ->pluck('perijinan_id')
                ->unique();

            $countDalamProses = DataPerijinan::whereIn('perijinan_id', $accessiblePerijinanIds)
                ->whereNotIn('status', ['approved', 'completed'])
                ->count();
            
            $countPerluPerbaikan = DataPerijinan::whereIn('perijinan_id', $accessiblePerijinanIds)
                ->where('status', 'perbaikan')
                ->count();
            
            $countDitolak = DataPerijinan::whereIn('perijinan_id', $accessiblePerijinanIds)
                ->where('status', 'rejected')
                ->count();
        } else {
            // Assigned roles (Operator OPD, Kepala OPD): count only assigned perijinan
            $accessiblePerijinanIds = PerijinanValidationFlow::where('assigned_user_id', $user->id)
                ->where('is_active', true)
                ->pluck('perijinan_id')
                ->unique();

            if ($accessiblePerijinanIds->isEmpty()) {
                $countDalamProses = 0;
                $countPerluPerbaikan = 0;
                $countDitolak = 0;
            } else {
                $countDalamProses = DataPerijinan::whereIn('perijinan_id', $accessiblePerijinanIds)
                    ->whereNotIn('status', ['approved', 'completed'])
                    ->count();
                
                $countPerluPerbaikan = DataPerijinan::whereIn('perijinan_id', $accessiblePerijinanIds)
                    ->where('status', 'perbaikan')
                    ->count();
                
                $countDitolak = DataPerijinan::whereIn('perijinan_id', $accessiblePerijinanIds)
                    ->where('status', 'rejected')
                    ->count();
            }
        }

        $view->with('countDalamProses', $countDalamProses);
        $view->with('countPerluPerbaikan', $countPerluPerbaikan);
        $view->with('countDitolak', $countDitolak);
    }
}
