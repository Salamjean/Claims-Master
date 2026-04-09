<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Assuré — Claims Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#243a8f',
                        secondary: '#7cb604',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            font-family: 'Outfit', sans-serif;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade {
            animation: fadeUp 0.6s ease-out both;
        }
    </style>
</head>

<body class="m-0 p-0 overflow-x-hidden w-full font-sans text-slate-900"
    style="background: linear-gradient(135deg, #243a8f 0%, #1c2e72 50%, #7cb604 100%); min-height: 100vh;">

    <div class="min-h-screen w-full flex items-center justify-center py-12 px-4 relative overflow-hidden">

        {{-- Bouton Retour Accueil --}}
        <a href="{{ url('/') }}"
            class="absolute top-4 left-4 sm:top-6 sm:left-6 flex items-center gap-2 text-white/80 hover:text-white transition-colors z-50 bg-white/10 hover:bg-white/20 px-3 py-2 sm:px-4 sm:py-2 rounded-xl backdrop-blur-md border border-white/20 shadow-lg">
            <i class="fa-solid fa-arrow-left"></i>
            <span class="text-sm font-medium hidden sm:inline">Retour à l'accueil</span>
        </a>

        {{-- Cercles décoratifs --}}
        <div class="absolute top-[-80px] left-[-80px] w-72 h-72 rounded-full opacity-20 pointer-events-none"
            style="background: radial-gradient(circle, #7cb604, transparent)"></div>
        <div class="absolute bottom-[-60px] right-[-60px] w-64 h-64 rounded-full opacity-15 pointer-events-none"
            style="background: radial-gradient(circle, #ffffff, transparent)"></div>

        <div class="relative z-10 w-full max-w-2xl animate-fade my-6 sm:my-8">

            {{-- Logo / En-tête --}}
            <div class="text-center mb-5 sm:mb-6 mt-12 sm:mt-0">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl mx-auto mb-3 sm:mb-4 flex items-center justify-center shadow-lg"
                    style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                    <i class="fa-solid fa-user-plus text-white text-xl sm:text-2xl"></i>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-white">Créer mon espace Assuré</h1>
                <p class="text-white/70 text-xs sm:text-sm mt-1">Claims Master — Inscription rapide</p>
            </div>

            {{-- Carte formulaire --}}
            <div class="rounded-2xl sm:rounded-3xl p-5 sm:p-8 shadow-2xl"
                style="background: rgba(255,255,255,0.12); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2);">

                @if(session('error'))
                    <div
                        class="mb-4 sm:mb-5 bg-red-500/20 border border-red-400/40 text-red-100 text-xs sm:text-sm rounded-xl px-4 py-3 flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('assure.register.submit') }}" method="POST" class="space-y-4 sm:space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                        {{-- Nom --}}
                        <div>
                            <label class="block text-white/80 text-xs sm:text-sm font-medium mb-1 sm:mb-2">
                                <i class="fa-solid fa-user mr-1 opacity-70"></i>
                                Nom *
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Votre nom"
                                required
                                class="w-full px-3 py-2.5 sm:px-4 sm:py-3 text-sm sm:text-base rounded-xl text-slate-800 bg-white/95 border-2 border-transparent focus:border-secondary focus:outline-none transition-all placeholder:text-slate-400 {{ $errors->has('name') ? 'border-red-400' : '' }}">
                            @error('name')
                                <p class="text-red-300 text-xs mt-1"><i
                                        class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Prénom --}}
                        <div>
                            <label class="block text-white/80 text-xs sm:text-sm font-medium mb-1 sm:mb-2">
                                Prénom
                            </label>
                            <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}"
                                placeholder="Votre prénom"
                                class="w-full px-3 py-2.5 sm:px-4 sm:py-3 text-sm sm:text-base rounded-xl text-slate-800 bg-white/95 border-2 border-transparent focus:border-secondary focus:outline-none transition-all placeholder:text-slate-400 {{ $errors->has('prenom') ? 'border-red-400' : '' }}">
                            @error('prenom')
                                <p class="text-red-300 text-xs mt-1"><i
                                        class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                        {{-- Téléphone / Contact --}}
                        <div>
                            <label class="block text-white/80 text-xs sm:text-sm font-medium mb-1 sm:mb-2">
                                <i class="fa-solid fa-phone mr-1 opacity-70"></i>
                                Téléphone *
                            </label>
                            <input type="text" name="contact" id="contact" value="{{ old('contact') }}"
                                placeholder="Ex: 0102030405" required
                                class="w-full px-3 py-2.5 sm:px-4 sm:py-3 text-sm sm:text-base rounded-xl text-slate-800 bg-white/95 border-2 border-transparent focus:border-secondary focus:outline-none transition-all placeholder:text-slate-400 {{ $errors->has('contact') ? 'border-red-400' : '' }}">
                            @error('contact')
                                <p class="text-red-300 text-xs mt-1"><i
                                        class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-white/80 text-xs sm:text-sm font-medium mb-1 sm:mb-2">
                                <i class="fa-solid fa-envelope mr-1 opacity-70"></i>
                                Email
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                placeholder="votre@email.com"
                                class="w-full px-3 py-2.5 sm:px-4 sm:py-3 text-sm sm:text-base rounded-xl text-slate-800 bg-white/95 border-2 border-transparent focus:border-secondary focus:outline-none transition-all placeholder:text-slate-400 {{ $errors->has('email') ? 'border-red-400' : '' }}">
                            @error('email')
                                <p class="text-red-300 text-xs mt-1"><i
                                        class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Adresse --}}
                    <div>
                        <label class="block text-white/80 text-xs sm:text-sm font-medium mb-1 sm:mb-2">
                            <i class="fa-solid fa-location-dot mr-1 opacity-70"></i>
                            Adresse
                        </label>
                        <input type="text" name="adresse" id="adresse" value="{{ old('adresse') }}"
                            placeholder="Ex: Abidjan, Cocody"
                            class="w-full px-3 py-2.5 sm:px-4 sm:py-3 text-sm sm:text-base rounded-xl text-slate-800 bg-white/95 border-2 border-transparent focus:border-secondary focus:outline-none transition-all placeholder:text-slate-400 {{ $errors->has('adresse') ? 'border-red-400' : '' }}">
                        @error('adresse')
                            <p class="text-red-300 text-xs mt-1"><i
                                    class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                        {{-- Mot de passe --}}
                        <div>
                            <label class="block text-white/80 text-xs sm:text-sm font-medium mb-1 sm:mb-2">
                                <i class="fa-solid fa-lock mr-1 opacity-70"></i>
                                Mot de passe *
                            </label>
                            <div class="relative">
                                <input type="password" name="password" id="password" placeholder="••••••••" required
                                    autocomplete="new-password"
                                    class="w-full px-3 py-2.5 sm:px-4 sm:py-3 pr-10 sm:pr-12 text-sm sm:text-base rounded-xl text-slate-800 bg-white/95 border-2 border-transparent focus:border-secondary focus:outline-none transition-all {{ $errors->has('password') ? 'border-red-400' : '' }}">
                                <button type="button" onclick="togglePwd('password', 'eye-icon-1')"
                                    class="absolute inset-y-0 right-2 sm:right-3 px-2 text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid fa-eye text-sm" id="eye-icon-1"></i>
                                </button>
                            </div>
                            <p class="text-white/50 text-xs mt-1">Au moins 8 caractères.</p>
                            @error('password')
                                <p class="text-red-300 text-xs mt-1"><i
                                        class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirmer Mot de passe --}}
                        <div>
                            <label class="block text-white/80 text-xs sm:text-sm font-medium mb-1 sm:mb-2">
                                Confirmer mot de passe *
                            </label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    placeholder="••••••••" required autocomplete="new-password"
                                    class="w-full px-3 py-2.5 sm:px-4 sm:py-3 pr-10 sm:pr-12 text-sm sm:text-base rounded-xl text-slate-800 bg-white/95 border-2 border-transparent focus:border-secondary focus:outline-none transition-all">
                                <button type="button" onclick="togglePwd('password_confirmation', 'eye-icon-2')"
                                    class="absolute inset-y-0 right-2 sm:right-3 px-2 text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid fa-eye text-sm" id="eye-icon-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Bouton --}}
                    <div class="pt-1 sm:pt-2">
                        <button type="submit"
                            class="w-full py-3 sm:py-3.5 mt-2 rounded-xl text-white font-bold text-sm sm:text-base flex items-center justify-center gap-2 transition-all hover:opacity-90 active:scale-95 shadow-lg"
                            style="background: linear-gradient(135deg, #243a8f, #1c2e72);">
                            <i class="fa-solid fa-user-check"></i>
                            S'inscrire
                        </button>
                    </div>

                    {{-- Lien Connexion --}}
                    <div class="text-center mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-white/10">
                        <p class="text-xs sm:text-sm text-white/70">
                            Vous avez déjà un compte ?
                            <a href="{{ route('login') }}"
                                class="text-secondary font-semibold hover:text-white transition-colors">
                                Se connecter
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            <p class="text-center text-white/40 text-xs mt-6">CLAIMS MASTER &copy; {{ date('Y') }}</p>
        </div>

        </div>

    <script>
            function togglePwd(fieldId, iconId) {
                const field = document.getElementById(fieldId);
                const icon = document.getElementById(iconId);
                field.type = field.type === 'password' ? 'text' : 'password';
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            }
        </script>
</body>

</html>