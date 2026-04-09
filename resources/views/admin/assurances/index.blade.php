@extends('admin.layouts.template')

@section('title', 'Liste des assurances')
@section('page-title', 'Assurances')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Compagnies d'assurance</h1>
            <p class="text-sm text-slate-400 mt-0.5">Gérez les compagnies inscrites sur la plateforme.</p>
        </div>
        <a href="{{ route('admin.assurances.create') }}"
            class="flex items-center gap-2 text-sm text-white font-medium px-4 py-2.5 rounded-xl transition-all hover:opacity-90"
            style="background: linear-gradient(135deg, #243a8f, #1c2e72);">
            <i class="fa-solid fa-plus text-xs"></i> Nouvelle assurance
        </a>
    </div>


    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left">
                        <th class="px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide text-center">Compagnie</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide text-center">Représentant</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide text-center">Contact</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide text-center">N° RCCM</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide text-center">N° DFE</th>
                        <th class="px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($assurances as $assurance)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4" style="display: flex; justify-content: center; align-items: center;">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-sm font-bold shrink-0"
                                        style="background: linear-gradient(135deg, #243a8f, #7cb604);">
                                        {{ strtoupper(substr($assurance->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-700">{{ $assurance->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $assurance->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-center">{{ $assurance->prenom }}</td>
                            <td class="px-6 py-4 text-slate-500 text-center">{{ $assurance->contact }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-medium bg-primary/10 text-primary px-2 py-1 rounded-lg">
                                    {{ $assurance->assuranceProfile->numero_rccm ?? '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-medium bg-secondary/10 px-2 py-1 rounded-lg" style="color:#6a9c03">
                                    {{ $assurance->assuranceProfile->numero_dfe ?? '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center" style="display: flex; justify-content: center; align-items: center;">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.assurances.show', $assurance) }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-primary bg-primary/5 hover:bg-primary/10 transition-all"
                                        title="Voir les détails">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.assurances.destroy', $assurance) }}" method="POST"
                                        onsubmit="return confirm('Supprimer cette assurance ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg text-red-400 bg-red-50 hover:bg-red-100 transition-all"
                                            title="Supprimer">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-300">
                                <i class="fa-solid fa-shield-halved text-4xl mb-3 block"></i>
                                <p class="text-sm">Aucune assurance inscrite pour le moment</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assurances->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $assurances->links() }}
            </div>
        @endif
    </div>

@endsection