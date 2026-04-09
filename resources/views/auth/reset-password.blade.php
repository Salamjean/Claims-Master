<!DOCTYPE html>
<html lang="fr" class="overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Réinitialiser le mot de passe — Claims Master</title>

    {{-- Tailwind CSS & FontAwesome --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: '#1e293b',
                        secondary: '#2563eb',
                        accent: '#7cb604'
                    },
                    animation: {
                        'fade': 'fadeIn 0.6s ease-out forwards',
                        'float': 'floating 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0', transform: 'translateY(20px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                        floating: { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-15px)' } }
                    }
                }
            }
        }
    </script>
</head>

<body class="m-0 p-0 overflow-x-hidden w-full font-sans text-slate-900"
    style="background: linear-gradient(135deg, #243a8f 0%, #1c2e72 50%, #7cb604 100%); min-height: 100vh;">

    <div class="min-h-screen w-full flex items-center justify-center py-12 px-4 relative overflow-hidden">

        {{-- Bouton Retour --}}
        <a href="{{ route('login') }}"
            class="absolute top-4 left-4 sm:top-6 sm:left-6 flex items-center gap-2 text-white/80 hover:text-white transition-colors z-50 bg-white/10 hover:bg-white/20 px-3 py-2 sm:px-4 sm:py-2 rounded-xl backdrop-blur-md border border-white/20 shadow-lg">
            <i class="fa-solid fa-arrow-left"></i>
            <span class="text-xs sm:text-sm font-medium hidden sm:inline">Retour à la connexion</span>
        </a>

        {{-- Cercles décoratifs --}}
        <div class="absolute top-[-80px] left-[-80px] w-72 h-72 rounded-full opacity-20 animate-float pointer-events-none"
            style="background: radial-gradient(circle, #7cb604, transparent)"></div>
        <div class="absolute bottom-[-60px] right-[-60px] w-64 h-64 rounded-full opacity-15 pointer-events-none"
            style="background: radial-gradient(circle, #ffffff, transparent)"></div>

        <div class="relative z-10 w-full max-w-md animate-fade mt-10 sm:mt-0">

            {{-- Logo / En-tête --}}
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-2xl mx-auto mb-3 sm:mb-4 flex items-center justify-center shadow-lg"
                    style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                    <i class="fa-solid fa-key text-white text-xl sm:text-2xl"></i>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-white">Créer un nouveau mot de passe</h1>
                <p class="text-white/70 text-xs sm:text-sm mt-2">Veuillez entrer le code reçu par email ainsi que votre
                    nouveau mot de passe.</p>
            </div>

            {{-- Carte formulaire --}}
            <div class="rounded-none sm:rounded-3xl p-6 sm:p-8 shadow-2xl border-x-0 sm:border-x"
                style="background: rgba(255,255,255,0.12); backdrop-filter: blur(20px); border-top: 1px solid rgba(255,255,255,0.2); border-bottom: 1px solid rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.2);">

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

                <form action="{{ route('password.update') }}" method="POST" class="space-y-4 sm:space-y-5">
                    @csrf

                    <input type="hidden" name="email" value="{{ $email }}">

                    {{-- Code OTP --}}
                    <div>
                        <label class="block text-white/80 text-xs sm:text-sm font-medium mb-1 sm:mb-2">
                            <i class="fa-solid fa-hashtag mr-1 opacity-70"></i>
                            Code de vérification (à 4 chiffres)
                        </label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" placeholder="Ex: 1234"
                            required
                            class="w-full px-3 py-2.5 sm:px-4 sm:py-3 text-sm sm:text-base rounded-xl text-slate-800 bg-white/95 border-2 border-transparent focus:border-secondary focus:outline-none transition-all placeholder:text-slate-400 {{ $errors->has('code') ? 'border-red-400' : '' }}">
                        @error('code')
                            <p class="text-red-300 text-xs mt-1"><i
                                    class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nouveau Mot de passe --}}
                    <div>
                        <label class="block text-white/80 text-xs sm:text-sm font-medium mb-1 sm:mb-2">
                            <i class="fa-solid fa-lock mr-1 opacity-70"></i>
                            Nouveau mot de passe
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

                    {{-- Confirmer Nouveau Mot de passe --}}
                    <div>
                        <label class="block text-white/80 text-xs sm:text-sm font-medium mb-1 sm:mb-2">
                            Confirmer le mot de passe
                        </label>
                        <div class="relative">
                            <input type="password" name="confirme_password" id="confirme_password"
                                placeholder="••••••••" required autocomplete="new-password"
                                class="w-full px-3 py-2.5 sm:px-4 sm:py-3 pr-10 sm:pr-12 text-sm sm:text-base rounded-xl text-slate-800 bg-white/95 border-2 border-transparent focus:border-secondary focus:outline-none transition-all">
                            <button type="button" onclick="togglePwd('confirme_password', 'eye-icon-2')"
                                class="absolute inset-y-0 right-2 sm:right-3 px-2 text-slate-400 hover:text-slate-600">
                                <i class="fa-solid fa-eye text-sm" id="eye-icon-2"></i>
                            </button>
                        </div>
                        @error('confirme_password')
                            <p class="text-red-300 text-xs mt-1"><i
                                    class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Bouton --}}
                    <button type="submit"
                        class="w-full py-3 sm:py-3.5 mt-4 rounded-xl text-white font-bold text-sm sm:text-base flex items-center justify-center gap-2 transition-all hover:opacity-90 active:scale-95 shadow-lg"
                        style="background: linear-gradient(135deg, #243a8f, #1c2e72);">
                        <i class="fa-solid fa-check-circle"></i>
                        Réinitialiser le mot de passe
                    </button>

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