@extends('agent.layouts.template')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')

@section('content')
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Header de profil --}}
        <div class="relative rounded-3xl overflow-hidden bg-white border border-slate-100 shadow-sm p-8 flex flex-col md:flex-row items-center gap-8">
            <div style="position:absolute; top:0; right:0; width:150px; height:150px; background:radial-gradient(circle at top right, rgba(59,130,246,0.05), transparent); pointer-events:none;"></div>

            {{-- Avatar géant --}}
            <div class="relative">
                <div id="hero_avatar"
                    class="w-32 h-32 rounded-3xl shadow-lg ring-4 ring-white flex items-center justify-center text-4xl font-black text-white overflow-hidden"
                    style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                    @if($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-white rounded-2xl shadow-md border border-slate-100 flex items-center justify-center text-blue-500">
                    <i class="fa-solid fa-badge-check text-sm"></i>
                </div>
            </div>

            <div class="text-center md:text-left space-y-2">
                <h1 class="text-3xl font-extrabold text-slate-900">{{ $user->name }} {{ $user->prenom }}</h1>
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-4">
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 border border-blue-100 rounded-xl text-xs font-bold text-blue-600">
                        <i class="fa-solid fa-user-shield"></i>
                        Agent {{ ucfirst($user->service->role ?? 'Service') }}
                    </div>
                </div>
                <p class="text-slate-400 text-sm font-medium">Inscrit depuis le {{ $user->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        <form id="profileForm" action="{{ route('agent.profile.update') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @csrf
            <input type="hidden" name="current_password" id="hidden_password">

            {{-- Colonne Infos Secours --}}
            <div class="space-y-6">
                {{-- Avatar & Photo --}}
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 flex flex-col items-center">
                    <div class="relative group cursor-pointer" onclick="document.getElementById('profile_picture_input').click()">
                        <div id="avatar_preview"
                            class="w-32 h-32 rounded-3xl shadow-lg ring-4 ring-white flex items-center justify-center text-4xl font-black text-white transition-transform group-hover:scale-105 overflow-hidden"
                            style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                            @if($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-white rounded-2xl shadow-md border border-slate-100 flex items-center justify-center text-blue-600 transition-colors group-hover:bg-blue-50">
                            <i class="fa-solid fa-camera text-sm"></i>
                        </div>
                        <input type="file" id="profile_picture_input" name="profile_picture" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </div>
                    <p class="mt-4 text-xs font-bold text-slate-400">Cliquez pour changer la photo</p>
                    @error('profile_picture')
                        <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-blue-500"></i>
                        Vérification
                    </h2>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Vos informations sont utilisées pour générer les constats officiels. Veillez à leur exactitude.
                    </p>
                </div>
            </div>

            {{-- Formulaire Principal --}}
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
                    <h2 class="text-lg font-bold text-slate-900 mb-8 flex items-center gap-3">
                        <span class="w-2 h-8 bg-blue-600 rounded-full"></span>
                        Informations Personnelles
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 ml-1">NOM</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none text-sm font-semibold text-slate-700">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 ml-1">PRÉNOM</label>
                            <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none text-sm font-semibold text-slate-700">
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-xs font-bold text-slate-500 ml-1">ADRESSE E-MAIL</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none text-sm font-semibold text-slate-700">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 ml-1">NUMÉRO DE TÉLÉPHONE</label>
                            <input type="text" name="contact" value="{{ old('contact', $user->contact) }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none text-sm font-semibold text-slate-700">
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-xs font-bold text-slate-500 ml-1">LIEU D'AFFECTATION / ADRESSE</label>
                            <textarea name="adresse" rows="2"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all outline-none text-sm font-semibold text-slate-700 resize-none">{{ old('adresse', $user->adresse) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-10 pt-8 border-t border-slate-100 flex justify-end">
                        <button type="button" onclick="confirmProfileUpdate()"
                            class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-200 transition-all flex items-center gap-3">
                            <i class="fa-solid fa-save"></i>
                            Enregistrer les modifications
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    const img = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    document.getElementById('avatar_preview').innerHTML = img;
                    document.getElementById('hero_avatar').innerHTML = img;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        async function confirmProfileUpdate() {
            const { value: password } = await Swal.fire({
                title: 'Confirmation requise',
                text: 'Veuillez saisir votre mot de passe actuel pour valider les modifications.',
                input: 'password',
                inputPlaceholder: 'Votre mot de passe actuel',
                inputAttributes: { autocapitalize: 'off', autocorrect: 'off' },
                showCancelButton: true,
                confirmButtonText: 'Confirmer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#2563eb',
                inputValidator: (value) => {
                    if (!value) { return 'Le mot de passe est obligatoire !' }
                }
            });

            if (password) {
                document.getElementById('hidden_password').value = password;
                document.getElementById('profileForm').submit();
            }
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: '{{ session('success') }}',
                confirmButtonColor: '#2563eb',
            });
        @endif

        @if($errors->has('current_password'))
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: '{{ $errors->first('current_password') }}',
                confirmButtonColor: '#ef4444',
            });
        @endif
    </script>
@endpush
