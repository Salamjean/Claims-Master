@extends(auth('user')->user()->role . '.layouts.template')

@section('title', 'Gestion des Agents')

@section('content')
    <div class="space-y-6 mx-auto" style="max-width: 1800px;">

        {{-- En-tête --}}
        <div class="relative rounded-2xl overflow-hidden text-white px-8 py-7"
            style="background: linear-gradient(135deg, #1d3557 0%, #152840 100%);">
            <div
                style="position:absolute;top:-50px;right:-50px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,0.07),transparent);pointer-events:none;">
            </div>
            <div class="relative flex flex-wrap items-center justify-between gap-5">
                <div>
                    <h1 class="text-2xl font-extrabold">Gestion des <span class="text-blue-300">Agents</span> 👮</h1>
                    <p class="text-sm text-white/50 mt-1">Gérez les accès des agents de votre service (Station : {{ auth('user')->user()->name }})</p>
                </div>
                <div>
                    <a href="{{ route(auth('user')->user()->role . '.agents.create') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25">
                        <i class="fa-solid fa-plus text-xs"></i>
                        Ajouter un Agent
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <p class="text-sm font-bold">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Table des agents --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="fa-solid fa-users text-blue-600 text-sm"></i>
                    </div>
                    <h2 class="text-sm font-bold text-slate-800">Liste des agents actifs</h2>
                </div>
                <div class="text-xs text-slate-400 font-medium">
                    Total : {{ count($agents) }} agent(s)
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Agent</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Date d'inscription</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($agents as $agent)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 font-bold">
                                            {{ strtoupper(substr($agent->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800">{{ $agent->name }} {{ $agent->prenom }}</p>
                                            <p class="text-xs text-slate-400">{{ $agent->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $agent->contact }}
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-slate-500">
                                    {{ $agent->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="{{ route(auth('user')->user()->role . '.agents.destroy', $agent->id) }}" method="POST" onsubmit="return confirm('Supprimer cet agent ?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-9 h-9 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center border border-red-100">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fa-solid fa-user-slash text-4xl mb-4 block opacity-20"></i>
                                    <p class="text-sm font-medium">Aucun agent n'a encore été ajouté.</p>
                                    <a href="{{ route(auth('user')->user()->role . '.agents.create') }}" class="text-blue-500 hover:underline text-xs mt-2 block">
                                        Ajouter votre premier agent
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
