<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail Professionnel — Claims Master</title>
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
    style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); min-height: 100vh;">

    <div class="min-h-screen w-full flex items-center justify-center py-12 px-4 relative overflow-hidden">

        {{-- Bouton Retour Accueil --}}
        <a href="{{ url('/') }}"
            class="absolute top-4 left-4 sm:top-6 sm:left-6 flex items-center gap-2 text-white/80 hover:text-white transition-colors z-50 bg-white/10 hover:bg-white/20 px-3 py-2 sm:px-4 sm:py-2 rounded-xl backdrop-blur-md border border-white/20 shadow-lg">
            <i class="fa-solid fa-arrow-left"></i>
            <span class="text-sm font-medium hidden sm:inline">Retour à l'accueil</span>
        </a>

        {{-- Cercles décoratifs --}}
        <div class="absolute top-[-80px] left-[-80px] w-72 h-72 rounded-full opacity-10 pointer-events-none"
            style="background: radial-gradient(circle, #243a8f, transparent)"></div>
        <div class="absolute bottom-[-60px] right-[-60px] w-64 h-64 rounded-full opacity-10 pointer-events-none"
            style="background: radial-gradient(circle, #ffffff, transparent)"></div>

        <div class="relative z-10 w-full max-w-md animate-fade mt-10 sm:mt-0">

            {{-- Logo / En-tête --}}
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-2xl mx-auto mb-3 sm:mb-4 flex items-center justify-center shadow-lg"
                    style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                    <i class="fa-solid fa-briefcase text-blue-400 text-xl sm:text-2xl"></i>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-white">Portail Professionnel</h1>
                <p class="text-white/50 text-xs sm:text-sm mt-1">Plateforme de Gestion des Sinistres</p>
            </div>

            {{-- Carte formulaire --}}
            <div class="rounded-2xl sm:rounded-3xl p-5 sm:p-8 shadow-2xl"
                style="background: rgba(255,255,255,0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1);">

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

                <form action="{{ route('portal.login.submit') }}" method="POST" class="space-y-4 sm:space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label class="block text-white/80 text-xs sm:text-sm font-medium mb-1 sm:mb-2">
                            <i class="fa-solid fa-envelope mr-1 opacity-70"></i>
                            Adresse Email
                        </label>
                        <input type="email" name="login" id="login" value="{{ old('login') }}"
                            placeholder="votre@emailpro.com" required autocomplete="username" class="w-full px-3 py-2.5 sm:px-4 sm:py-3 text-sm sm:text-base rounded-xl text-white bg-slate-800/50
                                  border-2 border-white/10 focus:border-blue-500 focus:outline-none transition-all
                                  placeholder:text-slate-500">
                    </div>

                    {{-- Mot de passe --}}
                    <div>
                        <div class="flex items-center justify-between mb-1 sm:mb-2">
                            <label class="text-white/80 text-xs sm:text-sm font-medium">
                                <i class="fa-solid fa-lock mr-1 opacity-70"></i>
                                Mot de passe
                            </label>
                            <a href="{{ route('portal.password.forgot') }}"
                                class="text-[10px] sm:text-xs text-white/30 hover:text-white transition-colors">
                                Oublié ?
                            </a>
                        </div>
                        <div class="relative">
                            <input type="password" name="password" id="password" placeholder="••••••••" required
                                autocomplete="current-password" class="w-full px-3 py-2.5 sm:px-4 sm:py-3 pr-10 sm:pr-12 text-sm sm:text-base rounded-xl text-white bg-slate-800/50
                                      border-2 border-white/10 focus:border-blue-500 focus:outline-none transition-all">
                            <button type="button" onclick="togglePwd()"
                                class="absolute inset-y-0 right-2 sm:right-3 px-2 text-slate-500 hover:text-slate-300">
                                <i class="fa-solid fa-eye text-sm" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Se souvenir de moi --}}
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="remember" id="remember"
                            class="w-3.5 h-3.5 sm:w-4 sm:h-4 rounded accent-blue-500 cursor-pointer">
                        <label for="remember" class="text-xs sm:text-sm text-white/40 cursor-pointer select-none">
                            Se souvenir de moi
                        </label>
                    </div>

                    {{-- Bouton --}}
                    <button type="submit" class="w-full py-3 sm:py-3.5 mt-2 rounded-xl text-white font-bold text-sm sm:text-base flex items-center justify-center gap-2
                           transition-all hover:bg-blue-600 active:scale-95 shadow-lg bg-blue-500">
                        <i class="fa-solid fa-shield-check mr-1"></i>
                        Accéder au Portail
                    </button>
                </form>
            </div>

            <p class="text-center text-white/20 text-xs mt-6 uppercase tracking-widest">Administration Sécurisée</p>
        </div>

    </div>

    <script>
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
