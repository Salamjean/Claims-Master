@extends('assurance.layouts.template')

@section('title', 'Gestion des dossiers Claims AI')

@section('content')
    <div>
        {{-- En-tête --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Dossiers Sinistres</h1>
                <p class="text-sm text-slate-400">Examen et validation des documents (Claims AI)</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('assurance.sinistres.index') }}"
                    class="px-4 py-2 border {{ request('status') !== 'review' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-slate-200 text-slate-600 bg-white hover:bg-slate-50' }} rounded-xl text-sm font-semibold transition-all">
                    Tous
                </a>
                <a href="{{ route('assurance.sinistres.index', ['status' => 'review']) }}"
                    class="px-4 py-2 border {{ request('status') === 'review' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-slate-200 text-slate-600 bg-white hover:bg-slate-50' }} rounded-xl text-sm font-semibold transition-all relative">
                    En révision
                </a>
            </div>
        </div>

        {{-- Tableau --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100" style="background: rgba(36,58,143,0.04)">
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Sinistre</th>
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Assuré</th>
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Avis Claims AI</th>
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="text-right px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($sinistres as $sinistre)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-800">#{{ $sinistre->id }} {{ $sinistre->type_sinistre }}</div>
                                <div class="text-xs text-slate-500 mt-1">
                                    @if($sinistre->status === 'traite')
                                        <span class="inline-flex items-center gap-1 text-indigo-600 font-medium">
                                            <i class="fa-solid fa-file-circle-check text-[10px]"></i> Constat terminé
                                        </span>
                                    @elseif($sinistre->workflow_step === 'manager_review')
                                        <span class="inline-flex items-center gap-1 text-yellow-600 font-medium">
                                            <i class="fa-solid fa-clock text-[10px]"></i> En attente de validation
                                        </span>
                                    @elseif(str_starts_with($sinistre->workflow_step, 'closed'))
                                        <span class="inline-flex items-center gap-1 text-emerald-600 font-medium">
                                            <i class="fa-solid fa-check text-[10px]"></i> {{ str_replace('closed_', '', $sinistre->workflow_step) == 'validated' ? 'Validé' : 'Rejeté' }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">{{ str_replace('_', ' ', $sinistre->workflow_step ?? 'Inconnu') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-medium text-slate-700">{{ $sinistre->assure->name ?? 'Inconnu' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                @if(is_array($sinistre->ai_analysis_report) && isset($sinistre->ai_analysis_report['gravity']))
                                    @php $grav = strtolower($sinistre->ai_analysis_report['gravity']); @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold 
                                        {{ $grav === 'high' ? 'bg-red-50 text-red-600 border border-red-100' : ($grav === 'medium' ? 'bg-orange-50 text-orange-600 border border-orange-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100') }}">
                                        Gravité : {{ ucfirst($grav) }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs italic">Pas d'analyse textuelle</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-400 text-xs">
                                {{ $sinistre->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('assurance.sinistres.show', $sinistre) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-primary-50 text-primary-700 hover:bg-primary-100 transition-all">
                                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                        Examiner
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center text-slate-400">
                                <i class="fa-solid fa-folder-open text-3xl mb-3 block opacity-30"></i>
                                Aucun dossier sinistre trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($sinistres->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $sinistres->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
