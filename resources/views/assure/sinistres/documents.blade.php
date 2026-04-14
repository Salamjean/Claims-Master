@extends('assure.layouts.template')

@section('title', 'Documents Requis')
@section('page-title', 'Mes Documents')

@section('content')
    <div class="max-w-4xl mx-auto pb-12">

        {{-- En-tête --}}
        <div class="mb-8 flex items-center justify-between flex-wrap gap-4 animate-in" style="--delay:0.1s">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-sm"
                        style="background-color: rgba(33, 54, 133, 0.1); color: #213685; border: 1px solid rgba(33, 54, 133, 0.2);">
                        <i class="fa-solid fa-file-signature text-base"></i>
                    </div>
                    Documents Requis
                </h2>
                <p class="text-slate-500 text-sm mt-2 ml-14">
                    Liste des dossiers nécessitant l'envoi de pièces justificatives additionnelles.
                </p>
            </div>
        </div>

        @if($sinistres->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-12 text-center animate-in"
                style="--delay:0.2s">
                <div
                    class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4 border border-slate-100">
                    <i class="fa-solid fa-check text-3xl text-emerald-400"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Tout est en ordre !</h3>
                <p class="text-slate-500 mt-2">Vous n'avez aucun document en attente de soumission.</p>
                <a href="{{ route('assure.sinistres.historique') }}"
                    class="inline-block mt-6 px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                    Voir mon historique
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($sinistres as $index => $sinistre)
                    @php
                        $docsRequis = $sinistre->documentsAttendus()->where('status_client', 'pending')->count();
                        $docsTotal = $sinistre->documentsAttendus->count();
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition-all animate-in"
                        style="--delay:{{ 0.1 * ($index + 1) }}s" onmouseover="this.style.borderColor='#213685'"
                        onmouseout="this.style.borderColor=''">
                        <div class="p-5 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">

                            <div class="flex items-start gap-4">
                                <div
                                    class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-car-burst text-slate-400 text-lg"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">Sinistre
                                            {{ $sinistre->numero_sinistre ?? 'SI-' . $sinistre->id }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border"
                                            style="background-color: rgba(33, 54, 133, 0.1); color: #213685; border-color: rgba(33, 54, 133, 0.2);">Action
                                            requise</span>
                                    </div>
                                    <h3 class="font-bold text-slate-800">{{ str_replace('_', ' ', $sinistre->type_sinistre) }}</h3>
                                    <p class="text-sm text-slate-500 mt-1 line-clamp-1 break-all">
                                        Déclaré le {{ $sinistre->created_at->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-6 w-full sm:w-auto mt-2 sm:mt-0 justify-between sm:justify-start">
                                <div class="text-right">
                                    <span class="block text-2xl font-black text-slate-800 leading-none">{{ $docsRequis }}</span>
                                    <span class="text-xs text-slate-500 font-medium">Docs manquants</span>
                                </div>

                                <a href="{{ route('assure.sinistres.upload-docs', $sinistre->id) }}"
                                    class="shrink-0 group relative inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white transition-all rounded-xl focus:outline-none shadow-sm overflow-hidden"
                                    style="background-color: #213685;" onmouseover="this.style.backgroundColor='#1a2b6b'"
                                    onmouseout="this.style.backgroundColor='#213685'">
                                    <span class="relative flex items-center gap-2">
                                        <i
                                            class="fa-solid fa-arrow-up-from-bracket text-xs transition-transform group-hover:-translate-y-1 group-hover:opacity-0"></i>
                                        <i
                                            class="fa-solid fa-upload absolute left-0 text-xs opacity-0 transition-transform translate-y-2 group-hover:translate-y-0 group-hover:opacity-100"></i>
                                        Soumettre
                                    </span>
                                </a>
                            </div>
                        </div>

                        {{-- Progress bar visuelle --}}
                        @if($docsTotal > 0)
                            @php
                                $progress = (($docsTotal - $docsRequis) / $docsTotal) * 100;
                            @endphp
                            <div class="w-full h-1.5 bg-slate-100">
                                <div class="h-full bg-slate-800 transition-all duration-1000" style="width: {{ $progress }}%"></div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

    </div>
@endsection