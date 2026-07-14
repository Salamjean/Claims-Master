@extends('hopital.layouts.template')

@section('title', 'Gestion de la Capacité')

@section('content')
    <div class="space-y-6 mx-auto" style="width: 70%;">
        {{-- En-tête --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Paramètres & Capacité</h1>
            <p class="text-slate-500 text-sm mt-1">Gérez la disponibilité opérationnelle de votre établissement en temps réel.</p>
        </div>

        {{-- Messages d'alerte --}}
        @if (session('success'))
            <div class="p-4 rounded-xl bg-green-50 border border-green-100 text-green-700 text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Formulaire --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <form action="{{ route('hopital.capacite.update') }}" method="POST" class="space-y-6">
                @csrf

                <div class="space-y-4">
                    <label class="block text-sm font-semibold text-slate-700">Disponibilité de l'ambulance</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex items-center p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/30">
                            <input type="radio" name="has_ambulance" value="1" {{ $user->has_ambulance ? 'checked' : '' }}
                                class="w-4 h-4 text-rose-600 focus:ring-rose-500 border-slate-300">
                            <div class="ml-3 flex items-center gap-2">
                                <i class="fa-solid fa-truck-medical text-green-500"></i>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Ambulance Disponible</p>
                                    <p class="text-xs text-slate-400">Prête à être dépêchée en cas d'accident.</p>
                                </div>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-all has-[:checked]:border-slate-500 has-[:checked]:bg-slate-50/50">
                            <input type="radio" name="has_ambulance" value="0" {{ !$user->has_ambulance ? 'checked' : '' }}
                                class="w-4 h-4 text-slate-600 focus:ring-slate-500 border-slate-300">
                            <div class="ml-3 flex items-center gap-2">
                                <i class="fa-solid fa-truck-medical text-slate-400 grayscale"></i>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Indisponible / En intervention</p>
                                    <p class="text-xs text-slate-400">Aucune ambulance libre actuellement.</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex items-center justify-end pt-6 border-t border-slate-100">
                    <button type="submit"
                        class="px-8 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold flex items-center shadow-lg shadow-rose-600/20 transition-all">
                        <i class="fa-solid fa-save mr-2"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
