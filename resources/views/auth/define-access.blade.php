<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activer mon compte — Claims Master</title>
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

<body class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden"
    style="background: linear-gradient(135deg, #243a8f 0%, #1c2e72 50%, #7cb604 100%);">

    {{-- Cercles décoratifs --}}
    <div class="absolute top-[-80px] left-[-80px] w-72 h-72 rounded-full opacity-20 pointer-events-none"
        style="background: radial-gradient(circle, #7cb604, transparent)"></div>
    <div class="absolute bottom-[-60px] right-[-60px] w-64 h-64 rounded-full opacity-15 pointer-events-none"
        style="background: radial-gradient(circle, #ffffff, transparent)"></div>

    <div class="relative z-10 w-full max-w-md animate-fade">

        {{-- Logo / En-tête --}}
        <div class="text-center mb-6">
            <div class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center shadow-lg"
                style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                <i class="fa-solid fa-shield-check text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">Activer mon compte</h1>
            <p class="text-white/70 text-sm mt-1">Saisissez le code reçu par email et définissez votre mot de passe.</p>
        </div>

        {{-- Carte formulaire --}}
        <div class="rounded-3xl p-8 shadow-2xl"
            style="background: rgba(255,255,255,0.12); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2);">

            @if(session('error'))
                <div
                    class="mb-5 bg-red-500/20 border border-red-400/40 text-red-100 text-sm rounded-xl px-4 py-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 bg-red-500/20 border border-red-400/40 text-red-100 text-sm rounded-xl px-4 py-3">
                    <p class="font-semibold mb-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Erreurs :</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('account.submit') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                {{-- Code de validation --}}
                <div>
                    <label class="block text-white/80 text-sm font-medium mb-2">
                        <i class="fa-solid fa-key mr-1 opacity-70"></i>
                        Code de vérification
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="Entrez le code reçu par email"
                        maxlength="20" autocomplete="off" class="w-full px-4 py-3 rounded-xl text-slate-800 font-semibold tracking-widest text-center text-lg
                                  bg-white/95 border-2 border-transparent focus:border-secondary focus:outline-none transition-all
                                  placeholder:text-slate-400 placeholder:font-normal placeholder:tracking-normal placeholder:text-sm
                                  {{ $errors->has('code') ? 'border-red-400' : '' }}">
                    @error('code')
                        <p class="text-red-300 text-xs mt-1"><i
                                class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nouveau mot de passe --}}
                <div>
                    <label class="block text-white/80 text-sm font-medium mb-2">
                        <i class="fa-solid fa-lock mr-1 opacity-70"></i>
                        Nouveau mot de passe
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Minimum 8 caractères" class="w-full px-4 py-3 pr-12 rounded-xl text-slate-800 bg-white/95
                                      border-2 border-transparent focus:border-secondary focus:outline-none transition-all
                                      {{ $errors->has('password') ? 'border-red-400' : '' }}">
                        <button type="button" onclick="togglePwd('password', 'icon1')"
                            class="absolute inset-y-0 right-3 px-2 text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-eye text-sm" id="icon1"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-300 text-xs mt-1"><i
                                class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmer mot de passe --}}
                <div>
                    <label class="block text-white/80 text-sm font-medium mb-2">
                        <i class="fa-solid fa-lock-keyhole mr-1 opacity-70"></i>
                        Confirmer le mot de passe
                    </label>
                    <div class="relative">
                        <input type="password" id="confirme_password" name="confirme_password"
                            placeholder="Répétez le mot de passe" class="w-full px-4 py-3 pr-12 rounded-xl text-slate-800 bg-white/95
                                      border-2 border-transparent focus:border-secondary focus:outline-none transition-all
                                      {{ $errors->has('confirme_password') ? 'border-red-400' : '' }}">
                        <button type="button" onclick="togglePwd('confirme_password', 'icon2')"
                            class="absolute inset-y-0 right-3 px-2 text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-eye text-sm" id="icon2"></i>
                        </button>
                    </div>
                    @error('confirme_password')
                        <p class="text-red-300 text-xs mt-1"><i
                                class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Indicateur de force --}}
                <div id="strength-bar" class="hidden">
                    <div class="flex gap-1">
                        <div class="h-1 flex-1 rounded-full bg-white/20" id="s1"></div>
                        <div class="h-1 flex-1 rounded-full bg-white/20" id="s2"></div>
                        <div class="h-1 flex-1 rounded-full bg-white/20" id="s3"></div>
                        <div class="h-1 flex-1 rounded-full bg-white/20" id="s4"></div>
                    </div>
                    <p class="text-white/60 text-xs mt-1" id="strength-label"></p>
                </div>

                {{-- Bouton --}}
                <button type="submit" class="w-full py-3.5 rounded-xl text-white font-bold text-sm flex items-center justify-center gap-2
                           transition-all hover:opacity-90 active:scale-95 shadow-lg mt-2"
                    style="background: linear-gradient(135deg, #7cb604, #5a8a03);">
                    <i class="fa-solid fa-check-circle"></i>
                    Activer mon compte
                </button>

                <p class="text-center text-white/50 text-xs">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    Après activation, vous serez redirigé vers la page de connexion.
                </p>
            </form>
        </div>

        <p class="text-center text-white/40 text-xs mt-6">CLAIMS MASTER &copy; {{ date('Y') }}</p>
    </div>

    <script>
        function togglePwd(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            field.type = field.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }

        document.getElementById('password').addEventListener('input', function () {
            const val = this.value;
            const bar = document.getElementById('strength-bar');
            const label = document.getElementById('strength-label');
            const segments = ['s1', 's2', 's3', 's4'];

            if (val.length === 0) { bar.classList.add('hidden'); return; }
            bar.classList.remove('hidden');

            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const colors = ['#ef4444', '#f97316', '#eab308', '#7cb604'];
            const labels = ['Très faible', 'Faible', 'Moyen', 'Fort'];

            segments.forEach((id, i) => {
                document.getElementById(id).style.background = i < score ? colors[score - 1] : 'rgba(255,255,255,0.2)';
            });
            label.textContent = labels[score - 1] || '';
        });
    </script>
</body>

</html>