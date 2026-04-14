@extends('assure.layouts.template')

@section('title', 'Ajouter une assurance')
@section('page-title', 'Ajouter une assurance')

@section('content')
    <div class="mx-auto" style="max-width: 80%;">
        <div class="mb-6">
            <a href="{{ route('assure.contrats.index') }}"
                class="text-slate-500 hover:text-blue-600 text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                Retour à la liste
            </a>
        </div>

        <div class="card overflow-hidden shadow-xl">
            <div class="bg-gradient-to-r from-blue-900 to-blue-900 p-8 text-white">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center border border-white/20">
                        <i class="fa-solid fa-car text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold">Nouvelle Assurance Automobile</h1>
                        <p class="text-blue-200 text-sm mt-1">Renseignez les détails de votre véhicule et de votre contrat
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('assure.contrats.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Informations Véhicule --}}
                    <div class="space-y-6">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Informations du Véhicule</h3>

                        <div class="space-y-2">
                            <label for="plaque" class="text-sm font-bold text-slate-700">Plaque d'immatriculation <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="plaque" id="plaque" value="{{ old('plaque') }}"
                                placeholder="Ex: 1234AB01"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 transition-all outline-none font-medium">
                            @error('plaque') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="marque" class="text-sm font-bold text-slate-700">Marque <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="marque" id="marque" value="{{ old('marque') }}"
                                    placeholder="Ex: Toyota"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 transition-all outline-none font-medium">
                                @error('marque') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label for="modele" class="text-sm font-bold text-slate-700">Modèle <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="modele" id="modele" value="{{ old('modele') }}"
                                    placeholder="Ex: Corolla"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 transition-all outline-none font-medium">
                                @error('modele') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="immatriculation" class="text-sm font-bold text-slate-700">N° d'immatriculation
                                (Carte Grise) <span class="text-red-500">*</span></label>
                            <input type="text" name="immatriculation" id="immatriculation"
                                value="{{ old('immatriculation') }}" placeholder="Ex: CH299253"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 transition-all outline-none font-medium">
                            @error('immatriculation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Informations Contrat --}}
                    <div class="space-y-6 border-l border-slate-100 md:pl-8">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Informations du Contrat</h3>

                        <div class="space-y-2">
                            <label for="numero_contrat" class="text-sm font-bold text-slate-700">Numéro du contrat <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="numero_contrat" id="numero_contrat" value="{{ old('numero_contrat') }}"
                                placeholder="Ex: POL-998877"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 transition-all outline-none font-medium">
                            @error('numero_contrat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Information Détection IA --}}
                        <div class="p-5 bg-blue-50 border border-blue-100 rounded-2xl flex gap-4 mt-2">
                            <div
                                class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shrink-0 shadow-lg shadow-blue-200">
                                <i class="fa-solid fa-robot text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-blue-900 uppercase tracking-tight">Détection par IA</h4>
                                <p class="text-xs text-blue-700 mt-1 leading-relaxed font-medium">
                                    Inutile de sélectionner votre assureur. Notre IA identifiera automatiquement votre
                                    compagnie à partir de l'attestation que vous téléverserez.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="type_vehicule" class="text-sm font-bold text-slate-700">Type de véhicule <span
                                    class="text-red-500">*</span></label>
                            <select name="type_vehicule" id="type_vehicule"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 transition-all outline-none font-medium appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em]"
                                style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E');">
                                <option value="">Sélectionnez le type</option>
                                <option value="Berline" {{ old('type_vehicule') == 'Berline' ? 'selected' : '' }}>Berline
                                </option>
                                <option value="SUV" {{ old('type_vehicule') == 'SUV' ? 'selected' : '' }}>SUV / 4x4</option>
                                <option value="Citadine" {{ old('type_vehicule') == 'Citadine' ? 'selected' : '' }}>Citadine
                                </option>
                                <option value="Camionnette" {{ old('type_vehicule') == 'Camionnette' ? 'selected' : '' }}>
                                    Camionnette</option>
                                <option value="Coupé" {{ old('type_vehicule') == 'Coupé' ? 'selected' : '' }}>Coupé</option>
                            </select>
                            @error('type_vehicule') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                    </div>
                </div>

                {{-- Section Documents Justificatifs --}}
                <div class="mt-12 pt-8 border-t border-slate-100">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-8">Documents Justificatifs</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {{-- Copie du contrat --}}
                        <div class="space-y-3">
                            <label class="text-sm font-bold text-slate-700">Copie du contrat (Image/PDF) <span
                                    class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 gap-4" id="container_document_pdf">
                                <label for="document_pdf"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-colors bg-white">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fa-solid fa-cloud-arrow-up text-blue-500 text-2xl mb-2"></i>
                                        <p class="text-[10px] text-slate-500 text-center px-2">Cliquez pour téléverser votre
                                            contrat</p>
                                    </div>
                                    <input id="document_pdf" name="document_pdf" type="file"
                                        class="hidden file-input-preview" data-preview="preview_document_pdf"
                                        accept="image/*,application/pdf" />
                                </label>
                                <div id="preview_document_pdf"
                                    class="hidden w-full h-32 border border-slate-200 rounded-2xl overflow-hidden bg-slate-50 relative group">
                                    <img src="" class="w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="text-white text-xs font-bold">Aperçu du contrat</span>
                                    </div>
                                </div>
                            </div>
                            @error('document_pdf') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Attestation d'assurance --}}
                        <div class="space-y-3">
                            <label class="text-sm font-bold text-slate-700">Attestation d'assurance <span
                                    class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 gap-4" id="container_attestation_assurance">
                                <label for="attestation_assurance"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-colors bg-white">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fa-solid fa-id-card text-emerald-500 text-2xl mb-2"></i>
                                        <p class="text-[10px] text-slate-500 text-center px-2">Cliquez pour téléverser
                                            l'attestation</p>
                                    </div>
                                    <input id="attestation_assurance" name="attestation_assurance" type="file"
                                        class="hidden file-input-preview" data-preview="preview_attestation_assurance"
                                        accept="image/*,application/pdf" />
                                </label>
                                <div id="preview_attestation_assurance"
                                    class="hidden w-full h-32 border border-slate-200 rounded-2xl overflow-hidden bg-slate-50 relative group">
                                    <img src="" class="w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="text-white text-xs font-bold font-asaci">Aperçu - Format ASACI</span>
                                    </div>
                                </div>
                            </div>
                            @error('attestation_assurance') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Carte Grise --}}
                        <div class="space-y-3">
                            <label class="text-sm font-bold text-slate-700">Carte Grise <span
                                    class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 gap-4" id="container_carte_grise">
                                <label for="carte_grise"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-colors bg-white">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fa-solid fa-file-invoice text-blue-500 text-2xl mb-2"></i>
                                        <p class="text-[10px] text-slate-500 text-center px-2">Cliquez pour téléverser la
                                            carte grise</p>
                                    </div>
                                    <input id="carte_grise" name="carte_grise" type="file" class="hidden file-input-preview"
                                        data-preview="preview_carte_grise" accept="image/*,application/pdf" />
                                </label>
                                <div id="preview_carte_grise"
                                    class="hidden w-full h-32 border border-slate-200 rounded-2xl overflow-hidden bg-slate-50 relative group">
                                    <img src="" class="w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="text-white text-xs font-bold">Aperçu Carte Grise</span>
                                    </div>
                                </div>
                            </div>
                            @error('carte_grise') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Visite Technique --}}
                        <div class="space-y-3">
                            <label class="text-sm font-bold text-slate-700">Visite Technique <span
                                    class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 gap-4" id="container_visite_technique">
                                <label for="visite_technique"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-colors bg-white">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fa-solid fa-clipboard-check text-orange-500 text-2xl mb-2"></i>
                                        <p class="text-[10px] text-slate-500 text-center px-2">Cliquez pour téléverser la
                                            visite technique</p>
                                    </div>
                                    <input id="visite_technique" name="visite_technique" type="file"
                                        class="hidden file-input-preview" data-preview="preview_visite_technique"
                                        accept="image/*,application/pdf" />
                                </label>
                                <div id="preview_visite_technique"
                                    class="hidden w-full h-32 border border-slate-200 rounded-2xl overflow-hidden bg-slate-50 relative group">
                                    <img src="" class="w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="text-white text-xs font-bold">Aperçu Visite Technique</span>
                                    </div>
                                </div>
                            </div>
                            @error('visite_technique') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Permis de Conduire --}}
                        <div class="space-y-3">
                            <label class="text-sm font-bold text-slate-700">Permis de Conduire <span
                                    class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 gap-4" id="container_permis_conduire">
                                <label for="permis_conduire"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-colors bg-white">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fa-solid fa-address-card text-purple-500 text-2xl mb-2"></i>
                                        <p class="text-[10px] text-slate-500 text-center px-2">Cliquez pour téléverser le
                                            permis</p>
                                    </div>
                                    <input id="permis_conduire" name="permis_conduire" type="file"
                                        class="hidden file-input-preview" data-preview="preview_permis_conduire"
                                        accept="image/*,application/pdf" />
                                </label>
                                <div id="preview_permis_conduire"
                                    class="hidden w-full h-32 border border-slate-200 rounded-2xl overflow-hidden bg-slate-50 relative group">
                                    <img src="" class="w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="text-white text-xs font-bold">Aperçu Permis</span>
                                    </div>
                                </div>
                            </div>
                            @error('permis_conduire') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-10 pt-8 border-t border-slate-100 flex justify-end">
                    <button type="submit" id="submitBtn"
                        class="px-10 py-4 bg-blue-600 hover:bg-blue-700 text-white font-extrabold rounded-2xl transition-all shadow-xl shadow-blue-200 flex items-center gap-3">
                        <i class="fa-solid fa-check"></i>
                        Enregistrer mon assurance
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // --- GESTION DES APERÇUS ---
                const fileInputs = document.querySelectorAll('.file-input-preview');

                fileInputs.forEach(input => {
                    input.addEventListener('change', function () {
                        const previewId = this.dataset.preview;
                        const previewDiv = document.getElementById(previewId);
                        const containerId = 'container_' + this.id;
                        const container = document.getElementById(containerId);
                        const img = previewDiv.querySelector('img');

                        if (this.files && this.files[0]) {
                            const file = this.files[0];
                            const reader = new FileReader();

                            reader.onload = function (e) {
                                if (file.type === 'application/pdf') {
                                    img.src = 'https://cdn-icons-png.flaticon.com/512/337/337946.png'; // Icône PDF par défaut
                                    img.classList.add('p-8');
                                } else {
                                    img.src = e.target.result;
                                    img.classList.remove('p-8');
                                }
                                previewDiv.classList.remove('hidden');
                                container.classList.replace('grid-cols-1', 'md:grid-cols-2');
                            }

                            reader.readAsDataURL(file);
                        }
                    });
                });

                // --- GESTION DU LOADING IA ---
                const form = document.querySelector('form');
                const submitBtn = document.getElementById('submitBtn');

                form.addEventListener('submit', function (e) {
                    // Affichage de l'overlay SweetAlert2
                    Swal.fire({
                        title: 'Analyse IA en cours...',
                        html: 'Nous vérifions la conformité de votre attestation (Format ASACI) et extrayons les informations.<br><br><b>Veuillez patienter quelques instants...</b>',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // On désactive le bouton pour éviter les doubles envois
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                });
            });
        </script>
    @endpush
@endsection