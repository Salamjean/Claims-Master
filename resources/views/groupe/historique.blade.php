@extends('groupe.layouts.app')

@section('title', 'Historique Groupe')
@section('page-title', 'Historique')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Historique des interventions</h1>
        <p class="text-slate-500 mt-1">Consultez les anciennes déclarations et interventions.</p>
    </div>

    <div class="card p-6 border border-slate-100">
        @if($sinistres->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-sm border-b border-slate-100">
                            <th class="py-3 px-4 font-medium rounded-tl-lg">Date</th>
                            <th class="py-3 px-4 font-medium">Référence</th>
                            <th class="py-3 px-4 font-medium">Lieu</th>
                            <th class="py-3 px-4 font-medium rounded-tr-lg">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700 divide-y divide-slate-50">
                        @foreach($sinistres as $sinistre)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4">{{ $sinistre->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 font-medium text-slate-900">{{ $sinistre->reference }}</td>
                            <td class="py-3 px-4">{{ $sinistre->lieu ?? 'Non précisé' }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                    Terminé
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $sinistres->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                    <i class="fa-solid fa-clock-rotate-left text-2xl"></i>
                </div>
                <h3 class="text-slate-700 font-medium mb-1">Aucun historique</h3>
                <p class="text-slate-400 text-sm">Vous n'avez aucune intervention terminée.</p>
            </div>
        @endif
    </div>
</div>
@endsection
