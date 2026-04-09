@extends('assurance.layouts.template')

@section('title', 'Gestion des documents : ' . $typeInfo['label'])

@section('content')
    <div class=" mx-auto" style="max-width: 80%;">

        {{-- Bouton Retour & Titre --}}
        <div class="mb-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('assurance.documents-requis.index') }}"
                    class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                        <i class="fa-solid {{ $typeInfo['icon'] }} text-{{ $typeInfo['color'] }}-500"></i>
                        {{ $typeInfo['label'] }}
                    </h1>
                    <p class="text-slate-500 text-sm mt-1">Configurez les documents exigés pour déclarer ce sinistre.</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div
                class="bg-emerald-50 text-emerald-600 p-4 rounded-xl mb-6 shadow-sm border border-emerald-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 shadow-sm border border-red-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- Formulaire --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
            <form action="{{ route('assurance.documents-requis.update', $type_sinistre) }}" method="POST" id="docsForm">
                @csrf

                <div class="mb-6 border-b border-slate-100 pb-4">
                    <h2 class="text-lg font-semibold text-slate-700">Liste des documents obligatoires</h2>
                    <p class="text-sm text-slate-500">Ajoutez autant de documents que nécessaire. Les champs vides seront
                        ignorés.</p>
                </div>

                <div id="documents-container" class="space-y-4">
                    @forelse($documents as $doc)
                        <div class="doc-row flex items-start gap-3">
                            <div class="flex-1">
                                <input type="text" name="documents[]"
                                    value="{{ old('documents.' . $loop->index, $doc->nom_document) }}"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all text-sm placeholder:text-slate-400"
                                    placeholder="Ex: Photos des dommages, Permis de conduire..." required>
                            </div>
                            <div class="w-44 shrink-0 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-list-check text-slate-400 text-xs"></i>
                                </div>
                                <select name="types[]"
                                    class="w-full pl-9 pr-8 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all text-sm appearance-none cursor-pointer">
                                    <option value="file" {{ old('types.' . $loop->index, $doc->type_champ) == 'file' ? 'selected' : '' }}>Fichier / Image</option>
                                    <option value="text" {{ old('types.' . $loop->index, $doc->type_champ) == 'text' ? 'selected' : '' }}>Texte court</option>
                                    <option value="number" {{ old('types.' . $loop->index, $doc->type_champ) == 'number' ? 'selected' : '' }}>Numérique</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                </div>
                            </div>
                            <button type="button"
                                class="remove-btn w-11 h-11 shrink-0 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-xl border border-red-100 flex items-center justify-center transition-colors"
                                title="Supprimer">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    @empty
                        <div class="doc-row flex items-start gap-3">
                            <div class="flex-1">
                                <input type="text" name="documents[]" value=""
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all text-sm placeholder:text-slate-400"
                                    placeholder="Ex: Copie de la carte grise..." required>
                            </div>
                            <div class="w-44 shrink-0 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-list-check text-slate-400 text-xs"></i>
                                </div>
                                <select name="types[]"
                                    class="w-full pl-9 pr-8 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all text-sm appearance-none cursor-pointer">
                                    <option value="file" selected>Fichier / Image</option>
                                    <option value="text">Texte court</option>
                                    <option value="number">Numérique</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                </div>
                            </div>
                            <button type="button"
                                class="remove-btn w-11 h-11 shrink-0 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-xl border border-red-100 flex items-center justify-center transition-colors"
                                title="Supprimer">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    @endforelse
                </div>

                {{-- Bouton d'ajout --}}
                <div class="mt-4">
                    <button type="button" id="addDocBtn"
                        class="inline-flex items-center gap-2 text-primary-600 font-semibold text-sm hover:text-primary-700 bg-primary-50 hover:bg-primary-100 px-4 py-2.5 rounded-lg transition-colors border border-primary-100">
                        <i class="fa-solid fa-plus bg-white w-5 h-5 rounded flex items-center justify-center text-xs"></i>
                        Ajouter un document
                    </button>
                </div>

                <div class="mt-10 pt-6 border-t border-slate-200 flex justify-end gap-3">
                    <a href="{{ route('assurance.documents-requis.index') }}"
                        class="px-6 py-3 bg-white border border-slate-300 rounded-xl text-slate-700 font-semibold text-sm hover:bg-slate-50 transition-colors">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-primary-600 text-white rounded-xl font-bold text-sm hover:bg-primary-700 shadow-md shadow-primary-600/20 transition-all active:scale-95">
                        <i class="fa-solid fa-save mr-2"></i> Enregistrer les documents
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script pour la gestion dynamique des champs --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const container = document.getElementById('documents-container');
                const addBtn = document.getElementById('addDocBtn');

                // Modèle HTML d'une ligne d'input
                const templateHtml = `
                                        <div class="doc-row flex items-start gap-3 opacity-0 translate-y-2 transition-all duration-300">
                                            <div class="flex-1">
                                                <input type="text" name="documents[]" value="" 
                                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all text-sm placeholder:text-slate-400"
                                                    placeholder="Ex: Nouveau document..." required>
                                            </div>
                                            <div class="w-44 shrink-0 relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <i class="fa-solid fa-list-check text-slate-400 text-xs"></i>
                                                </div>
                                                <select name="types[]" class="w-full pl-9 pr-8 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all text-sm appearance-none cursor-pointer">
                                                    <option value="file" selected>Fichier / Image</option>
                                                    <option value="text">Texte court</option>
                                                    <option value="number">Numérique</option>
                                                </select>
                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                                                </div>
                                            </div>
                                            <button type="button" class="remove-btn w-11 h-11 shrink-0 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white rounded-xl border border-red-100 flex items-center justify-center transition-colors" title="Supprimer">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    `;

                // Ajout d'une ligne
                addBtn.addEventListener('click', () => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = templateHtml;
                    const newRow = tempDiv.firstElementChild;
                    container.appendChild(newRow);

                    // Animation d'apparition
                    setTimeout(() => {
                        newRow.classList.remove('opacity-0', 'translate-y-2');
                    }, 10);

                    // Focus sur le nouveau champ
                    newRow.querySelector('input').focus();
                });

                // Suppression d'une ligne (Délégation d'événements)
                container.addEventListener('click', (e) => {
                    const btn = e.target.closest('.remove-btn');
                    if (btn) {
                        const row = btn.closest('.doc-row');
                        // S'il n'y a qu'une seule ligne, on vide juste l'input (pour toujours laisser un champ)
                        const rows = container.querySelectorAll('.doc-row');
                        if (rows.length === 1) {
                            row.querySelector('input').value = '';
                        } else {
                            // Animation de disparition puis suppression
                            row.style.opacity = '0';
                            row.style.transform = 'translateY(10px) scale(0.95)';
                            setTimeout(() => row.remove(), 250);
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection