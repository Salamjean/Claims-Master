@extends(auth('user')->user()->role . '.layouts.template')

@section('title', 'Ajouter un Agent')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Fil d'Ariane --}}
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
            <a href="{{ route(auth('user')->user()->role . '.agents.index') }}" class="hover:text-blue-500 transition-colors">Agents</a>
            <i class="fa-solid fa-angle-right text-[10px]"></i>
            <span class="text-slate-600">Nouveau compte</span>
        </div>

        {{-- En-tête --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Ajouter un <span class="text-blue-600">Agent</span></h1>
                <p class="text-sm text-slate-500 mt-1">Créez un accès pour un nouvel agent de votre station.</p>
            </div>
            <a href="{{ route(auth('user')->user()->role . '.agents.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 px-3 py-2 rounded-lg hover:bg-slate-100 transition-all">
                <i class="fa-solid fa-arrow-left"></i> Annuler
            </a>
        </div>

        {{-- Formulaire --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
            <form action="{{ route(auth('user')->user()->role . '.agents.store') }}" method="POST" class="p-8 md:p-10">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    {{-- Nom --}}
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-bold text-slate-700">Nom de famille <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-user text-xs"></i>
                            </div>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500/50 transition-all"
                                placeholder="Ex: TRAORE">
                        </div>
                        @error('name') <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Prénom --}}
                    <div class="space-y-2">
                        <label for="prenom" class="block text-sm font-bold text-slate-700">Prénom(s)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-user-tag text-xs"></i>
                            </div>
                            <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}"
                                class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500/50 transition-all"
                                placeholder="Ex: Moussa">
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
                                class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500/50 transition-all"
                                placeholder="agent@police.gouv.ci">
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
                                class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500/50 transition-all"
                                placeholder="07 00 00 00 00">
                        </div>
                        @error('contact') <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-slate-100 flex items-center justify-end gap-4">
                    <p class="text-xs text-slate-400 font-medium mr-auto">
                        <i class="fa-solid fa-circle-info mr-1"></i> L'agent recevra son mot de passe par email.
                    </p>
                    <button type="submit" class="inline-flex items-center gap-2 px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold rounded-2xl transition-all shadow-lg shadow-blue-600/30 transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-check"></i>
                        Créer le compte Agent
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
