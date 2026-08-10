@extends('hopital.layouts.template')

@section('title', 'Rapports d\'Intervention')
@section('page-title', 'Rapports d\'Intervention')

@section('content')
<div class="space-y-6">

    <!-- En-tête -->
    <div class="rounded-3xl p-6 md:p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 border border-white/10" style="background-color: #B9123C;">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 rounded-full text-xs font-bold text-white mb-2 backdrop-blur-md">
                <i class="fa-solid fa-file-signature"></i> Commandement & Contrôle
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Rapports d'Intervention (États des Lieux)</h1>
            <p class="text-rose-100 text-xs md:text-sm mt-1 max-w-2xl">
                Consultez, examinez et validez les rapports officiels d'état des lieux rédigés sur le terrain par vos groupes d'intervention.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('hopital.rapports_intervention') }}" class="px-4 py-2.5 bg-white/20 hover:bg-white/30 text-white rounded-2xl text-xs font-extrabold transition-all border border-white/30 flex items-center gap-2">
                <i class="fa-solid fa-rotate"></i> Actualiser
            </a>
        </div>
    </div>

    <!-- Cartes de statistiques & Filtres -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('hopital.rapports_intervention') }}" class="p-5 rounded-2xl border transition-all shadow-sm flex items-center justify-between {{ !request('status') ? 'bg-rose-50/80 border-rose-300 ring-2 ring-rose-500/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div>
                <span class="text-xs font-bold text-slate-500 block uppercase">Tous les Rapports</span>
                <span class="text-2xl font-black text-slate-800">{{ $reports->total() }}</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                <i class="fa-solid fa-folder-open text-lg"></i>
            </div>
        </a>

        <a href="{{ route('hopital.rapports_intervention', ['status' => 'en_attente']) }}" class="p-5 rounded-2xl border transition-all shadow-sm flex items-center justify-between {{ request('status') === 'en_attente' ? 'bg-amber-50/80 border-amber-300 ring-2 ring-amber-500/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div>
                <span class="text-xs font-bold text-amber-700 block uppercase">En Attente de Validation</span>
                <span class="text-2xl font-black text-amber-800">
                    {{ \App\Models\EtatDesLieux::whereHas('sinistre', function($q) use ($user) { $q->where('nearest_hospital_id', $user->id); })->where('status', 'en_attente')->count() }}
                </span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                <i class="fa-solid fa-clock text-lg"></i>
            </div>
        </a>

        <a href="{{ route('hopital.rapports_intervention', ['status' => 'valide']) }}" class="p-5 rounded-2xl border transition-all shadow-sm flex items-center justify-between {{ request('status') === 'valide' ? 'bg-emerald-50/80 border-emerald-300 ring-2 ring-emerald-500/20' : 'bg-white border-slate-200 hover:border-slate-300' }}">
            <div>
                <span class="text-xs font-bold text-emerald-700 block uppercase">Rapports Validés</span>
                <span class="text-2xl font-black text-emerald-800">
                    {{ \App\Models\EtatDesLieux::whereHas('sinistre', function($q) use ($user) { $q->where('nearest_hospital_id', $user->id); })->where('status', 'valide')->count() }}
                </span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                <i class="fa-solid fa-circle-check text-lg"></i>
            </div>
        </a>
    </div>

    <!-- BARRE DE RECHERCHE ET FILTRES AVANCÉS -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('hopital.rapports_intervention') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-center">
            
            {{-- Recherche Mot-clé --}}
            <div class="md:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="N° Sinistre, Lieu, Signataire, Caserne..."
                    class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:border-rose-500 focus:ring-2 focus:ring-rose-500/10 focus:outline-none transition-all" />
            </div>

            {{-- Filtre Statut --}}
            <div>
                <select name="status" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:border-rose-500 focus:outline-none transition-all">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" {{ request('status') === 'en_attente' ? 'selected' : '' }}>En attente de validation</option>
                    <option value="valide" {{ request('status') === 'valide' ? 'selected' : '' }}>Validé & Verrouillé</option>
                </select>
            </div>

            {{-- Filtre Nature d'intervention --}}
            <div>
                <select name="nature" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:border-rose-500 focus:outline-none transition-all">
                    <option value="">Toutes les natures</option>
                    <option value="Accident de circulation" {{ request('nature') === 'Accident de circulation' ? 'selected' : '' }}>Accident de circulation</option>
                    <option value="Incendie" {{ request('nature') === 'Incendie' ? 'selected' : '' }}>Incendie</option>
                    <option value="Malaise" {{ request('nature') === 'Malaise' ? 'selected' : '' }}>Malaise / Secours</option>
                    <option value="Sauvetage" {{ request('nature') === 'Sauvetage' ? 'selected' : '' }}>Sauvetage</option>
                    <option value="Inondation" {{ request('nature') === 'Inondation' ? 'selected' : '' }}>Inondation</option>
                    <option value="Fuite de gaz" {{ request('nature') === 'Fuite de gaz' ? 'selected' : '' }}>Fuite de gaz</option>
                    <option value="Effondrement" {{ request('nature') === 'Effondrement' ? 'selected' : '' }}>Effondrement</option>
                </select>
            </div>

            {{-- Boutons d'Action --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-xl text-xs transition-all shadow-sm flex items-center justify-center gap-1.5 border border-rose-500">
                    <i class="fa-solid fa-filter"></i> Filtrer
                </button>

                @if(request()->anyFilled(['search', 'status', 'nature', 'date']))
                    <a href="{{ route('hopital.rapports_intervention') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all border border-slate-200" title="Réinitialiser les filtres">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tableau des rapports -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-list-check text-rose-600"></i> Liste des Procès-Verbaux
            </h2>
            <span class="text-xs text-slate-400 font-medium">Affichage des 15 derniers rapports</span>
        </div>

        @if($reports->isEmpty())
            <div class="p-12 text-center space-y-3">
                <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-3xl flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-file-circle-xmark"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Aucun rapport d'intervention trouvé</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">
                    Aucun état des lieux ne correspond aux critères de recherche et filtres sélectionnés.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase text-[10px] font-black tracking-wider">
                        <tr>
                            <th class="px-6 py-4">N° Sinistre / Date</th>
                            <th class="px-6 py-4">Groupe d'Intervention</th>
                            <th class="px-6 py-4">Nature & Gravité</th>
                            <th class="px-6 py-4">Agent Signataire</th>
                            <th class="px-6 py-4 text-center">Statut</th>
                            <th class="px-6 py-4 text-right">Actions & Validation</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                        @foreach($reports as $report)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-extrabold text-slate-900 text-xs">
                                        {{ $report->sinistre->numero_sinistre ?? '#' . $report->sinistre->id }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-medium mt-0.5">
                                        {{ $report->created_at->format('d/m/Y à H:i') }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-bold shrink-0">
                                            <i class="fa-solid fa-users"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800">{{ $report->groupe->name ?? 'Groupe Intervenant' }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $report->casernes_mobilisees ?? 'Caserne Sapeurs-Pompiers' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $report->nature_intervention ?? 'Intervention' }}</div>
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                                        Gravité : {{ $report->niveau_gravite ?? 'Normale' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5 text-slate-800">
                                        <i class="fa-solid fa-signature text-rose-600"></i>
                                        <span>{{ $report->nom_agent_signataire ?? 'Non renseigné' }}</span>
                                    </div>
                                    @if($report->signature_agent)
                                        <span class="text-[10px] text-emerald-600 font-bold block mt-0.5">✓ Émargé numériquement</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if($report->status === 'valide')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                            <i class="fa-solid fa-check-double text-emerald-600"></i> Validé
                                        </span>
                                        <span class="block text-[10px] text-slate-400 mt-1">
                                            le {{ $report->validated_at ? $report->validated_at->format('d/m/Y H:i') : '' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-700 border border-amber-200 animate-pulse">
                                            <i class="fa-solid fa-hourglass-half"></i> En attente
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Bouton Consulter -->
                                        <a href="{{ route('hopital.sinistres.show', $report->sinistre) }}"
                                            class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold rounded-xl transition-all inline-flex items-center gap-1.5 text-xs shadow-sm">
                                            <i class="fa-solid fa-eye text-rose-400"></i> Consulter
                                        </a>

                                        @if($report->status !== 'valide')
                                            <form action="{{ route('hopital.etat_des_lieux.valider', $report) }}" method="POST" class="inline" onsubmit="return confirmValidation(this, '{{ $report->sinistre->numero_sinistre ?? '#' . $report->sinistre->id }}')">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl transition-all shadow-md hover:shadow-lg inline-flex items-center gap-1.5 text-xs border border-emerald-500">
                                                    <i class="fa-solid fa-check-circle"></i> Valider
                                                </button>
                                            </form>
                                        @else
                                            <span class="px-3 py-2 bg-slate-100 text-slate-400 font-bold rounded-xl text-xs inline-flex items-center gap-1 border border-slate-200">
                                                <i class="fa-solid fa-lock text-slate-400"></i> Verrouillé
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-100">
                {{ $reports->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    function confirmValidation(formElement, sinistreNum) {
        event.preventDefault();
        Swal.fire({
            title: 'Valider le rapport d\'intervention ?',
            html: `Vous êtes sur le point de valider le procès-verbal d'état des lieux pour le dossier <b>${sinistreNum}</b>.<br><br><span style="font-size: 12px; color: #be123c; font-weight: bold;">⚠️ Une fois validé, ce rapport sera verrouillé et le groupe ne pourra plus le modifier.</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa-solid fa-check-circle mr-1"></i> Oui, valider le rapport',
            cancelButtonText: 'Annuler',
            customClass: {
                popup: 'rounded-3xl shadow-2xl',
                confirmButton: 'px-5 py-2.5 rounded-xl font-bold text-xs',
                cancelButton: 'px-5 py-2.5 rounded-xl font-bold text-xs'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                formElement.submit();
            }
        });
        return false;
    }
</script>
@endpush
@endsection
