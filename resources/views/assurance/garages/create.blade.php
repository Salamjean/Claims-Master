@extends('assurance.layouts.template')

@section('title', isset($garage) ? 'Modifier le garage' : 'Ajouter un garage')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-800">
                    {{ isset($garage) ? 'Modifier le garage' : 'Ajouter un garage' }}
                </h1>
                <p class="text-sm text-slate-400">Renseignez les informations de ce partenaire de réparation</p>
            </div>
            <a href="{{ route('assurance.garages.index') }}"
                class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                <i class="fa-solid fa-arrow-left mr-1"></i> Retour à la liste
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg">
                <p class="font-bold mb-1">Oups ! Il y a des erreurs dans votre formulaire :</p>
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($garage) ? route('assurance.garages.update', $garage) : route('assurance.garages.store') }}"
            method="POST" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
            @csrf
            @if(isset($garage)) @method('PUT') @endif

            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nom du Garage</label>
                    <input type="text" name="name" value="{{ old('name', $garage->name ?? '') }}"
                        class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all text-sm"
                        required>
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email de contact</label>
                    <input type="email" name="email" value="{{ old('email', $garage->email ?? '') }}"
                        class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all text-sm"
                        required>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Téléphone</label>
                    <input type="text" name="contact" value="{{ old('contact', $garage->contact ?? '') }}"
                        class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all text-sm"
                        required>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Adresse / Emplacement</label>
                    <input type="text" name="adresse" value="{{ old('adresse', $garage->adresse ?? '') }}"
                        class="w-full h-11 px-4 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all text-sm"
                        required>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit"
                    class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl text-sm transition-all shadow-sm">
                    {{ isset($garage) ? 'Mettre à jour' : 'Enregistrer ce garage' }}
                </button>
            </div>
        </form>
    </div>
@endsection