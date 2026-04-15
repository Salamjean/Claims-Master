<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Sinistre;
use Illuminate\Support\Facades\Auth;

class PersonnelSidebarComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        $user = Auth::guard('user')->user();

        $countMesDossiers       = 0;
        $countDocsIncomplets    = 0;

        if ($user) {
            // Dossiers récupérés par ce personnel
            $mesDossiers = Sinistre::with('documentsAttendus')
                ->where('assigned_personnel_id', $user->id)
                ->whereNotIn('workflow_step', ['closed_validated', 'closed_rejected', 'rejected_no_warranty'])
                ->get();

            $countMesDossiers = $mesDossiers->count();

            // Parmi ces dossiers, ceux avec au moins un document manquant
            $countDocsIncomplets = $mesDossiers->filter(function ($sinistre) {
                return $sinistre->documentsAttendus->where('status_client', 'pending')->count() > 0;
            })->count();
        }

        $view->with([
            'sidebarCountMesDossiers'    => $countMesDossiers,
            'sidebarCountDocsIncomplets' => $countDocsIncomplets,
        ]);
    }
}
