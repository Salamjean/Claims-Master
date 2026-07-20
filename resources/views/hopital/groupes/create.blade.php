@extends('hopital.layouts.template')

@section('title', 'Inscrire un Groupe')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6" style="width: 75%;">

        {{-- Fil d'Ariane --}}
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
            <a href="{{ route('hopital.groupes.index') }}" class="hover:text-rose-600 transition-colors">Groupes</a>
            <i class="fa-solid fa-angle-right text-[10px]"></i>
            <span class="text-slate-600">Nouvelle Inscription (Rôle Groupe)</span>
        </div>

        {{-- En-tête --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Inscrire un <span class="text-rose-600">Groupe</span></h1>
                <p class="text-sm text-slate-500 mt-1">Créez un compte utilisateur avec le rôle <strong class="text-slate-700">Groupe</strong> rattaché à votre caserne.</p>
            </div>
            <a href="{{ route('hopital.groupes.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 px-3 py-2 rounded-lg hover:bg-slate-100 transition-all">
                <i class="fa-solid fa-arrow-left"></i> Annuler
            </a>
        </div>

        {{-- Formulaire --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
            <form action="{{ route('hopital.groupes.store') }}" method="POST" class="p-8 md:p-10">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    {{-- Nom du groupe / Responsable --}}
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-bold text-slate-700">Nom du groupe <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-users text-xs"></i>
                            </div>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500/50 transition-all"
                                placeholder="Ex: Groupe Secours Nord">
                        </div>
                        @error('name') <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Prénom / Complément --}}
                    <div class="space-y-2">
                        <label for="prenom" class="block text-sm font-bold text-slate-700">Prénom ou Référence</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-user-tag text-xs"></i>
                            </div>
                            <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}"
                                class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500/50 transition-all"
                                placeholder="Ex: Équipe Alpha">
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-bold text-slate-700">Adresse Email <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-envelope text-xs"></i>
                            </div>
                            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500/50 transition-all"
                                placeholder="groupe@sapeur-pompier.ci">
                        </div>
                        <p class="text-[10px] text-slate-400 font-medium italic mt-1">Un lien d'activation sera envoyé à cette adresse.</p>
                        @error('email') <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Contact --}}
                    <div class="space-y-2">
                        <label for="contact" class="block text-sm font-bold text-slate-700">Numéro de Téléphone <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-phone text-xs"></i>
                            </div>
                            <input type="text" name="contact" id="contact" required value="{{ old('contact') }}"
                                class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500/50 transition-all"
                                placeholder="07 00 00 00 00">
                        </div>
                        @error('contact') <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-slate-100 flex items-center justify-end gap-4">
                    <p class="text-xs text-slate-400 font-medium mr-auto">
                        <i class="fa-solid fa-circle-info mr-1"></i> Le compte sera créé avec le rôle <strong class="text-slate-600">groupe</strong>.
                    </p>
                    <button type="submit" class="inline-flex items-center gap-2 px-8 py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-2xl transition-all shadow-lg shadow-rose-600/30 transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-check"></i>
                        Inscrire le Groupe
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
