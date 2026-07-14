@extends('hopital.layouts.template')

@section('title', 'Historique des Prises en Charge')

@section('content')
    <div class="space-y-6 mx-auto" style="width: 100%;">
        {{-- En-tête --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Historique des Prises en Charge</h1>
            <p class="text-slate-500 text-sm mt-1">Consultez les dossiers des patients traités ou des alertes terminées.</p>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Type d'Accident</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Gravité Bilan</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Notes Médicales</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Date Prise en charge</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($sinistres as $sinistre)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($sinistre->assure->name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800">{{ $sinistre->assure->name ?? '—' }} {{ $sinistre->assure->prenom ?? '' }}</p>
                                            <p class="text-xs text-slate-500">{{ $sinistre->assure->contact ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-medium bg-slate-100 text-slate-700">
                                        <i class="fa-solid fa-car-burst text-[10px]"></i>
                                        {{ str_replace('_', ' ', $sinistre->type_sinistre) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($sinistre->hospital_severity === 'leger')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                            Léger
                                        </span>
                                    @elseif($sinistre->hospital_severity === 'grave')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800">
                                            Grave
                                        </span>
                                    @elseif($sinistre->hospital_severity === 'deces')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                            Décès
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">Non spécifié</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs text-slate-600 line-clamp-2 max-w-[300px]">{{ $sinistre->hospital_notes ?? 'Pas de note saisie.' }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs text-slate-500 font-medium">{{ $sinistre->updated_at->format('d/m/Y H:i') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center mx-auto mb-3 text-slate-300">
                                        <i class="fa-solid fa-clock text-lg"></i>
                                    </div>
                                    <p class="text-slate-500 font-medium">Aucun historique de prise en charge trouvé.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($sinistres->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $sinistres->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
