@extends('assurance.layouts.template')

@section('title', 'Liste de nos Garages')

@section('content')
    <div>
        {{-- En-tête --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Nos Garages Agréés</h1>
                <p class="text-sm text-slate-400">Gérez la liste de vos garages et centres de réparation</p>
            </div>
            <a href="{{ route('assurance.garages.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold shadow-sm transition-all hover:opacity-90 active:scale-95 bg-orange-600">
                <i class="fa-solid fa-plus"></i>
                Ajouter un garage
            </a>
        </div>

        {{-- Tableau --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-orange-50/50">
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Garage
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
                    @forelse($garages as $garage)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0 bg-orange-500">
                                        {{ strtoupper(substr($garage->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $garage->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $garage->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-orange-50 text-orange-700">
                                    <i class="fa-solid fa-id-badge text-[10px]"></i>
                                    {{ $garage->code_user }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-phone text-slate-300 text-xs"></i>
                                    {{ $garage->contact }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-400 text-xs">
                                {{ $garage->adresse }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('assurance.garages.edit', $garage) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-200 text-slate-600 hover:bg-slate-50 transition-all">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                        Modifier
                                    </a>
                                    <form action="{{ route('assurance.garages.destroy', $garage) }}" method="POST"
                                        onsubmit="return confirm('Retirer définitivement ce garage ?')">
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
                                <i class="fa-solid fa-wrench text-3xl mb-3 block opacity-30"></i>
                                Vous n'avez ajouté aucun garage.
                                <div class="mt-3">
                                    <a href="{{ route('assurance.garages.create') }}"
                                        class="text-sm font-medium hover:underline text-orange-600">
                                        Ajouter votre premier garage
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($garages->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $garages->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection