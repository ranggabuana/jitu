<?php

namespace App\Http\ViewComposers;

use App\Models\DataPerijinan;
use Illuminate\View\View;

class SidebarComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        // Count applications that need validation (in progress)
        $countDalamProses = DataPerijinan::whereNotIn('status', ['approved', 'completed'])->count();
        
        $view->with('countDalamProses', $countDalamProses);
    }
}
