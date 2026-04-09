@extends('assure.layouts.template')

@section('title', 'Soumission de Documents')
@section('page-title', 'Pièces Justificatives')

@section('content')
<div class="mx-auto pb-12" style="max-width: 1700px;">
    
    {{-- En-tête / Breadcrumb --}}
    <div class="mb-6 flex items-center justify-between flex-wrap gap-4 animate-in" style="--delay:0.1s">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-400 mb-2">
                <a href="{{ route('assure.sinistres.documents') }}" class="hover:text-slate-600 transition-colors">Documents Requis</a>
                <i class="fa-solid fa-chevron-right text-xs"></i>
                <span class="text-slate-600 font-semibold">Sinistre {{ $sinistre->numero_sinistre ?? 'SI-'.$sinistre->id }}</span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-sm" style="background-color: rgba(33, 54, 133, 0.1); color: #213685; border: 1px solid rgba(33, 54, 133, 0.2);">
                    <i class="fa-solid fa-cloud-arrow-up text-base"></i>
                </div>
                Dossier : {{ str_replace('_', ' ', $sinistre->type_sinistre) }}
            </h2>
        </div>
        
        <div class="flex items-center gap-3">
            @if($sinistre->workflow_step === 'docs_pending')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200">
                    <i class="fa-solid fa-clock"></i> Action requise
                </span>
            @elseif(str_starts_with($sinistre->workflow_step, 'closed'))
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                    <i class="fa-solid fa-check"></i> Dossier traité
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-50 text-blue-600 border border-blue-200">
                    <i class="fa-solid fa-gears"></i> En révision
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        {{-- COLONNE GAUCHE (Liste des documents - 2/3) --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Liste des documents --}}
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden animate-in d3">
                <div class="h-1.5" style="background: linear-gradient(to right, #213685, #7aaa25);"></div>
                <div class="p-6 md:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Pièces demandées</h3>
                        <span class="badge badge-gray">{{ $documentsAttendus->count() }} pièces</span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($documentsAttendus as $doc)
                            @php
                                $dernierSoumis = $doc->documentsSoumis->last();
                                $isUploaded = in_array($doc->status_client, ['uploaded', 'rejected']);
                                $aiStatus = $dernierSoumis ? $dernierSoumis->ai_compliance_status : 'pending';
                                
                                $cardClass = "transition-all p-5 sm:p-6 rounded-2xl border-2 ";
                                if ($isUploaded) {
                                    if ($aiStatus === 'valid') $cardClass .= "border-emerald-200 bg-emerald-50/20";
                                    elseif ($aiStatus === 'invalid') $cardClass .= "border-red-200 bg-red-50/30";
                                    else $cardClass .= "border-slate-200 bg-slate-50";
                                } else {
                                    $cardClass .= "border-dashed border-slate-200 bg-white hover:shadow-md";
                                }
                            @endphp
                            
                            <div class="{{ $cardClass }}" id="doc-container-{{ $doc->id }}" style="{{ !$isUploaded ? 'transition: all 0.3s ease;' : '' }}" {!! !$isUploaded ? 'onmouseover="this.style.borderColor=\'#213685\'" onmouseout="this.style.borderColor=\'\'"' : '' !!}>
                                
                                {{-- Haut de carte : Titre & Statut --}}
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 shadow-sm
                                            {{ $isUploaded ? 'bg-white text-slate-600' : 'bg-slate-50 border border-slate-100 text-slate-400' }}">
                                            @if($doc->type_champ === 'file')
                                                <i class="fa-solid fa-file-arrow-up"></i>
                                            @elseif($doc->type_champ === 'text')
                                                <i class="fa-solid fa-align-left"></i>
                                            @else
                                                <i class="fa-solid fa-hashtag"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-800 text-base">{{ $doc->nom_document }}</h4>
                                            <p class="text-xs text-slate-500 font-medium">
                                                Type : <span class="uppercase tracking-wide">{{ $doc->type_champ === 'file' ? 'Photo/PDF' : $doc->type_champ }}</span>
                                                @if($doc->is_mandatory) <span class="text-red-400 font-bold ml-1">• Requis</span> @endif
                                            </p>
                                        </div>
                                    </div>
                                    
                                    {{-- Badge Statut --}}
                                    <div id="status-badge-{{ $doc->id }}" class="shrink-0">
                                        @if($isUploaded)
                                            @if($aiStatus === 'valid')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700"><i class="fa-solid fa-check-circle"></i> Claims AI Conforme</span>
                                            @elseif($aiStatus === 'invalid')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-700 animate-pulse"><i class="fa-solid fa-triangle-exclamation"></i> Non Conforme</span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-slate-200 text-slate-700"><i class="fa-solid fa-spinner fa-spin"></i> Analyse Claims AI...</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
        
                                {{-- Feedback Claims AI s'il y a un problème --}}
                                <div id="feedback-{{ $doc->id }}" class="{{ ($isUploaded && $aiStatus === 'invalid') ? 'flex' : 'hidden' }} mb-5 text-sm bg-red-50/50 text-red-700 p-4 rounded-xl border border-red-100 gap-3 items-start">
                                    <i class="fa-solid fa-robot mt-0.5 text-red-500"></i>
                                    <div>
                                        <strong class="block mb-1">Retour de Claims AI :</strong> 
                                        <span class="ai-msg">{{ $isUploaded && $dernierSoumis ? $dernierSoumis->ai_feedback : '' }}</span>
                                        <p class="text-xs text-red-500 font-semibold mt-1">Veuillez soumettre un nouveau fichier plus lisible.</p>
                                    </div>
                                </div>
        
                                {{-- Fichier Actuel (Consultation) --}}
                                @if($isUploaded && $dernierSoumis && $dernierSoumis->file_path)
                                    <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-500 shrink-0">
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Document Transmis</p>
                                                <p class="text-sm font-semibold text-slate-800 line-clamp-1">Soumis le {{ $dernierSoumis->created_at->format('d/m/Y à H:i') }}</p>
                                            </div>
                                        </div>
                                        <a href="{{ Storage::url($dernierSoumis->file_path) }}" target="_blank" class="shrink-0 px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-bold text-sm rounded-lg border border-slate-200 shadow-sm transition-all flex items-center justify-center gap-2">
                                            <i class="fa-solid fa-eye text-xs"></i> Consulter
                                        </a>
                                    </div>
                                @elseif($isUploaded && $dernierSoumis && $dernierSoumis->file_value)
                                    <div class="mb-5 flex flex-col sm:flex-row sm:items-center p-4 bg-slate-50 rounded-xl border border-slate-200 gap-3">
                                        <i class="fa-solid fa-align-left text-slate-400"></i>
                                        <div>
                                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Valeur Saisie :</p>
                                            <p class="text-sm font-medium text-slate-800">{{ $dernierSoumis->file_value }}</p>
                                        </div>
                                    </div>
                                @endif
        
                                {{-- Formulaire d'envoi OU Message de validation --}}
                                @if(($isUploaded && $dernierSoumis && $dernierSoumis->manager_override_status === 'valid') || $sinistre->status === 'cloture')
                                    <div class="mt-2 text-center p-3 text-emerald-700 rounded-xl border flex items-center justify-center gap-2 {{ ($isUploaded && $dernierSoumis && $dernierSoumis->manager_override_status === 'valid') ? 'bg-emerald-50 border-emerald-100' : 'bg-slate-50 border-slate-200 text-slate-500' }}">
                                        <i class="fa-solid fa-lock"></i>
                                        <span class="text-sm font-bold">Document validé et verrouillé. Aucune modification possible.</span>
                                    </div>
                                @else
                                    <form onsubmit="submitDocument(event, {{ $doc->id }})" class="relative mt-2">
                                        @csrf
                                        
                                        @if($doc->type_champ === 'file')
                                            {{-- Composant Dropzone Fichier --}}
                                            <div class="relative">
                                                <input type="file" id="file-{{ $doc->id }}" name="document_file" class="peer hidden" accept=".jpg,.jpeg,.png,.pdf" onchange="updateFilename(this, {{ $doc->id }})">
                                                <label for="file-{{ $doc->id }}" class="flex flex-col items-center justify-center w-full min-h-[100px] border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 hover:bg-white transition-all cursor-pointer group" onmouseover="this.style.borderColor='#213685'" onmouseout="this.style.borderColor=''">
                                                    <div class="flex flex-col items-center justify-center pt-5 pb-5 px-4 text-center">
                                                        <i id="icon-{{ $doc->id }}" class="fa-solid fa-cloud-arrow-up mb-2 text-2xl text-slate-400 transition-colors group-hover:!text-[#213685]"></i>
                                                        <p id="filename-{{ $doc->id }}" class="text-sm text-slate-600 font-medium">Cliquez ou glissez un fichier (Image/PDF)</p>
                                                    </div>
                                                </label>
                                            </div>
                                        @else
                                            {{-- Composant Input Texte/Nombre --}}
                                            <div class="relative">
                                                <input type="{{ $doc->type_champ === 'number' ? 'number' : 'text' }}" id="text-{{ $doc->id }}" name="document_text" 
                                                    class="w-full h-14 pl-5 pr-5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:bg-white focus:ring-0 transition-all text-sm font-medium text-slate-800 focus:outline-none" style="outline-color: #213685;" onfocus="this.style.borderColor='#213685'" onblur="this.style.borderColor=''" 
                                                    placeholder="Saisissez {{ $doc->nom_document }}..." 
                                                    value="{{ $isUploaded ? ($dernierSoumis->file_value ?? '') : '' }}">
                                            </div>
                                        @endif
                                        
                                        {{-- Bouton Submit --}}
                                        <div class="mt-4 flex justify-end">
                                            <button type="submit" id="btn-{{ $doc->id }}" class="px-6 py-2.5 text-white font-bold rounded-xl text-sm transition-all shadow-sm flex items-center justify-center gap-2 group disabled:opacity-50" style="background-color: #213685;" onmouseover="this.style.backgroundColor='#1a2b6b'" onmouseout="this.style.backgroundColor='#213685'">
                                                <span>Envoyer le document</span>
                                                <i class="fa-solid fa-paper-plane text-xs opacity-70 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></i>
                                            </button>
                                        </div>
                                    </form>
                                @endif
        
                            </div>
                        @empty
                            <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                <div class="w-16 h-16 bg-white shadow-sm text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl border border-slate-100">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800">Aucun document requis</h3>
                                <p class="text-slate-500 text-sm mt-1">Votre déclaration est complète d'après Claims AI.</p>
                            </div>
                        @endforelse
                    </div>                    
                </div>
            </div>
        </div>

        {{-- COLONNE DROITE (Infos contextualisées - 1/3) --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- Rapport Claims AI Rapide --}}
            @if($sinistre->ai_analysis_report)
                <div class="p-6 rounded-3xl shadow-sm relative overflow-hidden animate-in d2" style="background-color: rgba(122, 170, 37, 0.05); border: 1px solid rgba(122, 170, 37, 0.2);">
                    <div class="absolute -right-6 -top-6 text-9xl pointer-events-none" style="color: rgba(122, 170, 37, 0.05);"><i class="fa-solid fa-microchip"></i></div>
                    <div class="flex flex-col gap-4 relative z-10">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="color: #7aaa25; border: 1px solid rgba(122, 170, 37, 0.2);">
                                <i class="fa-solid fa-robot text-lg"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 text-base leading-tight">Analyse de Claims AI</h3>
                        </div>
                        <div class="text-slate-600 text-sm leading-relaxed">
                            Gravité : <strong class="uppercase bg-white px-2 py-0.5 rounded text-slate-800 border border-slate-200 ml-1 shadow-sm">{{ $sinistre->ai_analysis_report['gravity'] ?? 'N/A' }}</strong>
                            <div class="mt-3 p-3 bg-white rounded-xl border border-slate-200 shadow-sm">
                                <strong>Contexte :</strong> <em class="block mt-1 text-xs">"{{ $sinistre->ai_analysis_report['context'] ?? '' }}"</em>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Instructions --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5">
                    <i class="fa-solid fa-circle-question text-8xl"></i>
                </div>
                <div class="relative z-10">
                    <h3 class="font-bold text-slate-800 text-base mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-lightbulb text-amber-500"></i> Instructions Rapides
                    </h3>
                    <ul class="space-y-3 text-sm text-slate-600">
                        <li class="flex gap-3">
                            <i class="fa-solid fa-check text-emerald-500 mt-1 shrink-0"></i>
                            <span>Vérifiez que vos photos sont d'assez bonne qualité et que le texte est lisible.</span>
                        </li>
                        <li class="flex gap-3">
                            <i class="fa-solid fa-check text-emerald-500 mt-1 shrink-0"></i>
                            <span>Les documents PDF ou Images (JPG/PNG) sont acceptés.</span>
                        </li>
                        <li class="flex gap-3">
                            <i class="fa-solid fa-check text-emerald-500 mt-1 shrink-0"></i>
                            <span>Claims AI analysera automatiquement chaque document après l'envoi.</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Retour --}}
            <a href="{{ route('assure.sinistres.show', $sinistre->id) }}" class="flex items-center justify-center w-full gap-2 px-6 py-3 bg-white text-slate-700 font-bold rounded-2xl border-2 border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition-all shadow-sm">
                <i class="fa-solid fa-arrow-left"></i> Retour aux détails
            </a>
            
        </div>

    </div>

</div>

@push('scripts')
<script>
    function updateFilename(input, id) {
        const fileNameLabel = document.getElementById('filename-' + id);
        const iconLabel = document.getElementById('icon-' + id);
        
        if (input.files && input.files.length > 0) {
            fileNameLabel.textContent = input.files[0].name;
            fileNameLabel.classList.add('font-bold');
            fileNameLabel.style.color = '#213685';
            fileNameLabel.classList.remove('text-slate-600');
            iconLabel.classList.replace('fa-cloud-arrow-up', 'fa-file-circle-check');
            iconLabel.style.color = '#213685';
            iconLabel.classList.remove('text-slate-400');
        } else {
            fileNameLabel.textContent = 'Cliquez ou glissez un fichier (Image/PDF)';
            fileNameLabel.classList.remove('font-bold');
            fileNameLabel.style.color = '';
            fileNameLabel.classList.add('text-slate-600');
            iconLabel.classList.replace('fa-file-circle-check', 'fa-cloud-arrow-up');
            iconLabel.style.color = '';
            iconLabel.classList.add('text-slate-400');
        }
    }

    async function submitDocument(event, docId) {
        event.preventDefault();
        
        const form = event.target;
        const btn = document.getElementById('btn-' + docId);
        const originalBtnHTML = btn.innerHTML;
        const feedbackDiv = document.getElementById('feedback-' + docId);
        const statusBadgeDiv = document.getElementById('status-badge-' + docId);
        const container = document.getElementById('doc-container-' + docId);
        
        // Form validation
        const fileInput = document.getElementById('file-' + docId);
        const textInput = document.getElementById('text-' + docId);
        if (fileInput && !fileInput.files.length) {
            Swal.fire({icon: 'warning', title: 'Attention', text: 'Veuillez sélectionner un fichier.'});
            return;
        }
        if (textInput && textInput.value.trim() === '') {
            Swal.fire({icon: 'warning', title: 'Attention', text: 'Veuillez remplir le champ texte.'});
            return;
        }
        
        // Mode Loading
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i><span>Analyse Claims AI en cours...</span>';
        
        const formData = new FormData(form);
        
        try {
            const response = await fetch(`/mon-espace/sinistres/upload-doc/${docId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();
            
            if (response.ok && result.success) {
                // UI Reset
                container.className = "transition-all p-5 sm:p-6 rounded-2xl border-2 hover:border-purple-300 ";
                
                if (result.status === 'valid') {
                    container.classList.add('border-emerald-200', 'bg-emerald-50/20');
                    statusBadgeDiv.innerHTML = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700"><i class="fa-solid fa-check-circle"></i> Claims AI Conforme</span>`;
                    feedbackDiv.classList.replace('flex', 'hidden');
                    
                    Swal.fire({
                        icon: 'success', title: 'Validé par Claims AI !',
                        text: 'Votre document répond aux critères demandés.',
                        timer: 2000, showConfirmButton: false
                    });
                } 
                else if (result.status === 'invalid') {
                    container.classList.add('border-red-200', 'bg-red-50/30');
                    statusBadgeDiv.innerHTML = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-700 animate-pulse"><i class="fa-solid fa-triangle-exclamation"></i> Non Conforme</span>`;
                    
                    feedbackDiv.classList.replace('hidden', 'flex');
                    feedbackDiv.querySelector('.ai-msg').textContent = result.feedback;
                    
                    Swal.fire({
                        icon: 'error', title: 'Document Rejeté',
                        text: 'Claims AI a détecté une anomalie. Consultez le retour pour plus de détails.',
                        confirmButtonText: 'Je comprends'
                    });
                } 
                else {
                    // pending
                    container.classList.add('border-slate-200', 'bg-slate-50');
                    statusBadgeDiv.innerHTML = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-slate-200 text-slate-700"><i class="fa-solid fa-spinner fa-spin"></i> En vérification...</span>`;
                    feedbackDiv.classList.replace('flex', 'hidden');
                    Swal.fire({icon: 'info', title: 'Document Envoyé', text: 'Le document est soumis pour examen.', timer:2000, showConfirmButton:false});
                }
            } else {
                Swal.fire({icon: 'error', title: 'Erreur Serveur', text: result.message || 'Le téléchargement a échoué.'});
            }
        } catch (error) {
            console.error(error);
            Swal.fire({icon: 'error', title: 'Erreur Réseau', text: 'Impossible de joindre le serveur.'});
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalBtnHTML;
        }
    }
</script>
@endpush
@endsection
