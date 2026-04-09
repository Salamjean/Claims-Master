@extends('assurance.layouts.template')

@section('title', 'Liste des assurés')

@section('content')
    <div>
        {{-- En-tête --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Assurés</h1>
                <p class="text-sm text-slate-400">Liste des assurés enregistrés</p>
            </div>
            <a href="{{ route('assurance.assures.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold shadow-sm transition-all hover:opacity-90 active:scale-95"
                style="background: linear-gradient(135deg, #243a8f, #1c2e72);">
                <i class="fa-solid fa-plus"></i>
                Inscrire un assuré
            </a>
        </div>

        {{-- Tableau --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100" style="background: rgba(36,58,143,0.04)">
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Assuré
                        </th>
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Code
                            assuré</th>
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Contact</th>
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Inscrit le</th>
                        <th class="text-right px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($assures as $assure)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0"
                                        style="background: linear-gradient(135deg, #243a8f, #7cb604)">
                                        {{ strtoupper(substr($assure->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $assure->name }} {{ $assure->prenom }}</p>
                                        <p class="text-xs text-slate-400">{{ $assure->email ?? 'Pas d\'email' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-semibold"
                                    style="background:rgba(36,58,143,0.08); color:#243a8f">
                                    <i class="fa-solid fa-id-badge text-[10px]"></i>
                                    {{ $assure->code_user ?? '—' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-phone text-slate-300 text-xs"></i>
                                    {{ $assure->contact }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-400 text-xs">
                                {{ $assure->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('assurance.assures.show', $assure) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-200 text-slate-600 hover:bg-slate-50 transition-all">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                        Détails
                                    </a>
                                    <form action="{{ route('assurance.assures.destroy', $assure) }}" method="POST"
                                        onsubmit="return confirm('Supprimer cet assuré ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-red-200 text-red-500 hover:bg-red-50 transition-all">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center text-slate-400">
                                <i class="fa-solid fa-users text-3xl mb-3 block opacity-30"></i>
                                Aucun assuré enregistré pour le moment.
                                <div class="mt-3">
                                    <a href="{{ route('assurance.assures.create') }}"
                                        class="text-sm font-medium hover:underline" style="color:#243a8f">
                                        Inscrire le premier assuré
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($assures->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $assures->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection