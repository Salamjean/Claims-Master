@extends('assurance.layouts.template')

@section('title', isset($expert) ? 'Modifier l\'expert' : 'Ajouter un expert')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-800">
                    {{ isset($expert) ? 'Modifier l\'expert' : 'Ajouter un expert' }}</h1>
                <p class="text-sm text-slate-400">Renseignez les informations de ce partenaire</p>
            </div>
            <a href="{{ route('assurance.experts.index') }}"
                class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                <i class="fa-solid fa-arrow-left mr-1"></i> Retour à la liste
            </a>
        </div>

        <form action="{{ isset($expert) ? route('assurance.experts.update', $expert) : route('assurance.experts.store') }}"
            method="POST" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
            @csrf
            @if(isset($expert)) @method('PUT') @endif

            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nom</label>
                    <input type="text" name="name" value="{{ old('name', $expert->name ?? '') }}"
                        class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring focus:ring-emerald-200 transition-all text-sm"
                        required>
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Prénom(s)</label>
                    <input type="text" name="prenom" value="{{ old('prenom', $expert->prenom ?? '') }}"
                        class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring focus:ring-emerald-200 transition-all text-sm">
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $expert->email ?? '') }}"
                        class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring focus:ring-emerald-200 transition-all text-sm"
                        required>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Téléphone</label>
                    <input type="text" name="contact" value="{{ old('contact', $expert->contact ?? '') }}"
                        class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring focus:ring-emerald-200 transition-all text-sm"
                        required>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Adresse / Cabinet</label>
                    <input type="text" name="adresse" value="{{ old('adresse', $expert->adresse ?? '') }}"
                        class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring focus:ring-emerald-200 transition-all text-sm"
                        required>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-all shadow-sm">
                    {{ isset($expert) ? 'Mettre à jour' : 'Enregistrer cet expert' }}
                </button>
            </div>
        </form>
    </div>
@endsection