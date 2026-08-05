<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Sinistre;
use Illuminate\Support\Facades\Auth;

class GroupeNavbarComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = Auth::guard('user')->user();
        $interventionsEnCoursCount = 0;

        if ($user) {
            $interventionsEnCoursCount = Sinistre::where('assigned_groupe_id', $user->id)
                ->where('hospital_status', '!=', 'termine')
                ->count();
        }

        $view->with('interventionsEnCoursCount', $interventionsEnCoursCount);
    }
}
