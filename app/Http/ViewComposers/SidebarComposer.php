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
            // Collective roles: count perijinan where user can validate at current step
            // Get perijinan IDs where this role is active in validation flow
            $accessiblePerijinanIds = PerijinanValidationFlow::where('role', $user->role)
                ->where('is_active', true)
                ->pluck('perijinan_id')
                ->unique();

            // For "Dalam Proses": count applications that are NOT approved/completed
            // AND where current step matches user's role
            $countDalamProses = DataPerijinan::whereIn('perijinan_id', $accessiblePerijinanIds)
                ->whereNotIn('status', ['approved', 'completed', 'rejected'])
                ->where('status', '!=', 'perbaikan') // Exclude perbaikan from dalam proses
                ->count();

            // For "Perlu Perbaikan": count applications with status 'perbaikan'
            // where user's role is in the validation flow
            $countPerluPerbaikan = DataPerijinan::whereIn('perijinan_id', $accessiblePerijinanIds)
                ->where('status', 'perbaikan')
                ->count();

            // For "Ditolak": count rejected applications
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
                    ->whereNotIn('status', ['approved', 'completed', 'rejected'])
                    ->where('status', '!=', 'perbaikan')
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
