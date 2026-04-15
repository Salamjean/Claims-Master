<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Sinistre;
use Illuminate\Support\Facades\Auth;

class AssuranceSidebarComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        $user = Auth::guard('user')->user();

        $countSinistresNonCloturer = 0;

        if ($user) {
            $countSinistresNonCloturer = Sinistre::where('assurance_id', $user->id)
                ->where('status', '!=', 'cloture')
                ->count();
        }

        $view->with('countSinistresNonCloturer', $countSinistresNonCloturer);
    }
}
