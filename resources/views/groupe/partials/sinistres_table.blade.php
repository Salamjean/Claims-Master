@if ($collection->count() > 0)
    <div class="overflow-x-auto w-full">
        <table class="w-full text-center border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-sm border-b border-slate-100">
                    <th class="py-3 px-4 font-medium rounded-tl-lg text-center">Date</th>
                    <th class="py-3 px-4 font-medium text-center">Référence</th>
                    <th class="py-3 px-4 font-medium text-center">Type de sinistre</th>
                    <th class="py-3 px-4 font-medium text-center">Lieu</th>
                    <th class="py-3 px-4 font-medium text-center">Statut</th>
                    <th class="py-3 px-4 font-medium text-center rounded-tr-lg">Action</th>
                </tr>
            </thead>
            <tbody class="text-sm text-slate-700 divide-y divide-slate-50">
                @foreach ($collection as $sinistre)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3 px-4 text-center">{{ $sinistre->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4 font-medium text-slate-900 text-center">{{ $sinistre->numero_sinistre }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700">
                                <i class="fa-solid fa-car-burst text-[10px]"></i>
                                {{ ucfirst(str_replace('_', ' ', $sinistre->type_sinistre)) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div>{{ $sinistre->lieu ?? 'Non précisé' }}</div>
                            @if ($sinistre->latitude && $sinistre->longitude)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $sinistre->latitude }},{{ $sinistre->longitude }}"
                                    target="_blank"
                                    class="text-[11px] text-rose-600 font-medium hover:underline inline-flex items-center justify-center gap-1 mt-1">
                                    <i class="fa-solid fa-map-location-dot"></i> Itinéraire GPS
                                </a>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if ($sinistre->hospital_status === 'en_attente')
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">En
                                    attente</span>
                            @elseif($sinistre->hospital_status === 'ambulance_en_route')
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">En
                                    cours</span>
                            @elseif($sinistre->hospital_status === 'arrive')
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Arrivé</span>
                            @else
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">{{ ucfirst($sinistre->hospital_status) }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <!-- Actions adaptées au groupe -->
                            @if ($sinistre->hospital_status === 'en_attente')
                                @if (is_null($sinistre->assigned_groupe_id))
                                    <form action="{{ route('groupe.sinistres.recuperer', $sinistre) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <button
                                            class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded hover:bg-emerald-700 transition-colors">
                                            Récupérer
                                        </button>
                                    </form>
                                @elseif($sinistre->assigned_groupe_id === auth('user')->id())
                                    <form action="{{ route('groupe.sinistres.dispatch', $sinistre) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <button
                                            class="px-3 py-1.5 bg-rose-600 text-white text-xs font-medium rounded hover:bg-rose-700 transition-colors">
                                            Dépêcher équipe
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 italic">Récupéré par
                                        {{ $sinistre->assignedGroupe->name ?? 'une autre équipe' }}</span>
                                @endif
                            @elseif($sinistre->hospital_status === 'ambulance_en_route' && $sinistre->assigned_groupe_id === auth('user')->id())
                                <form action="{{ route('groupe.sinistres.arrive', $sinistre) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <button
                                        class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors">
                                        Signaler Arrivée
                                    </button>
                                </form>
                            @elseif($sinistre->hospital_status === 'arrive' && $sinistre->assigned_groupe_id === auth('user')->id())
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('groupe.sinistres.etat_des_lieux', $sinistre) }}"
                                        class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded hover:bg-indigo-700 transition-colors inline-block">
                                        {{ $sinistre->etatDesLieux ? 'Modifier état des lieux' : 'Faire état des lieux' }}
                                    </a>

                                    @if ($sinistre->etatDesLieux)
                                        <a href="{{ route('groupe.sinistres.etat_des_lieux.pdf', $sinistre) }}"
                                            class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded hover:bg-emerald-700 transition-colors inline-flex items-center gap-1">
                                            <i class="fa-solid fa-file-pdf"></i> Télécharger PDF
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-12 w-full">
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
            <i class="fa-solid fa-inbox text-2xl"></i>
        </div>
        <h3 class="text-slate-700 font-medium mb-1">{{ $emptyMessage ?? 'Aucune intervention' }}</h3>
        <p class="text-slate-400 text-sm">Il n'y a pas d'interventions dans cette section pour le moment.</p>
    </div>
@endif
