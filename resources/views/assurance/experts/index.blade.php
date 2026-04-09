@extends('assurance.layouts.template')

@section('title', 'Liste de nos Experts')

@section('content')
    <div>
        {{-- En-tête --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Nos Experts</h1>
                <p class="text-sm text-slate-400">Gérez la liste de vos experts automobiles agréés</p>
            </div>
            <a href="{{ route('assurance.experts.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold shadow-sm transition-all hover:opacity-90 active:scale-95 bg-emerald-600">
                <i class="fa-solid fa-plus"></i>
                Ajouter un expert
            </a>
        </div>

        {{-- Tableau --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-emerald-50/50">
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Expert
                        </th>
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Code
                        </th>
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Contact</th>
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Adresse</th>
                        <th class="text-right px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($experts as $expert)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0 bg-emerald-500">
                                        {{ strtoupper(substr($expert->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $expert->name }} {{ $expert->prenom }}</p>
                                        <p class="text-xs text-slate-400">{{ $expert->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-emerald-50 text-emerald-700">
                                    <i class="fa-solid fa-id-badge text-[10px]"></i>
                                    {{ $expert->code_user }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-phone text-slate-300 text-xs"></i>
                                    {{ $expert->contact }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-400 text-xs">
                                {{ $expert->adresse }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('assurance.experts.edit', $expert) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-200 text-slate-600 hover:bg-slate-50 transition-all">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                        Modifier
                                    </a>
                                    <form action="{{ route('assurance.experts.destroy', $expert) }}" method="POST"
                                        onsubmit="return confirm('Retirer définitivement cet expert ?')">
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
                                <i class="fa-solid fa-user-tie text-3xl mb-3 block opacity-30"></i>
                                Vous n'avez ajouté aucun expert.
                                <div class="mt-3">
                                    <a href="{{ route('assurance.experts.create') }}"
                                        class="text-sm font-medium hover:underline text-emerald-600">
                                        Ajouter votre premier expert
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($experts->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $experts->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection