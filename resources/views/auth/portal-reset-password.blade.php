<!DOCTYPE html>
<html lang="fr" class="overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Réinitialiser le mot de passe (Portail) — Claims Master</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: '#0f172a',
                        secondary: '#3b82f6',
                        accent: '#10b981'
                    }
                }
            }
        }
    </script>
</head>

<body class="m-0 p-0 overflow-x-hidden w-full font-sans text-slate-200"
    style="background: radial-gradient(circle at top right, #1e293b, #0f172a); min-height: 100vh;">

    <div class="min-h-screen w-full flex items-center justify-center py-12 px-4 relative">
        <div class="absolute inset-0 z-0 opacity-20" 
             style="background-image: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');"></div>

        <div class="relative z-10 w-full max-w-md">
            {{-- En-tête --}}
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-600/20 rounded-2xl mx-auto mb-4 flex items-center justify-center border border-blue-500/30">
                    <i class="fa-solid fa-key text-blue-400 text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400">
                    Nouveau mot de passe
                </h1>
                <p class="text-slate-400 text-sm mt-2">Portail Professionnel Sécurisé</p>
            </div>

            {{-- Carte --}}
            <div class="bg-slate-900/50 backdrop-blur-xl p-8 rounded-3xl border border-white/10 shadow-2xl">
                
                @if(session('error'))
                    <div class="mb-5 bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-xl px-4 py-3 flex items-center gap-3">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="mb-5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl px-4 py-3 flex items-center gap-3">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('portal.password.update') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    {{-- Code OTP --}}
                    <div>
                        <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">
                            Code de vérification (4 chiffres)
                        </label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="0000" required
                            class="w-full px-4 py-3 rounded-xl bg-slate-800/50 border border-white/10 focus:border-blue-500 focus:outline-none transition-all text-white placeholder:text-slate-600">
                        @error('code')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nouveau MDP --}}
                    <div>
                        <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">
                            Nouveau mot de passe
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password" placeholder="••••••••" required
                                class="w-full px-4 py-3 rounded-xl bg-slate-800/50 border border-white/10 focus:border-blue-500 focus:outline-none transition-all text-white placeholder:text-slate-600">
                            <button type="button" onclick="togglePwd('password', 'eye-1')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500">
                                <i class="fa-solid fa-eye" id="eye-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Confirmation --}}
                    <div>
                        <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">
                            Confirmer le mot de passe
                        </label>
                        <div class="relative">
                            <input type="password" name="confirme_password" id="confirme_password" placeholder="••••••••" required
                                class="w-full px-4 py-3 rounded-xl bg-slate-800/50 border border-white/10 focus:border-blue-500 focus:outline-none transition-all text-white placeholder:text-slate-600">
                            <button type="button" onclick="togglePwd('confirme_password', 'eye-2')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500">
                                <i class="fa-solid fa-eye" id="eye-2"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-900/20 active:scale-95 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-shield-check"></i>
                        Valider le changement
                    </button>
                </form>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('portal.login') }}" class="text-slate-500 hover:text-slate-300 text-sm transition-colors">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Retour au portail
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePwd(id, iconId) {
            const input = document.getElementById(id);
            const icon = document.getElementById(iconId);
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }
    </script>
</body>
</html>
