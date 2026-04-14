@extends('assure.layouts.template')

@section('title', 'Mes Assurances')
@section('page-title', 'Mes Assurances')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Mes Assurances Automobile</h1>
                <p class="text-slate-500 text-sm mt-1">Gérez vos contrats et véhicules assurés</p>
            </div>
            <a href="{{ route('assure.contrats.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-900 hover:bg-blue-800 text-white font-bold text-sm rounded-xl transition-all shadow-lg shadow-blue-200">
                <i class="fa-solid fa-plus"></i>
                Ajouter une assurance
            </a>
        </div>

        @if($contrats->isEmpty())
            <div class="card p-12 text-center">
                <div
                    class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-slate-100">
                    <i class="fa-solid fa-car-burst text-slate-300 text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-700">Aucune assurance enregistrée</h3>
                <p class="text-slate-500 max-w-sm mx-auto mt-2 mb-8">Vous n'avez pas encore ajouté vos informations d'assurance
                    automobile. Ajoutez-les pour faciliter vos déclarations de sinistres.</p>
                <a href="{{ route('assure.contrats.create') }}" class="text-blue-600 font-bold hover:underline">
                    Ajouter ma première assurance maintenant
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($contrats as $contrat)
                    <div class="card overflow-hidden flex flex-col">
                        <div class="p-5 border-bottom border-slate-50 bg-slate-50/50">
                            <div class="flex justify-between items-start mb-4">
                                <span class="badge {{ $contrat->statut === 'actif' ? 'badge-green' : 'badge-gray' }}">
                                    {{ ucfirst($contrat->statut) }}
                                </span>
                                <i class="fa-solid fa-car text-blue-600 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-extrabold text-slate-800">{{ $contrat->marque }} {{ $contrat->modele }}</h3>
                            <p class="text-xs font-mono text-slate-500 mt-1 uppercase">{{ $contrat->plaque }}</p>
                        </div>

                        <div class="p-5 space-y-4 flex-1">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">Contrat N°</span>
                                <span class="font-bold text-slate-700">{{ $contrat->numero_contrat }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">Assureur</span>
                                <span
                                    class="font-bold text-blue-800">{{ $contrat->assureur->name ?? $contrat->nom_assureur ?? 'Non spécifié' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-500">Type</span>
                                <span class="font-bold text-slate-700">{{ $contrat->type_vehicule }}</span>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 border-t border-slate-100 grid grid-cols-2 gap-2">
                            @if($contrat->document_pdf)
                                <a href="{{ asset('storage/' . $contrat->document_pdf) }}" target="_blank"
                                    class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-slate-200 text-slate-600 text-[10px] font-bold rounded-lg hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-file-pdf text-red-500"></i>
                                    Contrat
                                </a>
                            @endif
                            @if($contrat->attestation_assurance)
                                <div class="relative">
                                    <a href="{{ asset('storage/' . $contrat->attestation_assurance) }}" target="_blank"
                                        class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-slate-200 text-slate-600 text-[10px] font-bold rounded-lg hover:bg-slate-50 transition-colors">
                                        <i class="fa-solid fa-id-card text-blue-500"></i>
                                        Attestation
                                    </a>
                                    @if($contrat->attestation_ai_status === 'valid')
                                        <span
                                            class="absolute -top-2 -right-2 bg-emerald-500 text-white text-[8px] px-1.5 py-0.5 rounded-full shadow-sm font-bold flex items-center gap-1 border border-white">
                                            <i class="fa-solid fa-check"></i> IA
                                        </span>
                                    @endif
                                </div>
                            @endif
                            @if($contrat->carte_grise)
                                <a href="{{ asset('storage/' . $contrat->carte_grise) }}" target="_blank"
                                    class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-slate-200 text-slate-600 text-[10px] font-bold rounded-lg hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-file-invoice text-blue-500"></i>
                                    C. Grise
                                </a>
                            @endif
                            @if($contrat->visite_technique)
                                <a href="{{ asset('storage/' . $contrat->visite_technique) }}" target="_blank"
                                    class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-slate-200 text-slate-600 text-[10px] font-bold rounded-lg hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-clipboard-check text-orange-500"></i>
                                    V. Tech
                                </a>
                            @endif
                            @if($contrat->permis_conduire)
                                <a href="{{ asset('storage/' . $contrat->permis_conduire) }}" target="_blank"
                                    class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-white border border-slate-200 text-slate-600 text-[10px] font-bold rounded-lg hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-address-card text-purple-500"></i>
                                    Permis
                                </a>
                            @endif

                            <form action="{{ route('assure.contrats.destroy', $contrat) }}" method="POST" class="col-span-2 mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete(this)"
                                    class="w-full h-10 inline-flex items-center justify-center bg-white border border-slate-200 text-red-500 rounded-lg hover:bg-red-50 hover:border-red-100 transition-all shadow-sm gap-2 font-bold text-xs"
                                    title="Supprimer">
                                    <i class="fa-solid fa-trash-can"></i>
                                    Supprimer mon assurance
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: 'Supprimer cette assurance ?',
                text: "Cette action est irréversible et supprimera également les documents associés.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-5 py-2',
                    cancelButton: 'rounded-xl px-5 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            })
        }
    </script>
@endpush