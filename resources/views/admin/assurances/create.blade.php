@extends('admin.layouts.template')

@section('title', 'Inscrire une Assurance')
@section('page-title', 'Inscrire une assurance')

@push('styles')
    <style>
        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .step-line {
            flex: 1;
            height: 2px;
            background: #e2e8f0;
        }

        .step-line.done {
            background: #243a8f;
        }

        .field-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
            display: block;
        }

        .field-input {
            width: 100%;
            padding: 10px 14px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.875rem;
            color: #1e293b;
            transition: all 0.2s;
            outline: none;
        }

        .field-input:focus {
            border-color: #243a8f;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(36, 58, 143, 0.08);
        }

        .field-input.error {
            border-color: #ef4444;
        }

        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            background: #f8fafc;
            transition: all 0.2s;
        }

        .upload-zone:hover {
            border-color: #243a8f;
            background: #eef1fb;
        }

        .upload-zone input[type=file] {
            display: none;
        }
    </style>
@endpush

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Nouvelle assurance</h1>
            <p class="text-sm text-slate-400 mt-0.5">Remplissez les informations pour inscrire une compagnie d'assurance.
            </p>
        </div>
        <a href="{{ route('admin.assurances.index') }}"
            class="flex items-center gap-2 text-sm text-slate-500 hover:text-primary border border-slate-200 hover:border-primary/30 px-4 py-2 rounded-xl transition-all">
            <i class="fa-solid fa-arrow-left text-xs"></i> Retour à la liste
        </a>
    </div>

    {{-- MESSAGES --}}
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
            <p class="font-semibold mb-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Erreurs de validation :</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.assurances.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- COLONNE PRINCIPALE --}}
            <div class="xl:col-span-2 space-y-5">

                {{-- Section : Informations générales --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                            style="background:rgba(36,58,143,0.1)">
                            <i class="fa-solid fa-building text-xs" style="color:#243a8f"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold text-slate-700">Informations générales</h2>
                            <p class="text-xs text-slate-400">Identité de la compagnie</p>
                        </div>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="field-label">Nom de la compagnie <span class="text-red-400">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Ex: AXA Assurances"
                                class="field-input {{ $errors->has('name') ? 'error' : '' }}">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="field-label">Représentant / Prénom</label>
                            <input type="text" name="prenom" value="{{ old('prenom') }}"
                                placeholder="Prénom du représentant" class="field-input">
                        </div>
                        <div>
                            <label class="field-label">Adresse email <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center text-slate-300 text-sm">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    placeholder="contact@assurance.com"
                                    class="field-input pl-9 {{ $errors->has('email') ? 'error' : '' }}">
                            </div>
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="field-label">Téléphone / Contact <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center text-slate-300 text-sm">
                                    <i class="fa-solid fa-phone"></i>
                                </span>
                                <input type="text" name="contact" value="{{ old('contact') }}"
                                    placeholder="+225 07 00 00 00"
                                    class="field-input pl-9 {{ $errors->has('contact') ? 'error' : '' }}">
                            </div>
                            @error('contact') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="field-label">Commune</label>
                            <input type="text" name="commune" value="{{ old('commune') }}" placeholder="Ex: Cocody"
                                class="field-input">
                        </div>
                        <div>
                            <label class="field-label">Adresse physique</label>
                            <input type="text" name="adresse" value="{{ old('adresse') }}"
                                placeholder="Ex: Rue des Jardins, Abidjan" class="field-input">
                        </div>
                    </div>
                </div>

                {{-- Section : Documents RCCM & DFE --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                            style="background:rgba(124,182,4,0.1)">
                            <i class="fa-solid fa-file-contract text-xs" style="color:#7cb604"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold text-slate-700">Documents légaux</h2>
                            <p class="text-xs text-slate-400">RCCM et DFE de la compagnie</p>
                        </div>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- RCCM --}}
                        <div class="space-y-3">
                            <div>
                                <label class="field-label">N° RCCM</label>
                                <input type="text" name="numero_rccm" value="{{ old('numero_rccm') }}"
                                    placeholder="Ex: CI-ABJ-2024-B-12345"
                                    class="field-input {{ $errors->has('numero_rccm') ? 'error' : '' }}">
                                @error('numero_rccm') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="field-label">Fiche RCCM <span class="text-slate-400 font-normal">(PDF, JPG,
                                        PNG — max 5 Mo)</span></label>
                                <label class="upload-zone block" for="path_rccm">
                                    <input type="file" id="path_rccm" name="path_rccm" accept=".pdf,.jpg,.jpeg,.png"
                                        onchange="showFilename(this, 'label_rccm')">
                                    <i class="fa-solid fa-cloud-arrow-up text-2xl mb-2 block" style="color:#243a8f"></i>
                                    <p class="text-xs text-slate-400" id="label_rccm">Cliquer pour téléverser la fiche RCCM
                                    </p>
                                </label>
                                @error('path_rccm') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- DFE --}}
                        <div class="space-y-3">
                            <div>
                                <label class="field-label">N° DFE</label>
                                <input type="text" name="numero_dfe" value="{{ old('numero_dfe') }}"
                                    placeholder="Ex: DFE-CI-2024-00123"
                                    class="field-input {{ $errors->has('numero_dfe') ? 'error' : '' }}">
                                @error('numero_dfe') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="field-label">Fiche DFE <span class="text-slate-400 font-normal">(PDF, JPG, PNG
                                        — max 5 Mo)</span></label>
                                <label class="upload-zone block" for="path_dfe">
                                    <input type="file" id="path_dfe" name="path_dfe" accept=".pdf,.jpg,.jpeg,.png"
                                        onchange="showFilename(this, 'label_dfe')">
                                    <i class="fa-solid fa-cloud-arrow-up text-2xl mb-2 block" style="color:#7cb604"></i>
                                    <p class="text-xs text-slate-400" id="label_dfe">Cliquer pour téléverser la fiche DFE
                                    </p>
                                </label>
                                @error('path_dfe') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            {{-- COLONNE DROITE : Mot de passe + Résumé --}}
            <div class="space-y-5">

                {{-- Mot de passe --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                            style="background:rgba(239,68,68,0.08)">
                            <i class="fa-solid fa-lock text-xs text-red-400"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-semibold text-slate-700">Accès & Sécurité</h2>
                            <p class="text-xs text-slate-400">Identifiants de connexion</p>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="field-label">Mot de passe <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <input type="password" id="password" name="password" placeholder="••••••••"
                                    class="field-input pr-10 {{ $errors->has('password') ? 'error' : '' }}">
                                <button type="button" onclick="togglePwd('password', 'eye1')"
                                    class="absolute inset-y-0 right-3 text-slate-300 hover:text-slate-500">
                                    <i class="fa-solid fa-eye text-sm" id="eye1"></i>
                                </button>
                            </div>
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="field-label">Confirmer le mot de passe <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    placeholder="••••••••" class="field-input pr-10">
                                <button type="button" onclick="togglePwd('password_confirmation', 'eye2')"
                                    class="absolute inset-y-0 right-3 text-slate-300 hover:text-slate-500">
                                    <i class="fa-solid fa-eye text-sm" id="eye2"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-slate-400">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            Le mot de passe doit contenir au minimum 8 caractères.
                        </p>
                    </div>
                </div>

                {{-- Résumé --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h2 class="text-sm font-semibold text-slate-700">Récapitulatif</h2>
                    </div>
                    <div class="p-6 space-y-3 text-sm text-slate-500">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-check-circle text-secondary text-base"></i>
                            <span>Un compte utilisateur sera créé avec le rôle <span
                                    class="font-semibold text-slate-700">assurance</span>.</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-check-circle text-secondary text-base"></i>
                            <span>Les fichiers RCCM et DFE seront stockés en toute sécurité.</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-check-circle text-secondary text-base"></i>
                            <span>L'assurance pourra se connecter avec l'email et le mot de passe fournis.</span>
                        </div>
                    </div>
                </div>

                {{-- Bouton submit --}}
                <button type="submit"
                    class="w-full py-3 rounded-2xl text-white font-semibold text-sm flex items-center justify-center gap-2 transition-all hover:opacity-90 active:scale-95"
                    style="background: linear-gradient(135deg, #243a8f, #1c2e72);">
                    <i class="fa-solid fa-shield-check"></i>
                    Inscrire l'assurance
                </button>

                <a href="{{ route('admin.assurances.index') }}"
                    class="w-full py-3 rounded-2xl text-slate-500 font-medium text-sm text-center border border-slate-200 hover:bg-slate-50 transition-all block">
                    Annuler
                </a>

            </div>
        </div>
    </form>

@endsection

@push('scripts')
    <script>
        function showFilename(input, labelId) {
            const label = document.getElementById(labelId);
            if (input.files && input.files[0]) {
                label.textContent = '✔ ' + input.files[0].name;
                label.style.color = '#7cb604';
                label.style.fontWeight = '600';
            }
        }

        function togglePwd(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
@endpush