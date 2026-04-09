<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Sinistre;
use Illuminate\Support\Facades\Auth;

class AssureSidebarComposer
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
        
        $countSuivi = 0;
        $countEnCours = 0;
        $countHistoriquePending = 0;
        $countMesSinistresTotal = 0;

        if ($user) {
            $userId = $user->id;

            // Count for "Suivi" (Waiting for agent assignment/service action)
            $countSuivi = Sinistre::where('user_id', $userId)
                ->where('status', 'en_attente')
                ->count();

            // Count for "En cours" (Actively being handled by service)
            $countEnCours = Sinistre::where('user_id', $userId)
                ->where('status', 'en_cours')
                ->count();

            // Count for "Historique" (Treated by service, but not yet closed by insurance)
            $countHistoriquePending = Sinistre::where('user_id', $userId)
                ->where('status', 'traite')
                ->count();

            // Total for "Mes Sinistres" parent menu (Everything active/pending)
            $countMesSinistresTotal = $countSuivi + $countEnCours + $countHistoriquePending;

            $view->with([
                'countSuivi' => $countSuivi,
                'countEnCours' => $countEnCours,
                'countHistoriquePending' => $countHistoriquePending,
                'countMesSinistresTotal' => $countMesSinistresTotal,
            ]);
        }
    }
}
