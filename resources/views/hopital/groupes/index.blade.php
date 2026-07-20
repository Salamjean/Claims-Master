@extends('hopital.layouts.template')

@section('title', 'Gestion des Groupes')

@section('content')
    <div class="space-y-6 mx-auto" style="width: 75%;">

        {{-- En-tête --}}
        <div class="relative rounded-2xl overflow-hidden text-white px-8 py-7"
            style="background: linear-gradient(135deg, #be123c 0%, #881337 100%);">
            <div
                style="position:absolute;top:-50px;right:-50px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,0.07),transparent);pointer-events:none;">
            </div>
            <div class="relative flex flex-wrap items-center justify-between gap-5">
                <div>
                    <h1 class="text-2xl font-extrabold">Gestion des <span class="text-rose-200">Groupes</span> 🚒</h1>
                    <p class="text-sm text-white/70 mt-1">Inscrivez et gérez les groupes / équipes de votre caserne ({{ $hopital->name }})</p>
                </div>
                <div>
                    <a href="{{ route('hopital.groupes.create') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-rose-700 hover:bg-rose-50 font-bold rounded-xl transition-all shadow-lg">
                        <i class="fa-solid fa-plus text-xs"></i>
                        Inscrire un Groupe
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <p class="text-sm font-bold">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-100 text-red-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
                <i class="fa-solid fa-circle-exclamation text-xl"></i>
                <p class="text-sm font-bold">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Table des groupes --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center">
                        <i class="fa-solid fa-users-viewfinder text-rose-600 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-bold text-slate-800">Liste des groupes inscrits</h2>
                </div>
                <div class="text-xs text-slate-400 font-medium">
                    Total : {{ count($groupes) }} groupe(s)
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nom / Groupe</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Rôle</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Date d'inscription</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($groupes as $groupe)
                            @php
                                $isActivated = !is_null($groupe->email_verified_at) && !$groupe->must_change_password;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold">
                                            <i class="fa-solid fa-user-group"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800">{{ $groupe->name }} {{ $groupe->prenom }}</p>
                                            <p class="text-xs text-slate-400">{{ $groupe->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $groupe->contact }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        {{ ucfirst($groupe->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($isActivated)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i> Activé
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="fa-solid fa-clock text-[10px]"></i> En attente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-slate-500">
                                    {{ $groupe->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if(!$isActivated)
                                            <form action="{{ route('hopital.groupes.resend_activation', $groupe->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all shadow-sm flex items-center gap-1.5" title="Renvoyer l'email d'activation">
                                                    <i class="fa-solid fa-paper-plane text-[11px]"></i>
                                                    Renvoyer l'email
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('hopital.groupes.destroy', $groupe->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce groupe ?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-9 h-9 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center border border-red-100" title="Supprimer">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-users-slash text-4xl mb-4 block opacity-20"></i>
                                    <p class="text-sm font-medium">Aucun groupe n'a encore été inscrit par votre caserne.</p>
                                    <a href="{{ route('hopital.groupes.create') }}" class="text-rose-600 hover:underline text-xs mt-2 block font-semibold">
                                        Inscrire un premier groupe
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
