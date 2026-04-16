@extends('assurance.layouts.template')

@section('title', 'Révision du Dossier Sinistre')

@section('content')
    <div class=" mx-auto" style="width:80%">

        {{-- En-tête --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-400 mb-2">
                    <a href="{{ route('assurance.sinistres.index') }}" class="hover:text-slate-600 transition-colors">Dossiers
                        Sinistres</a>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                    <span class="text-slate-600 font-semibold">Examen #{{ $sinistre->id }}</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-800">Dossier : {{ str_replace('_', ' ', $sinistre->type_sinistre) }}
                </h1>
                <p class="text-slate-500 mt-1">
                    Déclaré le {{ $sinistre->created_at->format('d/m/Y') }} par <span
                        class="font-semibold text-slate-700">{{ $sinistre->assure->name }}</span>
                </p>
            </div>
            <div>
                @if ($sinistre->status === 'cloture' && $sinistre->workflow_step === 'closed_validated')
                    <span
                        class="px-4 py-2 bg-emerald-100 text-emerald-700 font-bold rounded-xl text-sm border border-emerald-200">Validé</span>
                @elseif($sinistre->status === 'cloture' && $sinistre->workflow_step === 'closed_rejected')
                    <span
                        class="px-4 py-2 bg-red-100 text-red-700 font-bold rounded-xl text-sm border border-red-200">Rejeté</span>
                @elseif($sinistre->status === 'traite')
                    <span
                        class="px-4 py-2 bg-indigo-100 text-indigo-700 font-bold rounded-xl text-sm border border-indigo-200">Constat
                        Terminé (À Valider)</span>
                @else
                    <span
                        class="px-4 py-2 bg-yellow-100 text-yellow-700 font-bold rounded-xl text-sm border border-yellow-200">En
                        cours / Révision</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Colonne Infos & Claims AI --}}
            <div class="lg:col-span-1 space-y-6">

                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-file-lines text-slate-400"></i> Description du client
                    </h3>
                    <p class="text-slate-600 text-sm italic bg-slate-50 p-4 rounded-xl border border-slate-100">
                        "{{ $sinistre->description ?? 'Aucune description fournie.' }}"
                    </p>

                    @if ($sinistre->photos)
                        <div class="mt-4 flex gap-2 overflow-x-auto pb-2">
                            @foreach ($sinistre->photos as $photo)
                                <a href="{{ Storage::url($photo) }}" target="_blank"
                                    class="block w-16 h-16 rounded-lg overflow-hidden border border-slate-200 shrink-0 hover:opacity-80 transition-opacity">
                                    <img src="{{ Storage::url($photo) }}" class="w-full h-full object-cover">
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if ($sinistre->ai_analysis_report)
                    <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100 shadow-sm relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 text-indigo-100/50 text-7xl"><i
                                class="fa-solid fa-microchip"></i></div>
                        <div class="relative z-10">
                            <h3 class="font-bold text-indigo-900 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-robot text-indigo-500"></i> Analyse de Claims AI (Texte)
                            </h3>

                            <div class="space-y-4 text-sm">
                                <div>
                                    <span class="text-indigo-400 uppercase text-xs font-bold block mb-1">Gravité
                                        estimée</span>
                                    @php $grav = strtolower($sinistre->ai_analysis_report['gravity'] ?? ''); @endphp
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded bg-white text-indigo-700 font-semibold border border-indigo-200">
                                        {{ ucfirst($grav) }}
                                    </span>
                                </div>

                                <div>
                                    <span class="text-indigo-400 uppercase text-xs font-bold block mb-1">Contexte
                                        dégagé</span>
                                    <p class="text-indigo-800 font-medium">
                                        {{ $sinistre->ai_analysis_report['context'] ?? 'Non fourni' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- WORKFLOW: Étape 3 (Garanties) et Étape 4/5 (Mandatement) --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-tasks text-slate-400"></i> Traitement du Dossier
                    </h3>

                    {{-- Vérification Garanties --}}
                    <div class="mb-6 p-4 border border-slate-200 rounded-xl bg-slate-50">
                        <h4 class="font-bold text-sm mb-3">1. Vérification des garanties</h4>
                        @if (in_array($sinistre->workflow_step, [
                                'warranty_verified_pending_assignment',
                                'expert_garage_assigned',
                                'closed_validated',
                            ]))
                            <div class="text-sm text-emerald-600 font-semibold mb-2"><i
                                    class="fa-solid fa-check-circle"></i> Garanties validées, sinistre couvert.</div>
                        @else
                            <form action="{{ route('assurance.sinistres.verify_garanties', $sinistre->id) }}"
                                method="POST">
                                @csrf
                                <div class="space-y-3">
                                    <select name="est_couvert"
                                        class="w-full h-10 px-3 rounded-lg text-sm border-slate-300 focus:border-primary-500"
                                        required
                                        onchange="document.getElementById('rejet_box').classList.toggle('hidden', this.value === '1')">
                                        <option value="">Le sinistre est-il couvert ?</option>
                                        <option value="1">Oui, couvert</option>
                                        <option value="0">Non, rejeté</option>
                                    </select>
                                    <div id="rejet_box" class="hidden">
                                        <textarea name="motif_rejet" class="w-full p-2 h-20 text-sm border-slate-300 rounded-lg"
                                            placeholder="Motif du rejet..."></textarea>
                                    </div>
                                    <button type="submit"
                                        class="w-full py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold hover:bg-slate-900 transition-colors">Enregistrer
                                        Décision</button>
                                </div>
                            </form>
                        @endif
                    </div>

                    {{-- Mandatement Expert & Garage --}}
                    @if (in_array($sinistre->workflow_step, [
                            'warranty_verified_pending_assignment',
                            'expert_garage_assigned',
                            'closed_validated',
                        ]))
                        <div class="p-4 border border-slate-200 rounded-xl bg-slate-50">
                            <h4 class="font-bold text-sm mb-3">2. Orientation Expert / Garage</h4>
                            <form action="{{ route('assurance.sinistres.assign_expert_garage', $sinistre->id) }}"
                                method="POST">
                                @csrf
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-xs text-slate-500 font-semibold mb-1 block">Expert
                                            Mandaté</label>
                                        <select name="expert_id"
                                            class="w-full h-10 px-3 rounded-lg text-sm border-slate-300">
                                            <option value="">-- Aucun --</option>
                                            @foreach ($experts as $ex)
                                                <option value="{{ $ex->id }}"
                                                    {{ $sinistre->expert_id == $ex->id ? 'selected' : '' }}>
                                                    {{ $ex->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500 font-semibold mb-1 block">Garage
                                            Réparateur</label>
                                        <select name="garage_id"
                                            class="w-full h-10 px-3 rounded-lg text-sm border-slate-300">
                                            <option value="">-- Aucun --</option>
                                            @foreach ($garages as $ga)
                                                <option value="{{ $ga->id }}"
                                                    {{ $sinistre->garage_id == $ga->id ? 'selected' : '' }}>
                                                    {{ $ga->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit"
                                        class="w-full py-2 bg-primary-600 text-white rounded-lg text-sm font-semibold hover:bg-primary-700 transition-colors">Assigner</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

                {{-- Impressions PDF --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-print text-slate-400"></i> Documents à éditer
                    </h3>
                    <div class="flex flex-col gap-3">
                        <a href="{{ route('assurance.sinistres.pdf.dossier', $sinistre->id) }}" target="_blank"
                            class="w-full flex items-center justify-between p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-sm font-medium text-slate-700">
                            <span><i class="fa-regular fa-folder-open text-primary-500 mr-2"></i> Dossier Sinistre</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-xs text-slate-400"></i>
                        </a>

                        @if ($sinistre->workflow_step == 'expert_garage_assigned' || $sinistre->garage_id)
                            <a href="{{ route('assurance.sinistres.pdf.prise_en_charge', $sinistre->id) }}" target="_blank"
                                class="w-full flex items-center justify-between p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-sm font-medium text-slate-700">
                                <span><i class="fa-solid fa-wrench text-orange-500 mr-2"></i> Bon de Prise en Charge</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-xs text-slate-400"></i>
                            </a>

                            <a href="{{ route('assurance.sinistres.pdf.bon_sortie', $sinistre->id) }}" target="_blank"
                                class="w-full flex items-center justify-between p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-sm font-medium text-slate-700">
                                <span><i class="fa-solid fa-car-side text-emerald-500 mr-2"></i> Bon de Sortie</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-xs text-slate-400"></i>
                            </a>
                        @else
                            <div class="text-xs text-slate-400 italic p-2 bg-slate-50 rounded text-center">Assignez un
                                garage pour générer les bons.</div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Colonne Documents & Décision --}}
            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-800">Pièces justificatives</h2>
                        <div class="flex items-center gap-3">
                            <span
                                class="text-xs font-bold text-slate-500 uppercase">{{ $sinistre->documentsAttendus->count() }}
                                requis</span>
                            <button type="button"
                                onclick="document.getElementById('add-doc-form').classList.toggle('hidden')"
                                class="flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-colors">
                                <i class="fa-solid fa-plus"></i> Ajouter un document
                            </button>
                        </div>
                    </div>

                    {{-- Formulaire d'ajout de document --}}
                    <div id="add-doc-form" class="hidden px-6 py-4 bg-indigo-50 border-b border-indigo-100">
                        <form action="{{ route('assurance.sinistres.add_document', $sinistre) }}" method="POST"
                            class="flex flex-wrap items-end gap-3">
                            @csrf
                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-xs font-bold text-slateistre-700 mb-1">Nom du document <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="nom_document" required
                                    placeholder="Ex: Rapport de police, Certificat médical..."
                                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white">
                            </div>
                            <div class="w-36">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Type de champ</label>
                                <select name="type_champ"
                                    class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white">
                                    <option value="file">Fichier</option>
                                    <option value="text">Texte</option>
                                </select>
                            </div>
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-colors">
                                <i class="fa-solid fa-floppy-disk mr-1"></i> Enregistrer
                            </button>
                            <button type="button"
                                onclick="document.getElementById('add-doc-form').classList.add('hidden')"
                                class="px-4 py-2 bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 text-xs font-bold rounded-lg transition-colors">
                                Annuler
                            </button>
                        </form>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($sinistre->documentsAttendus as $doc)
                            @php
                                $dernierSoumis = $doc->documentsSoumis->last();
                                $isUploaded = $doc->status_client === 'uploaded';
                                $aiStatus = $dernierSoumis ? $dernierSoumis->ai_compliance_status : null;
                                $managerOverride = $dernierSoumis ? $dernierSoumis->manager_override_status : null;

                                // Couleur de bordure et background final (Priorité Manager > Claims AI > En attente)
                                $currentStatus = $managerOverride ?? $aiStatus;
                                $cardClasses = 'p-5 ';
                                if (!$isUploaded) {
                                    $cardClasses .= 'bg-white border-l-4 border-l-slate-200';
                                } elseif ($currentStatus === 'valid') {
                                    $cardClasses .= 'bg-emerald-50/30 border-l-4 border-l-emerald-500';
                                } elseif ($currentStatus === 'invalid') {
                                    $cardClasses .= 'bg-red-50/30 border-l-4 border-l-red-500';
                                } else {
                                    $cardClasses .= 'bg-yellow-50/30 border-l-4 border-l-yellow-400';
                                }
                            @endphp

                            <div class="{{ $cardClasses }} transition-all">
                                <div class="flex flex-col sm:flex-row gap-5">

                                    {{-- Preview / Icône du document --}}
                                    <div
                                        class="w-full sm:w-32 shrink-0 flex flex-col items-center justify-center p-3 bg-slate-50 rounded-xl border border-slate-100">
                                        @if ($isUploaded && $dernierSoumis && $doc->type_champ === 'file' && $dernierSoumis->file_path)
                                            @if (in_array(pathinfo($dernierSoumis->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                                <a href="{{ Storage::url($dernierSoumis->file_path) }}" target="_blank"
                                                    class="block w-full h-20 rounded shadow-sm overflow-hidden hover:scale-105 transition-transform">
                                                    <img src="{{ Storage::url($dernierSoumis->file_path) }}"
                                                        alt="Document" class="w-full h-full object-cover">
                                                </a>
                                                <a href="{{ Storage::url($dernierSoumis->file_path) }}" target="_blank"
                                                    class="text-[10px] text-primary-600 font-bold mt-2 uppercase hover:underline">Voir
                                                    l'image</a>
                                            @else
                                                <i class="fa-solid fa-file-pdf text-4xl text-red-400 mb-2"></i>
                                                <a href="{{ Storage::url($dernierSoumis->file_path) }}" target="_blank"
                                                    class="text-[10px] text-primary-600 font-bold uppercase hover:underline">Ouvrir
                                                    PDF</a>
                                            @endif
                                        @elseif($isUploaded && $dernierSoumis && $doc->type_champ !== 'file')
                                            <i class="fa-solid fa-font text-3xl text-slate-300 mb-2"></i>
                                            <div
                                                class="text-xs text-center font-semibold text-slate-700 break-all w-full leading-tight">
                                                {{ $dernierSoumis->file_value }}
                                            </div>
                                        @else
                                            <i class="fa-solid fa-clock text-3xl text-slate-300"></i>
                                            <span class="text-[10px] font-bold text-slate-400 mt-2 uppercase">Non
                                                soumis</span>
                                        @endif
                                    </div>

                                    {{-- Détails et Actions --}}
                                    <div class="flex-1 flex flex-col justify-between">

                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-bold text-slate-800 text-base leading-tight">
                                                    {{ $doc->nom_document }}</h4>
                                                <div class="text-xs text-slate-500 mt-1">
                                                    Format: <span
                                                        class="uppercase font-semibold text-slate-400">{{ $doc->type_champ }}</span>
                                                    @if ($doc->is_mandatory)
                                                        <span class="text-red-400 font-bold ml-2">• Requis</span>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- Bouton supprimer le document demandé --}}
                                            <form id="del-doc-form-{{ $doc->id }}"
                                                action="{{ route('assurance.sinistres.remove_document', [$sinistre, $doc]) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    onclick="confirmDeleteDoc({{ $doc->id }}, '{{ addslashes($doc->nom_document) }}')"
                                                    class="ml-3 p-1.5 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Supprimer ce document">
                                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                                </button>
                                            </form>

                                            @if ($isUploaded)
                                                <div class="text-right">
                                                    @if ($aiStatus === 'valid')
                                                        <span
                                                            class="inline-flex px-2 py-0.5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded"><i
                                                                class="fa-solid fa-robot mr-1"></i> Claims AI ok</span>
                                                    @elseif($aiStatus === 'invalid')
                                                        <span
                                                            class="inline-flex px-2 py-0.5 bg-red-100 text-red-700 text-xs font-bold rounded"><i
                                                                class="fa-solid fa-robot mr-1"></i> Claims AI Fail</span>
                                                    @else
                                                        <span
                                                            class="inline-flex px-2 py-0.5 bg-slate-100 text-slate-700 text-xs font-bold rounded">Claims
                                                            AI Pending</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        @if ($isUploaded && $dernierSoumis)
                                            @if ($dernierSoumis->ai_feedback)
                                                <div
                                                    class="text-xs mt-3 bg-white p-2 border border-slate-100 rounded-lg text-slate-600 flex gap-2 w-full">
                                                    <i class="fa-solid fa-comment-dots mt-0.5 text-slate-400"></i>
                                                    <span
                                                        class="italic leading-relaxed">{{ $dernierSoumis->ai_feedback }}</span>
                                                </div>
                                            @endif

                                            {{-- Actions de forçage (Override) --}}
                                            <div
                                                class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                                                <div
                                                    class="text-xs font-medium {{ $managerOverride ? 'text-primary-600' : 'text-slate-400' }}">
                                                    @if ($managerOverride === 'valid')
                                                        <i class="fa-solid fa-user-check mr-1"></i> Validé par Gestionnaire
                                                    @elseif($managerOverride === 'invalid')
                                                        <i class="fa-solid fa-user-xmark mr-1"></i> Rejeté par Gestionnaire
                                                    @else
                                                        Action requise
                                                    @endif
                                                </div>

                                                <form
                                                    action="{{ route('assurance.sinistres.review-doc', ['sinistre' => $sinistre->id, 'documentAttendu' => $doc->id]) }}"
                                                    method="POST" class="flex gap-2">
                                                    @csrf
                                                    <input type="hidden" name="override_status"
                                                        id="override_status_{{ $doc->id }}" value="">

                                                    @if ($managerOverride === 'valid')
                                                        {{-- Bouton Déverrouiller (quand c'est déjà validé) --}}
                                                        <button type="button"
                                                            onclick="document.getElementById('override_status_{{ $doc->id }}').value='pending'; this.form.submit();"
                                                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white border border-slate-300 text-slate-600 hover:bg-slate-100 hover:text-slate-900">
                                                            <i class="fa-solid fa-unlock mr-1"></i> Déverrouiller
                                                        </button>
                                                    @else
                                                        {{-- Boutons normaux (quand ce n'est pas encore validé) --}}
                                                        <button type="button"
                                                            onclick="document.getElementById('override_status_{{ $doc->id }}').value='valid'; this.form.submit();"
                                                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white border border-slate-200 text-emerald-600 hover:bg-emerald-50">
                                                            Valider
                                                        </button>

                                                        <button type="button"
                                                            onclick="let m = prompt('Motif du rejet (optionnel) :'); if(m !== null) { let f = document.createElement('input'); f.type='hidden'; f.name='feedback'; f.value=m; this.form.appendChild(f); document.getElementById('override_status_{{ $doc->id }}').value='invalid'; this.form.submit(); }"
                                                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $managerOverride === 'invalid' ? 'bg-red-500 text-white shadow-md shadow-red-500/20 ring-2 ring-red-500/50' : 'bg-white border border-slate-200 text-red-600 hover:bg-red-50' }}">
                                                            Rejeter
                                                        </button>
                                                    @endif
                                                </form>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-slate-500">Aucun document requis.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Décision Finale --}}
                @if ($sinistre->status !== 'cloture')
                    @php
                        // Vérifier si tous les documents attendus ont un document soumis avec manager_override_status === 'valid'
                        $tousDocumentsValides = true;
                        if ($sinistre->documentsAttendus->count() > 0) {
                            $tousDocumentsValides = $sinistre->documentsAttendus->every(function ($doc) {
                                $dernierSoumis = $doc->documentsSoumis->last();
                                return $dernierSoumis && $dernierSoumis->manager_override_status === 'valid';
                            });
                        }
                    @endphp
                    <div
                        class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm mt-6 flex items-center justify-between {{ !$tousDocumentsValides ? 'opacity-80' : '' }}">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Clôturer le dossier</h3>
                            <p class="text-sm text-slate-500 mt-1">L'indemnisation est validée et le sinistre sera
                                définitivement clos.</p>
                        </div>

                        @if ($tousDocumentsValides)
                            <form id="form-cloture-sinistre-{{ $sinistre->id }}"
                                action="{{ route('assurance.sinistres.decision', $sinistre->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="decision" value="valide">
                                <button type="button" onclick="confirmCloture({{ $sinistre->id }})"
                                    class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-all shadow-sm flex items-center gap-2">
                                    <i class="fa-solid fa-flag-checkered"></i> Valider et Clôturer
                                </button>
                            </form>
                        @else
                            <div class="text-right">
                                <button type="button" disabled
                                    class="px-6 py-3 bg-slate-300 cursor-not-allowed text-slate-500 font-bold rounded-xl text-sm shadow-sm flex items-center gap-2 mb-2">
                                    <i class="fa-solid fa-lock"></i> Valider et Clôturer
                                </button>
                                <span class="text-xs text-red-500 font-semibold"><i
                                        class="fa-solid fa-triangle-exclamation mr-1"></i> Veuillez valider tous les
                                    documents requis</span>
                            </div>
                        @endif
                    </div>
                @endif

            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDeleteDoc(docId, docName) {
                Swal.fire({
                    title: 'Supprimer ce document ?',
                    html: 'Le document <strong>' + docName +
                        '</strong> sera définitivement supprimé de la liste des pièces requises.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Oui, supprimer',
                    cancelButtonText: 'Annuler',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('del-doc-form-' + docId).submit();
                    }
                });
            }

            function confirmCloture(sinistreId) {
                Swal.fire({
                    title: 'Clôturer le sinistre ?',
                    text: "Vous êtes sur le point de valider et clôturer définitivement ce sinistre. Cette action est irréversible.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#059669', // text-emerald-600
                    cancelButtonColor: '#64748b', // text-slate-500
                    confirmButtonText: '<i class="fa-solid fa-check"></i> Oui, clôturer',
                    cancelButtonText: 'Annuler',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('form-cloture-sinistre-' + sinistreId).submit();
                    }
                });
            }
        </script>
    @endpush

@endsection
