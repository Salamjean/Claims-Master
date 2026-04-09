@extends('assurance.layouts.template')

@section('title', 'Documents requis par type de sinistre')

@section('content')
    <div class="mx-auto" style="max-width: 80%;">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800">Documents Requis</h1>
            <p class="text-slate-500 mt-1">Sélectionnez un type de sinistre pour configurer les documents que vos assurés
                devront fournir lors de leur déclaration.</p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 shadow-sm border border-red-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($types as $key => $type)
                <a href="{{ route('assurance.documents-requis.show', $key) }}"
                    class="group block bg-white border border-slate-200 rounded-2xl p-6 transition-all hover:shadow-xl hover:-translate-y-1 hover:border-slate-300 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-24 h-24 bg-{{ $type['color'] }}-50 rounded-bl-full -z-0 transition-transform group-hover:scale-110">
                    </div>

                    <div class="relative z-10 flex flex-col h-full">
                        <div
                            class="w-14 h-14 rounded-xl bg-{{ $type['color'] }}-100 text-{{ $type['color'] }}-600 flex items-center justify-center text-2xl mb-5 shadow-sm">
                            <i class="fa-solid {{ $type['icon'] }}"></i>
                        </div>
                        <h2 class="text-lg font-bold text-slate-800 mb-2">{{ $type['label'] }}</h2>
                        <p class="text-slate-500 text-sm mt-auto">Gérer les documents pour ce type de cas.</p>

                        <div
                            class="mt-5 flex items-center text-{{ $type['color'] }}-600 font-medium text-sm gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                            Configurer <i
                                class="fa-solid fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection