<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Claims Master</title>
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

        <div class="relative z-10 w-full max-w-md animate-fade mt-10 sm:mt-0">

            {{-- Logo / En-tête --}}
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-2xl mx-auto mb-3 sm:mb-4 flex items-center justify-center shadow-lg"
                    style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                    <i class="fa-solid fa-shield-halved text-white text-xl sm:text-2xl"></i>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-white">Connexion</h1>
                <p class="text-white/70 text-xs sm:text-sm mt-1">Claims Master — Gestion Sécurisée</p>
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

                @if(session('success'))
                    <div
                        class="mb-4 sm:mb-5 bg-emerald-500/20 border border-emerald-400/40 text-emerald-100 text-xs sm:text-sm rounded-xl px-4 py-3 flex items-center gap-2">
                        <i class="fa-solid fa-circle-check shrink-0"></i>
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Toggle de mode de connexion --}}
                <div class="mb-6">
                    <div class="flex p-1 bg-white/10 backdrop-blur-md rounded-xl border border-white/20">
                        <button type="button" id="toggle-id" onclick="setLoginMode('id')"
                            class="flex-1 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-300 flex items-center justify-center gap-2 text-white bg-primary/40">
                            <i class="fa-solid fa-id-card"></i>
                            Identifiant / Tel
                        </button>
                        <button type="button" id="toggle-email" onclick="setLoginMode('email')"
                            class="flex-1 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all duration-300 flex items-center justify-center gap-2 text-white/60 hover:text-white">
                            <i class="fa-solid fa-envelope"></i>
                            Email
                        </button>
                    </div>
                </div>

                <form action="{{ route('user.login') }}" method="POST" class="space-y-4 sm:space-y-5">
                    @csrf
                    <input type="hidden" name="login_mode" id="login_mode" value="id">

                    {{-- Login dynamique --}}
                    <div>
                        <label id="login-label" class="block text-white/80 text-xs sm:text-sm font-medium mb-1 sm:mb-2">
                            <i id="login-icon" class="fa-solid fa-id-card mr-1 opacity-70"></i>
                            Identifiant ou Téléphone
                        </label>
                        <input type="text" name="login" id="login" value="{{ old('login') }}"
                            placeholder="CM-XXXXXX  ou  07XXXXXXXX" required autocomplete="username" class="w-full px-3 py-2.5 sm:px-4 sm:py-3 text-sm sm:text-base rounded-xl text-slate-800 bg-white/95
                                  border-2 border-transparent focus:border-secondary focus:outline-none transition-all
                                  placeholder:text-slate-400
                                  {{ $errors->has('login') ? 'border-red-400' : '' }}">
                        @error('login')
                            <p class="text-red-300 text-xs mt-1"><i
                                    class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Mot de passe --}}
                    <div>
                        <div class="flex items-center justify-between mb-1 sm:mb-2">
                            <label class="text-white/80 text-xs sm:text-sm font-medium">
                                <i class="fa-solid fa-lock mr-1 opacity-70"></i>
                                Mot de passe
                            </label>
                            <a href="{{ route('password.forgot') }}"
                                class="text-[10px] sm:text-xs text-white/50 hover:text-white transition-colors">
                                Oublié ?
                            </a>
                        </div>
                        <div class="relative">
                            <input type="password" name="password" id="password" placeholder="••••••••" required
                                autocomplete="current-password" class="w-full px-3 py-2.5 sm:px-4 sm:py-3 pr-10 sm:pr-12 text-sm sm:text-base rounded-xl text-slate-800 bg-white/95
                                      border-2 border-transparent focus:border-secondary focus:outline-none transition-all
                                      {{ $errors->has('password') ? 'border-red-400' : '' }}">
                            <button type="button" onclick="togglePwd()"
                                class="absolute inset-y-0 right-2 sm:right-3 px-2 text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-eye text-sm" id="eye-icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-300 text-xs mt-1"><i
                                    class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Se souvenir de moi --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="remember" id="remember"
                            class="w-3.5 h-3.5 sm:w-4 sm:h-4 rounded accent-secondary cursor-pointer">
                        <label for="remember" class="text-xs sm:text-sm text-white/60 cursor-pointer select-none">
                            Se souvenir de moi
                        </label>
                    </div>

                    {{-- Bouton --}}
                    <button type="submit" class="w-full py-3 sm:py-3.5 mt-2 rounded-xl text-white font-bold text-sm sm:text-base flex items-center justify-center gap-2
                           transition-all hover:opacity-90 active:scale-95 shadow-lg"
                        style="background: linear-gradient(135deg, #243a8f, #1c2e72);">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        Se connecter
                    </button>

                    {{-- Lien Inscription --}}
                    <div class="text-center mt-4 pt-4 border-t border-white/10">
                        <p class="text-sm text-white/70">
                            Vous n'avez pas de compte ?
                            <a href="{{ route('assure.register.form') }}"
                                class="text-secondary font-semibold hover:text-white transition-colors">
                                S'inscrire
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            <p class="text-center text-white/40 text-xs mt-6">CLAIMS MASTER &copy; {{ date('Y') }}</p>
        </div>

    </div>

    <script>
        function setLoginMode(mode) {
            const label = document.getElementById('login-label');
            const icon = document.getElementById('login-icon');
            const input = document.getElementById('login');
            const btnEmail = document.getElementById('toggle-email');
            const btnId = document.getElementById('toggle-id');

            const inputMode = document.getElementById('login_mode');

            if (mode === 'email') {
                inputMode.value = 'email';
                label.innerHTML = '<i id="login-icon" class="fa-solid fa-envelope mr-1 opacity-70"></i> Email';
                input.placeholder = 'votre@email.com';
                input.type = 'email';
                btnEmail.classList.add('bg-primary/40', 'text-white');
                btnEmail.classList.remove('text-white/60');
                btnId.classList.remove('bg-primary/40', 'text-white');
                btnId.classList.add('text-white/60');
            } else {
                inputMode.value = 'id';
                label.innerHTML = '<i id="login-icon" class="fa-solid fa-id-card mr-1 opacity-70"></i> Identifiant ou Téléphone';
                input.placeholder = 'CM-XXXXXX  ou  07XXXXXXXX';
                input.type = 'text';
                btnId.classList.add('bg-primary/40', 'text-white');
                btnId.classList.remove('text-white/60');
                btnEmail.classList.remove('bg-primary/40', 'text-white');
                btnEmail.classList.add('text-white/60');
            }
        }

        function togglePwd() {
            const field = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            field.type = field.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }
    </script>
</body>

</html>