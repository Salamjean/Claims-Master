@extends('agent.layouts.template')
@section('title', 'Rédaction du Constat Officiel')
@section('page-title', 'Rédaction du Constat')

@section('content')
<div class="mx-auto space-y-6" style="width:100%;">

    {{-- En-tête --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('agent.sinistres.show', $sinistre->id) }}"
                class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-sm">
                <i class="fa-solid fa-arrow-left text-slate-500 text-sm"></i>
            </a>
            <div>
                <h2 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-file-pen text-violet-500 text-base"></i>
                    Rédaction du Constat Officiel
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    Sinistre <span class="font-bold text-violet-600">{{ $sinistre->numero_sinistre }}</span>
                    — Constat terrain validé ✅
                </p>
            </div>
        </div>
        <span class="inline-flex items-center gap-2 px-4 py-2 bg-violet-100 text-violet-700 text-sm font-bold rounded-xl">
            <i class="fa-solid fa-circle text-[8px]"></i> Étape 2 / 2
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Colonne Gauche : Récapitulatif Constat Terrain --}}
        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-emerald-50/60 flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <i class="fa-solid fa-map-location-dot text-emerald-600 text-xs"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 text-sm">Constat Terrain</h3>
                    <span class="ml-auto text-xs font-bold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full">✅ Validé</span>
                </div>
                <div class="p-5 space-y-4">
                    @php $c = $sinistre->constat; @endphp

                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">Type</p>
                        <p class="text-sm font-bold text-slate-700 uppercase">{{ $c->type_constat }}</p>
                    </div>

                    @if($c->lieu)
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">Lieu</p>
                        <p class="text-sm font-bold text-slate-700">{{ $c->lieu }}</p>
                    </div>
                    @endif

                    @if($c->date_heure)
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">Date / Heure</p>
                        <p class="text-sm font-bold text-slate-700">{{ $c->date_heure->format('d/m/Y à H:i') }}</p>
                    </div>
                    @endif

                    @if($c->description_faits)
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">Faits</p>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ $c->description_faits }}</p>
                    </div>
                    @endif

                    @if($c->dommages)
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">Dommages</p>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ $c->dommages }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Assuré --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-blue-50/60 flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="fa-solid fa-user text-blue-600 text-xs"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 text-sm">Assuré</h3>
                </div>
                <div class="p-5 space-y-3">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">Nom</p>
                        <p class="text-sm font-bold text-slate-700">{{ $sinistre->assure->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1">Contact SMS</p>
                        <p class="text-sm font-bold text-violet-700 flex items-center gap-2">
                            <i class="fa-solid fa-mobile-screen text-xs"></i>
                            {{ $sinistre->assure->contact ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne Droite : Formulaire de Rédaction --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-violet-50/60 flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center">
                        <i class="fa-solid fa-file-lines text-violet-600 text-xs"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 text-sm">Rédaction du Document Officiel</h3>
                </div>
                <div class="p-6">

                    @if($errors->any())
                    <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <p class="text-sm font-bold text-red-700 flex items-center gap-2 mb-2">
                            <i class="fa-solid fa-circle-exclamation"></i> Erreur de validation
                        </p>
                        <ul class="text-sm text-red-600 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Onglets Texte / PDF --}}
                    <div class="flex p-1 bg-slate-100 rounded-2xl mb-8 w-fit">
                        <button type="button" id="tab-text"
                            onclick="switchTab('text')"
                            class="tab-btn px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300">
                            <i class="fa-solid fa-pen-to-square mr-2"></i> Rédiger ici
                        </button>
                        <button type="button" id="tab-pdf"
                            onclick="switchTab('pdf')"
                            class="tab-btn px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300">
                            <i class="fa-solid fa-file-pdf mr-2"></i> Joindre un PDF
                        </button>
                    </div>

                    <style>
                        .tab-btn.active-tab {
                            background: white;
                            color: #7c3aed;
                            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
                        }
                        .tab-btn:not(.active-tab) {
                            color: #64748b;
                        }
                        .tab-btn:not(.active-tab):hover {
                            color: #1e293b;
                        }
                    </style>

                    <form action="{{ route('agent.sinistres.redaction.store', $sinistre->id) }}" method="POST"
                        id="redaction-form" enctype="multipart/form-data">
                        @csrf

                        {{-- Zone texte --}}
                        <div id="panel-text">
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">
                                Contenu de la Rédaction
                            </label>
                            <p class="text-xs text-slate-400 mb-3">
                                Rédigez le procès-verbal officiel : circonstances, parties impliquées, dommages et conclusions.
                            </p>
                            <textarea
                                name="redaction_contenu"
                                id="redaction_contenu"
                                rows="16"
                                placeholder="Rédigez ici le constat officiel...&#10;&#10;Le soussigné [NOM], agent immatriculé [...], certifie avoir constaté...&#10;&#10;Circonstances :&#10;...&#10;&#10;Dommages :&#10;..."
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm text-slate-700 font-mono leading-relaxed resize-y focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all @error('redaction_contenu') border-red-400 bg-red-50 @enderror"
                                minlength="50">{{ old('redaction_contenu', $sinistre->constat->redaction_contenu ?? '') }}</textarea>
                            <div class="flex justify-between mt-2">
                                <p class="text-xs text-slate-400">Minimum 50 caractères</p>
                                <p class="text-xs font-bold" id="char-count">0 caractère(s)</p>
                            </div>
                        </div>

                        {{-- Zone PDF --}}
                        <div id="panel-pdf" class="hidden">
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">
                                Fichier PDF du Constat
                            </label>
                            <p class="text-xs text-slate-400 mb-4">
                                Joignez le document PDF du constat officiel (max 10 Mo).
                            </p>
                            <label for="redaction_pdf"
                                class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-violet-300 rounded-xl bg-violet-50/40 hover:bg-violet-50 cursor-pointer transition-all group">
                                <div class="flex flex-col items-center justify-center text-center p-6">
                                    <i class="fa-solid fa-file-pdf text-4xl text-violet-300 group-hover:text-violet-500 mb-3 transition-colors"></i>
                                    <p class="text-sm font-bold text-slate-600" id="pdf-label">
                                        Cliquez pour sélectionner un PDF
                                    </p>
                                    <p class="text-xs text-slate-400 mt-1">Format PDF uniquement — 10 Mo maximum</p>
                                </div>
                                <input type="file" id="redaction_pdf" name="redaction_pdf"
                                    accept="application/pdf" class="hidden"
                                    onchange="updatePdfLabel(this)">
                            </label>
                        </div>

                        {{-- Montant à payer --}}
                        <div class="my-6 p-5 bg-violet-50/50 border border-violet-100 rounded-2xl">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
                                    <i class="fa-solid fa-money-bill-wave text-violet-600 text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider">Frais de Récupération</h4>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Montant fixé pour l'assuré</p>
                                </div>
                            </div>
                            
                            <div class="relative max-w-xs transition-all duration-300">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-violet-400 font-black text-sm">FCFA</span>
                                </div>
                                <input type="number" 
                                    name="montant_a_payer" 
                                    id="montant_a_payer" 
                                    value="{{ old('montant_a_payer', 1500) }}" 
                                    required
                                    min="0"
                                    step="50"
                                    class="block w-full pl-16 pr-4 py-3 border-2 border-violet-100 rounded-xl text-lg font-black text-violet-700 bg-white focus:border-violet-400 focus:ring-4 focus:ring-violet-500/5 focus:outline-none transition-all placeholder:text-slate-300 shadow-sm"
                                    placeholder="Ex: 1500">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-3 font-medium italic">
                                * Ce montant devra être réglé par l'assuré par mobile money pour débloquer l'accès au constat.
                            </p>
                        </div>

                        {{-- Alerte SMS --}}
                        <div class="my-5 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-mobile-screen text-amber-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-amber-800">SMS automatique à l'assuré</p>
                                    <p class="text-xs text-amber-600 mt-1">
                                        Un SMS sera envoyé au <strong>{{ $sinistre->assure->contact ?? '—' }}</strong>
                                        via Yellika pour l'informer que son constat est disponible et prêt à être récupéré.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" id="submit-btn"
                                class="flex-1 inline-flex items-center justify-center gap-3 px-6 py-3.5 bg-violet-600 hover:bg-violet-700 text-white font-black text-sm rounded-xl transition-all shadow-md shadow-violet-200 active:scale-95">
                                <i class="fa-solid fa-paper-plane"></i>
                                Valider &amp; Notifier l'assuré par SMS
                            </button>
                            <a href="{{ route('agent.sinistres.show', $sinistre->id) }}"
                                class="px-5 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-sm rounded-xl transition-all">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── Onglets ────────────────────────────────────────────────────────────────
    function switchTab(tab) {
        document.getElementById('panel-text').classList.toggle('hidden', tab !== 'text');
        document.getElementById('panel-pdf').classList.toggle('hidden', tab !== 'pdf');

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.toggle('active-tab', btn.id === 'tab-' + tab);
        });
    }

    // Initialisation style onglet actif
    switchTab('text');

    // ── Compteur de caractères ─────────────────────────────────────────────────
    const textarea  = document.getElementById('redaction_contenu');
    const charCount = document.getElementById('char-count');

    function updateCount() {
        const n = textarea.value.length;
        charCount.textContent = n + ' caractère(s)';
        charCount.className = n >= 50
            ? 'text-xs text-emerald-600 font-bold'
            : 'text-xs text-red-400 font-bold';
    }
    textarea.addEventListener('input', updateCount);
    updateCount();

    // ── Label nom du PDF ───────────────────────────────────────────────────────
    function updatePdfLabel(input) {
        const label = document.getElementById('pdf-label');
        if (input.files && input.files[0]) {
            label.textContent = '📎 ' + input.files[0].name;
            label.className = 'text-sm font-bold text-violet-700';
        }
    }

    // ── Confirmation avant soumission ──────────────────────────────────────────
    document.getElementById('redaction-form').addEventListener('submit', function(e) {
        const hasPdf  = document.getElementById('redaction_pdf').files.length > 0;
        const hasText = textarea.value.trim().length >= 50;
        const textVisible = !document.getElementById('panel-text').classList.contains('hidden');

        if (textVisible && !hasText && !hasPdf) {
            e.preventDefault();
            alert('Veuillez rédiger le constat (min. 50 caractères) ou joindre un PDF.');
            return;
        }

        if (!confirm('⚠️ Valider cette rédaction ?\n\nUn SMS sera envoyé à l\'assuré immédiatement.\nCette action est irréversible.')) {
            e.preventDefault();
            return;
        }

        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Envoi...';
    });
</script>
@endpush
