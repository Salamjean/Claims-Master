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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        @keyframes pulseGlow {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.08); }
        }
        .animate-glow {
            animation: pulseGlow 4s ease-in-out infinite;
        }

        .bg-grid-pattern {
            background-image: radial-gradient(rgba(36, 58, 143, 0.06) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>

<body class="bg-slate-900 text-slate-800 min-h-screen antialiased flex flex-col justify-between overflow-x-hidden selection:bg-indigo-600 selection:text-white relative bg-cover bg-center bg-no-repeat bg-fixed"
    style="background-image: url('{{ asset('assets/images/portal_bg.png') }}');">

    {{-- Dark Backdrop Blur Overlay --}}
    <div class="fixed inset-0 bg-slate-950/40 backdrop-blur-[3px] pointer-events-none -z-10"></div>

    {{-- Ambient Background Orbs --}}
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden -z-10">
        <div class="absolute -top-32 -left-32 w-[500px] h-[500px] rounded-full bg-indigo-500/20 blur-[120px] animate-glow"></div>
        <div class="absolute top-1/2 -right-32 w-[500px] h-[500px] rounded-full bg-blue-500/20 blur-[140px] animate-glow" style="animation-delay: 2s;"></div>
        <div class="absolute inset-0 bg-grid-pattern opacity-30"></div>
    </div>

    {{-- Top Header (Bouton retour retiré) --}}
    <header class="w-full px-6 py-5 flex items-center justify-end relative z-20">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/80 border border-slate-200/80 rounded-full text-indigo-900 text-xs font-bold shadow-sm backdrop-blur-md">
            <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
            <span>Portail Professionnel</span>
        </div>
    </header>

    {{-- Main Container --}}
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 md:p-10 relative z-10">
        <div class="w-full max-w-5xl bg-white border border-slate-100 rounded-3xl sm:rounded-[32px] shadow-[0_20px_50px_rgba(8,112,184,0.08)] overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[600px]">

            {{-- LEFT PANEL: Branding & Visuals (5 cols on lg) --}}
            <div class="lg:col-span-5 bg-gradient-to-br from-slate-900 via-indigo-950 to-blue-950 p-8 sm:p-10 text-white flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 space-y-6">
                    {{-- Logo Badge --}}
                    <div class="inline-flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-500 to-blue-600 p-0.5 shadow-lg shadow-indigo-500/30">
                            <div class="w-full h-full bg-slate-950/60 backdrop-blur-md rounded-[14px] flex items-center justify-center text-white">
                                <i class="fa-solid fa-shield-halved text-xl text-indigo-400"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-white tracking-tight">Claims Master</h2>
                            <p class="text-[10px] text-indigo-300 font-bold uppercase tracking-widest">Espace Pro</p>
                        </div>
                    </div>

                    <div class="space-y-3 pt-4">
                        <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">
                            Plateforme Unifiée de Gestion des Sinistres
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-medium">
                            Accès sécurisé pour les assureurs, courtiers, experts de terrain et forces de l'ordre.
                        </p>
                    </div>

                    {{-- Badges des Métiers --}}
                    <div class="space-y-2.5 pt-2">
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-sm">
                            <div class="w-8 h-8 rounded-xl bg-blue-500/20 text-blue-300 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-building-shield text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white">Compagnies & Courtiers</p>
                                <p class="text-[10px] text-slate-300">Suivi des polices & validation des constats</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-sm">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-user-shield text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white">Agents & Experts Terrain</p>
                                <p class="text-[10px] text-slate-300">Gestion des interventions & rapports</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-sm">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-scale-balanced text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-white">Forces de l'Ordre</p>
                                <p class="text-[10px] text-slate-300">Réception immédiate des alertes urgentes</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Stats --}}
                <div class="relative z-10 pt-6 mt-6 border-t border-white/10 flex items-center justify-between text-[11px] font-bold text-slate-300">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-lock text-emerald-400"></i> SSL 256-bit
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-bolt text-indigo-400"></i> Audit IA
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-blue-400"></i> 99.9% Uptime
                    </span>
                </div>
            </div>

            {{-- RIGHT PANEL: Light Form Card (7 cols on lg) --}}
            <div class="lg:col-span-7 p-6 sm:p-10 md:p-12 flex flex-col justify-center bg-white">

                <div class="max-w-md mx-auto w-full space-y-6">

                    {{-- Form Header --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                            <span class="text-xs font-extrabold text-indigo-600 uppercase tracking-widest">Connexion Sécurisée</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Accéder à mon espace</h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">Saisissez vos identifiants professionnels pour vous connecter.</p>
                    </div>

                    {{-- Alerts --}}
                    @if(session('error'))
                        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-xs sm:text-sm font-semibold flex items-center gap-3">
                            <i class="fa-solid fa-circle-exclamation text-red-500 text-lg shrink-0"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs sm:text-sm font-semibold flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-lg shrink-0"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- Login Form --}}
                    <form action="{{ route('portal.login.submit') }}" method="POST" class="space-y-5">
                        @csrf

                        {{-- Email Input --}}
                        <div class="space-y-2">
                            <label for="login" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Adresse Email Professionnelle <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-envelope text-sm"></i>
                                </div>
                                <input type="email" name="login" id="login" value="{{ old('login') }}"
                                    placeholder="votre@compagnie.com" required autocomplete="username"
                                    class="w-full pl-11 pr-4 py-3.5 bg-slate-50/80 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                            </div>
                        </div>

                        {{-- Password Input --}}
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Mot de passe <span class="text-red-500">*</span>
                                </label>
                                <a href="{{ route('portal.password.forgot') }}"
                                    class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-colors">
                                    Mot de passe oublié ?
                                </a>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-lock text-sm"></i>
                                </div>
                                <input type="password" name="password" id="password" placeholder="••••••••" required
                                    autocomplete="current-password"
                                    class="w-full pl-11 pr-12 py-3.5 bg-slate-50/80 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                                <button type="button" onclick="togglePwd()"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-700 transition-colors">
                                    <i class="fa-solid fa-eye text-sm" id="eye-icon"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Remember Me --}}
                        <div class="flex items-center justify-between pt-1">
                            <label for="remember" class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="remember" id="remember"
                                    class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                <span class="text-xs font-bold text-slate-600 group-hover:text-slate-900 transition-colors">Se souvenir de moi</span>
                            </label>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit"
                            class="w-full py-4 rounded-2xl bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 hover:from-indigo-500 hover:to-blue-600 text-white font-extrabold text-sm shadow-xl shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2.5">
                            <i class="fa-solid fa-shield-check text-base"></i>
                            <span>CONNEXION</span>
                        </button>
                    </form>

                </div>

            </div>

        </div>
    </main>

    {{-- Bottom Footer info --}}
    <footer class="w-full py-4 text-center text-slate-400 text-xs font-semibold relative z-20">
        &copy; {{ date('Y') }} Claims Master. Tous droits réservés. Espace d'administration professionnelle sécurisée.
    </footer>

    <script>
        function togglePwd() {
            const field = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
