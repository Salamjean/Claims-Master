<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\Sinistre;
use App\Models\SinistreDocumentAttendu;
use App\Models\User;
use App\Notifications\DocumentRejectedNotification;
use App\Services\AIService;
use App\Services\YellikaSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class PersonnelDashboardController extends Controller
{
    protected YellikaSmsService $sms;

    public function __construct(YellikaSmsService $sms)
    {
        $this->sms = $sms;
    }
    /**
     * Tableau de bord du personnel
     */
    public function dashboard()
    {
        $personnel   = auth('user')->user();
        $assuranceId = $personnel->assurance_id;

        // Stats sinistres de l'assurance
        $totalSinistres     = Sinistre::where('assurance_id', $assuranceId)->count();
        $sinistresEnAttente = Sinistre::where('assurance_id', $assuranceId)
            ->where('status', 'soumis')->count();
        $sinistresEnCours   = Sinistre::where('assurance_id', $assuranceId)
            ->whereIn('status', ['en_cours', 'documents_soumis', 'expertise_en_cours'])->count();
        $sinistresCloturer  = Sinistre::where('assurance_id', $assuranceId)
            ->where('status', 'cloture')->count();

        // Pool général : sinistres non encore récupérés par un personnel (hors clôturés)
        $pool = Sinistre::with('assure')
            ->where('assurance_id', $assuranceId)
            ->whereNull('assigned_personnel_id')
            ->where('status', '!=', 'cloture')
            ->latest()
            ->get();

        $totalNonRecuperes = $pool->count();

        return view('personnel.dashboard', compact(
            'personnel',
            'totalSinistres',
            'totalNonRecuperes',
            'sinistresEnAttente',
            'sinistresEnCours',
            'sinistresCloturer',
            'pool'
        ));
    }

    /**
     * Page de recherche globale (tous les sinistres de l'assurance)
     */
    public function search(Request $request)
    {
        $personnel   = auth('user')->user();
        $assuranceId = $personnel->assurance_id;

        $fAssure  = trim($request->get('f_assure', ''));
        $fType    = trim($request->get('f_type', ''));
        $fNumero  = trim($request->get('f_numero', ''));
        $hasFilter = $fAssure || $fType || $fNumero;

        $resultats = null;

        if ($hasFilter) {
            $resultats = Sinistre::with(['assure', 'documentsAttendus', 'assignedPersonnel'])
                ->where('assurance_id', $assuranceId)
                ->when($fAssure, fn($q) => $q->whereHas(
                    'assure',
                    fn($a) =>
                    $a->where('name', 'like', "%{$fAssure}%")
                        ->orWhere('prenom', 'like', "%{$fAssure}%")
                ))
                ->when($fType,   fn($q) => $q->where('type_sinistre', 'like', "%{$fType}%"))
                ->when($fNumero, fn($q) => $q->where('numero_sinistre', 'like', "%{$fNumero}%"))
                ->latest()
                ->paginate(20)
                ->withQueryString();
        }

        return view('personnel.search', compact('fAssure', 'fType', 'fNumero', 'hasFilter', 'resultats', 'personnel'));
    }

    /**
     * Mes dossiers récupérés
     */
    public function mesDossiers(Request $request)
    {
        $personnel   = auth('user')->user();
        $assuranceId = $personnel->assurance_id;

        $fAssure  = trim($request->get('f_assure', ''));
        $fType    = trim($request->get('f_type', ''));
        $fNumero  = trim($request->get('f_numero', ''));

        $mesDossiers = Sinistre::with(['assure', 'documentsAttendus'])
            ->where('assurance_id', $assuranceId)
            ->where('assigned_personnel_id', $personnel->id)
            ->where('status', '!=', 'cloture')
            ->when($fAssure, fn($q) => $q->whereHas(
                'assure',
                fn($a) =>
                $a->where('name', 'like', "%{$fAssure}%")
                    ->orWhere('prenom', 'like', "%{$fAssure}%")
            ))
            ->when($fType, fn($q) => $q->where('type_sinistre', 'like', "%{$fType}%"))
            ->when($fNumero, fn($q) => $q->where('numero_sinistre', 'like', "%{$fNumero}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $hasFilter = $fAssure || $fType || $fNumero;

        return view('personnel.mes-dossiers', compact('mesDossiers', 'fAssure', 'fType', 'fNumero', 'hasFilter'));
    }

    /**
     * Récupérer un sinistre pour traitement
     */
    public function claim(Sinistre $sinistre)
    {
        $personnel = auth('user')->user();

        abort_if($sinistre->assurance_id !== $personnel->assurance_id, 403);
        abort_if($sinistre->assigned_personnel_id !== null, 409);

        $sinistre->update([
            'assigned_personnel_id' => $personnel->id,
            'assigned_personnel_at' => now(),
        ]);

        return back()->with('success', 'Dossier récupéré avec succès. Il apparaît maintenant dans vos dossiers.');
    }

    /**
     * Remettre un sinistre dans le pool
     */
    public function release(Sinistre $sinistre)
    {
        $personnel = auth('user')->user();

        abort_if($sinistre->assigned_personnel_id !== $personnel->id, 403);

        $sinistre->update([
            'assigned_personnel_id' => null,
            'assigned_personnel_at' => null,
        ]);

        return back()->with('success', 'Dossier remis dans le pool général.');
    }

    /**
     * Liste des sinistres (avec pagination)
     */
    public function sinistres(Request $request)
    {
        $personnel   = auth('user')->user();
        $assuranceId = $personnel->assurance_id;

        $query = Sinistre::with('assure')
            ->where('assurance_id', $assuranceId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_sinistre', 'like', "%{$search}%")
                    ->orWhereHas('assure', fn($sq) => $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%"));
            });
        }

        $sinistres = $query->latest()->paginate(15)->withQueryString();

        return view('personnel.sinistres.index', compact('sinistres'));
    }

    /**
     * Détail d'un sinistre (lecture seule)
     */
    public function showSinistre(Sinistre $sinistre)
    {
        $personnel   = auth('user')->user();
        $assuranceId = $personnel->assurance_id;

        abort_if($sinistre->assurance_id !== $assuranceId, 403);

        $sinistre->load('assure', 'documents', 'expert', 'garage', 'constat');

        return view('personnel.sinistres.show', compact('sinistre'));
    }

    /**
     * Page d'examen complète d'un sinistre (mêmes actions que l'assurance)
     */
    public function reviewSinistre(Sinistre $sinistre)
    {
        $personnel   = auth('user')->user();
        $assuranceId = $personnel->assurance_id;

        abort_if($sinistre->assurance_id !== $assuranceId, 403);
        abort_if($sinistre->assigned_personnel_id !== $personnel->id, 403);

        $sinistre->load(['assure', 'documentsAttendus.documentsSoumis', 'expert', 'garage']);

        $experts = User::where('role', 'expert')->where('assurance_id', $assuranceId)->get();
        $garages = User::where('role', 'garage')->where('assurance_id', $assuranceId)->get();

        return view('personnel.sinistres.review', compact('sinistre', 'experts', 'garages'));
    }

    /**
     * Override décision IA sur un document
     */
    public function reviewDoc(Request $request, Sinistre $sinistre, SinistreDocumentAttendu $documentAttendu)
    {
        abort_if($sinistre->assurance_id !== auth('user')->user()->assurance_id, 403);

        $request->validate([
            'override_status' => 'required|in:valid,invalid,pending',
            'feedback'        => 'nullable|string',
        ]);

        $dernierSoumis = $documentAttendu->documentsSoumis()->latest()->first();

        if ($dernierSoumis) {
            $dernierSoumis->manager_override_status = ($request->override_status === 'pending') ? null : $request->override_status;

            if ($request->filled('feedback')) {
                $dernierSoumis->ai_feedback = "Observation du gestionnaire : " . $request->feedback;
            }
            $dernierSoumis->save();

            if (in_array($request->override_status, ['invalid', 'pending'])) {
                $documentAttendu->status_client = 'pending';
                $documentAttendu->save();

                if ($request->override_status === 'invalid') {
                    Notification::send($sinistre->assure, new DocumentRejectedNotification($sinistre, $documentAttendu, $request->feedback));

                    if (!empty($sinistre->assure->contact)) {
                        $smsMessage = "CLAIMS MASTER: Votre document (" . $documentAttendu->nom_document . ") pour le sinistre #" . $sinistre->id . " a ete rejete. Veuillez vous connecter pour le soumettre a nouveau.";
                        $this->sms->sendSms($sinistre->assure->contact, $smsMessage);
                    }
                }
            }
        }

        return back()->with('success', 'Le statut du document a été mis à jour.');
    }

    /**
     * Vérification des garanties
     */
    public function verifyGaranties(Request $request, Sinistre $sinistre)
    {
        abort_if($sinistre->assurance_id !== auth('user')->user()->assurance_id, 403);

        $request->validate([
            'est_couvert' => 'required|boolean',
            'motif_rejet' => 'nullable|string|required_if:est_couvert,0',
        ]);

        $sinistre->est_couvert = $request->est_couvert;
        if (!$request->est_couvert) {
            $sinistre->motif_rejet    = $request->motif_rejet;
            $sinistre->status         = 'cloture';
            $sinistre->workflow_step  = 'rejected_no_warranty';
        } else {
            $sinistre->workflow_step = 'warranty_verified_pending_assignment';
            if (!$sinistre->numero_sinistre) {
                $sinistre->numero_sinistre = 'SIN-' . date('Ymd') . '-' . str_pad($sinistre->id, 4, '0', STR_PAD_LEFT);
            }
        }
        $sinistre->save();

        return back()->with('success', 'La vérification des garanties a été enregistrée.');
    }

    /**
     * Orientation Expert / Garage
     */
    public function assignExpertGarage(Request $request, Sinistre $sinistre)
    {
        abort_if($sinistre->assurance_id !== auth('user')->user()->assurance_id, 403);

        $request->validate([
            'expert_id' => 'nullable|exists:users,id',
            'garage_id' => 'nullable|exists:users,id',
        ]);

        if ($request->filled('expert_id')) {
            $sinistre->expert_id          = $request->expert_id;
            $sinistre->date_mandat_expert = now();
        }
        if ($request->filled('garage_id')) {
            $sinistre->garage_id = $request->garage_id;
            $sinistre->status    = 'en_cours';
        }
        $sinistre->workflow_step = 'expert_garage_assigned';
        $sinistre->save();

        return back()->with('success', "L'expert et/ou le garage ont été assignés.");
    }

    /**
     * Décision finale sur le dossier
     */
    public function decision(Request $request, Sinistre $sinistre)
    {
        abort_if($sinistre->assurance_id !== auth('user')->user()->assurance_id, 403);

        $request->validate([
            'decision'        => 'required|in:valide,rejete,complement_requis',
            'message_client'  => 'nullable|string',
        ]);

        if ($request->decision === 'valide') {
            $sinistre->status        = 'cloture';
            $sinistre->workflow_step = 'closed_validated';
        } elseif ($request->decision === 'rejete') {
            $sinistre->status        = 'cloture';
            $sinistre->workflow_step = 'closed_rejected';
        } elseif ($request->decision === 'complement_requis') {
            $sinistre->status        = 'en_attente';
            $sinistre->workflow_step = 'docs_pending';

            $docsManquants = $sinistre->documentsAttendus()
                ->where(function ($q) {
                    $q->where('status_client', 'pending')
                        ->orWhereHas('documentsSoumis', fn($sq) => $sq->where('manager_override_status', 'invalid'));
                })->get();

            if ($docsManquants->count() > 0) {
                $aiService = new AIService();
                $docNames  = $docsManquants->pluck('nom_document')->toArray();
                $aiMessage = $aiService->generateDocumentRequestMessage($sinistre, $docNames);

                Notification::send($sinistre->assure, new \App\Notifications\DocumentsRequisNotification($sinistre, $docsManquants, $aiMessage));

                if (!empty($sinistre->assure->contact)) {
                    $this->sms->sendSms($sinistre->assure->contact, "CLAIMS MASTER: Des complements sont requis pour votre sinistre #" . $sinistre->id . ". " . substr($aiMessage, 0, 100));
                }
            }
        }

        $sinistre->save();

        return redirect()->route('personnel.mes-dossiers')->with('success', 'La décision a été enregistrée avec succès.');
    }

    /**
     * Afficher le profil
     */
    public function profile()
    {
        $personnel = auth('user')->user();
        return view('personnel.profile', compact('personnel'));
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(Request $request)
    {
        $personnel = auth('user')->user();

        $request->validate([
            'name'    => 'required|string|max:255',
            'prenom'  => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:30',
            'adresse' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($personnel->profile_picture) {
                Storage::disk('public')->delete($personnel->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $personnel->profile_picture = $path;
        }

        $personnel->name    = $request->name;
        $personnel->prenom  = $request->prenom;
        $personnel->contact = $request->contact;
        $personnel->adresse = $request->adresse;
        $personnel->save();

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Afficher le formulaire de changement de mot de passe
     */
    public function showChangePassword()
    {
        return view('personnel.password');
    }

    /**
     * Traiter le changement de mot de passe
     */
    public function updatePassword(Request $request)
    {
        $personnel = auth('user')->user();

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ], [
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        if (!Hash::check($request->current_password, $personnel->password)) {
            return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
        }

        $personnel->password             = Hash::make($request->password);
        $personnel->must_change_password = false;
        $personnel->save();

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        auth('user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login')->with('success', 'Déconnexion réussie.');
    }

    /**
     * Ajouter un document attendu pour ce sinistre.
     */
    public function addDocumentAttendu(Request $request, Sinistre $sinistre)
    {
        $personnel = auth('user')->user();
        abort_if($sinistre->assurance_id !== $personnel->assurance_id, 403);
        abort_if($sinistre->assigned_personnel_id !== $personnel->id, 403);

        $request->validate([
            'nom_document' => 'required|string|max:255',
            'type_champ'   => 'required|in:file,text',
        ]);

        SinistreDocumentAttendu::create([
            'sinistre_id'   => $sinistre->id,
            'nom_document'  => $request->nom_document,
            'type_champ'    => $request->type_champ,
            'is_mandatory'  => true,
            'status_client' => 'pending',
        ]);

        return back()->with('success', 'Document ajouté avec succès.');
    }

    /**
     * Supprimer un document attendu de ce sinistre.
     */
    public function removeDocumentAttendu(Sinistre $sinistre, SinistreDocumentAttendu $documentAttendu)
    {
        $personnel = auth('user')->user();
        abort_if($documentAttendu->sinistre_id !== $sinistre->id, 403);
        abort_if($sinistre->assurance_id !== $personnel->assurance_id, 403);

        $documentAttendu->delete();

        return back()->with('success', 'Document supprimé.');
    }
}
