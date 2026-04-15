@extends('assurance.layouts.template')

@section('title', 'Personnel')

@section('content')
    <div>
        {{-- En-tête --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Notre Personnel</h1>
                <p class="text-sm text-slate-400">Gérez les membres du personnel de votre assurance</p>
            </div>
            <a href="{{ route('assurance.personnel.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold shadow-sm transition-all hover:opacity-90 active:scale-95 bg-indigo-600">
                <i class="fa-solid fa-plus"></i>
                Ajouter un membre
            </a>
        </div>

        {{-- Alertes --}}
        @if (session('success'))
            <div
                class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">
                <i class="fa-solid fa-circle-check shrink-0"></i>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div
                class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- Tableau --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-indigo-50/50">
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Membre
                        </th>
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Code
                        </th>
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Contact</th>
                        <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Statut
                        </th>
                        <th class="text-right px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($personnels as $membre)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-xs font-bold shrink-0 bg-indigo-500">
                                        {{ strtoupper(substr($membre->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $membre->name }} {{ $membre->prenom }}
                                        </p>
                                        <p class="text-xs text-slate-400">{{ $membre->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-indigo-50 text-indigo-700">
                                    <i class="fa-solid fa-id-badge text-[10px]"></i>
                                    {{ $membre->code_user }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-phone text-slate-300 text-xs"></i>
                                    {{ $membre->contact }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if ($membre->must_change_password)
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-600 border border-amber-200">
                                        <i class="fa-solid fa-clock text-[10px]"></i>
                                        En attente d'activation
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                                        Actif
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('assurance.personnel.destroy', $membre) }}" method="POST"
                                        onsubmit="return confirm('Supprimer définitivement ce membre du personnel ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-red-200 text-red-500 hover:bg-red-50 transition-all">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-400">
                                    <i class="fa-solid fa-users text-4xl text-slate-200"></i>
                                    <p class="text-sm font-medium">Aucun membre du personnel ajouté</p>
                                    <a href="{{ route('assurance.personnel.create') }}"
                                        class="text-xs text-indigo-600 hover:underline">Ajouter un premier membre</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($personnels->hasPages())
            <div class="mt-4">
                {{ $personnels->links() }}
            </div>
        @endif
    </div>
@endsection
