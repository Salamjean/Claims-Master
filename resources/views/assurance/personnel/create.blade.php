@extends('assurance.layouts.template')

@section('title', 'Ajouter un membre du personnel')

@section('content')
    <div class="max-w-2xl mx-auto">

        {{-- En-tête --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('assurance.personnel.index') }}"
                class="w-9 h-9 rounded-xl flex items-center justify-center border border-slate-200 text-slate-500 hover:bg-slate-50 transition-all">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800">Ajouter un membre du personnel</h1>
                <p class="text-sm text-slate-400">Un email d'activation sera envoyé automatiquement.</p>
            </div>
        </div>

        {{-- Erreurs de validation --}}
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulaire --}}
        <form action="{{ route('assurance.personnel.store') }}" method="POST">
            @csrf

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Nom --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition"
                            placeholder="Dupont" required>
                    </div>

                    {{-- Prénom --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                            Prénom
                        </label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition"
                            placeholder="Jean">
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                        Adresse email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition"
                        placeholder="jean.dupont@example.com" required>
                    <p class="mt-1 text-xs text-slate-400">Un email d'activation avec un code sera envoyé à cette adresse.
                    </p>
                </div>

                {{-- Contact --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                        Contact / Téléphone <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="contact" value="{{ old('contact') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition"
                        placeholder="+221 77 000 00 00" required>
                </div>

                {{-- Poste --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                        Poste / Fonction
                    </label>
                    <input type="text" name="poste" value="{{ old('poste') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition"
                        placeholder="Gestionnaire sinistres">
                </div>

            </div>

            {{-- Boutons --}}
            <div class="mt-5 flex items-center justify-end gap-3">
                <a href="{{ route('assurance.personnel.index') }}"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                    Annuler
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 active:scale-95 transition shadow-sm shadow-indigo-200">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    Créer et envoyer l'invitation
                </button>
            </div>
        </form>
    </div>
@endsection
