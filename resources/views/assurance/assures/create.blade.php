@extends('assurance.layouts.template')

@section('title', 'Inscrire un assuré')

@section('content')
    <div class="max-w-2xl mx-auto">

        {{-- En-tête --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('assurance.assures.index') }}"
                class="w-9 h-9 rounded-xl flex items-center justify-center bg-white border border-slate-200 text-slate-500 hover:text-slate-800 hover:border-slate-300 transition-all shadow-sm">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800">Inscrire un assuré</h1>
                <p class="text-sm text-slate-400">Un SMS sera envoyé avec le code et le mot de passe</p>
            </div>
        </div>

        {{-- Erreurs --}}
        @if($errors->any())
            <div class="mb-5 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
                <p class="font-semibold mb-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Erreurs :</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('assurance.assures.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

                {{-- Section informations --}}
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:rgba(36,58,143,0.1)">
                        <i class="fa-solid fa-user text-xs" style="color:#243a8f"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-slate-700">Informations de l'assuré</h2>
                        <p class="text-xs text-slate-400">Le code et le mot de passe seront générés automatiquement</p>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Nom --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1.5">
                            Nom <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nom de l'assuré"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm
                                                  focus:ring-2 focus:border-transparent outline-none transition-all
                                                  {{ $errors->has('name') ? 'border-red-300 bg-red-50' : 'bg-slate-50 focus:ring-blue-100' }}">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1"><i
                                    class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Prénom --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1.5">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}" placeholder="Prénom de l'assuré"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm
                                                  focus:ring-2 focus:ring-blue-100 focus:border-transparent outline-none transition-all">
                    </div>

                    {{-- Contact --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1.5">
                            Contact (téléphone) <span class="text-red-400">*</span>
                            <span class="text-xs text-slate-400 font-normal ml-1">— Le SMS sera envoyé ici</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                                <i class="fa-solid fa-phone text-xs"></i>
                            </span>
                            <input type="text" name="contact" value="{{ old('contact') }}" placeholder="Ex: 0700000000"
                                class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm
                                                      focus:ring-2 focus:border-transparent outline-none transition-all
                                                      {{ $errors->has('contact') ? 'border-red-300 bg-red-50' : 'bg-slate-50 focus:ring-blue-100' }}">
                        </div>
                        @error('contact')
                            <p class="text-red-500 text-xs mt-1"><i
                                    class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1.5">
                            Email <span class="text-slate-400 text-xs font-normal">(optionnel)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                                <i class="fa-solid fa-envelope text-xs"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="email@exemple.com"
                                class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm
                                                      focus:ring-2 focus:border-transparent outline-none transition-all
                                                      {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'bg-slate-50 focus:ring-blue-100' }}">
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1"><i
                                    class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Adresse --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-600 mb-1.5">Adresse</label>
                        <input type="text" name="adresse" value="{{ old('adresse') }}" placeholder="Adresse de l'assuré"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm
                                                  focus:ring-2 focus:ring-blue-100 focus:border-transparent outline-none transition-all">
                    </div>
                </div>

                {{-- INFO : ce qui sera généré --}}
                <div class="mx-6 mb-6 rounded-xl px-4 py-3 flex items-start gap-3"
                    style="background:rgba(124,182,4,0.08); border:1px solid rgba(124,182,4,0.25)">
                    <i class="fa-solid fa-circle-info mt-0.5 text-sm" style="color:#7cb604"></i>
                    <div class="text-sm" style="color:#5a8a03">
                        <p class="font-semibold">Ce qui sera généré automatiquement :</p>
                        <ul class="mt-1 space-y-0.5 text-xs">
                            <li>• <strong>Code assuré</strong> : format <code
                                    class="bg-white/60 px-1 rounded">CM-XXXXXX-{{ date('Y') }}</code></li>
                            <li>• <strong>Mot de passe</strong> : 8 caractères aléatoires</li>
                            <li>• Un <strong>SMS</strong> sera envoyé au numéro de contact avec ces informations</li>
                            <li>• Un <strong>email</strong> sera aussi envoyé si une adresse email est renseignée</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Section contrat --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mt-5">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:rgba(124,182,4,0.1)">
                        <i class="fa-solid fa-file-contract text-xs" style="color:#7cb604"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-slate-700">Informations du contrat</h2>
                        <p class="text-xs text-slate-400">Renseignez les détails du contrat d'assurance</p>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1.5">Numéro de contrat <span
                                class="text-red-400">*</span></label>
                        <input type="text" name="numero_contrat" value="{{ old('numero_contrat') }}"
                            placeholder="Ex: CTR-2026-001"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm focus:ring-2 focus:border-transparent outline-none transition-all {{ $errors->has('numero_contrat') ? 'border-red-300 bg-red-50' : 'bg-slate-50 focus:ring-blue-100' }}">
                        @error('numero_contrat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1.5">Type de contrat <span
                                class="text-red-400">*</span></label>
                        <select name="type_contrat"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm focus:ring-2 focus:border-transparent outline-none transition-all {{ $errors->has('type_contrat') ? 'border-red-300 bg-red-50' : 'bg-slate-50 focus:ring-blue-100' }}">
                            <option value="">Sélectionner un type</option>
                            <option value="auto" {{ old('type_contrat') == 'auto' ? 'selected' : '' }}>🚗 Automobile</option>
                            <option value="habitation" {{ old('type_contrat') == 'habitation' ? 'selected' : '' }}>🏠
                                Habitation</option>
                            <option value="sante" {{ old('type_contrat') == 'sante' ? 'selected' : '' }}>🏥 Santé</option>
                            <option value="vie" {{ old('type_contrat') == 'vie' ? 'selected' : '' }}>❤️ Vie</option>
                            <option value="autre" {{ old('type_contrat') == 'autre' ? 'selected' : '' }}>📋 Autre</option>
                        </select>
                        @error('type_contrat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1.5">Date de début <span
                                class="text-red-400">*</span></label>
                        <input type="date" name="date_debut" value="{{ old('date_debut') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm focus:ring-2 focus:border-transparent outline-none transition-all {{ $errors->has('date_debut') ? 'border-red-300 bg-red-50' : 'bg-slate-50 focus:ring-blue-100' }}">
                        @error('date_debut')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1.5">Date de fin <span
                                class="text-slate-400 text-xs font-normal">(optionnel)</span></label>
                        <input type="date" name="date_fin" value="{{ old('date_fin') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm focus:ring-2 focus:ring-blue-100 focus:border-transparent outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1.5">Prime (FCFA) <span
                                class="text-slate-400 text-xs font-normal">(optionnel)</span></label>
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 left-3 flex items-center text-slate-400 text-xs font-medium">FCFA</span>
                            <input type="number" name="prime" value="{{ old('prime') }}" placeholder="0" min="0" step="1"
                                class="w-full pl-14 pr-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm focus:ring-2 focus:ring-blue-100 focus:border-transparent outline-none transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1.5">Statut <span
                                class="text-red-400">*</span></label>
                        <select name="statut"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm focus:ring-2 focus:border-transparent outline-none transition-all {{ $errors->has('statut') ? 'border-red-300 bg-red-50' : 'bg-slate-50 focus:ring-blue-100' }}">
                            <option value="actif" {{ old('statut', 'actif') == 'actif' ? 'selected' : '' }}>✅ Actif</option>
                            <option value="suspendu" {{ old('statut') == 'suspendu' ? 'selected' : '' }}>⏸️ Suspendu</option>
                            <option value="resilie" {{ old('statut') == 'resilie' ? 'selected' : '' }}>❌ Résilié</option>
                            <option value="expire" {{ old('statut') == 'expire' ? 'selected' : '' }}>⌛ Expiré</option>
                        </select>
                        @error('statut')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-600 mb-1.5">Document PDF <span
                                class="text-slate-400 text-xs font-normal">(optionnel, max 5 Mo)</span></label>
                        <input type="file" name="document_pdf" accept=".pdf"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error('document_pdf')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 mt-5">
                <a href="{{ route('assurance.assures.index') }}"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition-all">
                    Annuler
                </a>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold flex items-center gap-2 shadow-sm transition-all hover:opacity-90 active:scale-95"
                    style="background: linear-gradient(135deg, #243a8f, #1c2e72);">
                    <i class="fa-solid fa-paper-plane"></i>
                    Inscrire et envoyer le SMS
                </button>
            </div>
        </form>
    </div>
@endsection