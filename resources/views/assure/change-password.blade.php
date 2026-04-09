<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer mon mot de passe — Claims Master</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#243a8f', secondary: '#7cb604' } } }
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
            animation: fadeUp 0.5s ease-out both;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4"
    style="background: linear-gradient(135deg, #243a8f 0%, #1c2e72 50%, #7cb604 100%);">

    {{-- Cercles décoratifs --}}
    <div class="fixed top-[-80px] left-[-80px] w-72 h-72 rounded-full opacity-20 pointer-events-none"
        style="background: radial-gradient(circle, #7cb604, transparent)"></div>
    <div class="fixed bottom-[-60px] right-[-60px] w-64 h-64 rounded-full opacity-15 pointer-events-none"
        style="background: radial-gradient(circle, #ffffff, transparent)"></div>

    <div class="relative z-10 w-full max-w-md animate-fade">

        {{-- Icône --}}
        <div class="text-center mb-6">
            <div class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center shadow-lg"
                style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                <i class="fa-solid fa-lock text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">Changement de mot de passe</h1>
            <p class="text-white/70 text-sm mt-1">Première connexion — cette étape est obligatoire</p>
        </div>

        {{-- Carte --}}
        <div class="rounded-3xl p-8 shadow-2xl"
            style="background: rgba(255,255,255,0.12); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.2);">

            {{-- Info --}}
            <div class="mb-6 rounded-xl px-4 py-3 flex items-start gap-3"
                style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15);">
                <i class="fa-solid fa-circle-info text-white/70 mt-0.5 shrink-0"></i>
                <p class="text-white/80 text-sm">
                    Pour votre sécurité, vous devez définir un nouveau mot de passe personnel avant d'accéder à votre
                    espace.
                </p>
            </div>

            @if($errors->any())
                <div class="mb-5 bg-red-500/20 border border-red-400/40 text-red-100 text-sm rounded-xl px-4 py-3">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('assure.password.update') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Nouveau mot de passe --}}
                <div>
                    <label class="block text-white/80 text-sm font-medium mb-2">
                        <i class="fa-solid fa-lock mr-1 opacity-70"></i>
                        Nouveau mot de passe
                    </label>
                    <input type="password" name="password" id="password" placeholder="Minimum 8 caractères" required
                        class="w-full px-4 py-3 rounded-xl text-slate-800 bg-white/95
                                  border-2 border-transparent focus:border-secondary focus:outline-none transition-all
                                  placeholder:text-slate-400 text-sm">
                </div>

                {{-- Confirmer mot de passe --}}
                <div>
                    <label class="block text-white/80 text-sm font-medium mb-2">
                        <i class="fa-solid fa-lock-open mr-1 opacity-70"></i>
                        Confirmer le mot de passe
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        placeholder="Répétez le mot de passe" required class="w-full px-4 py-3 rounded-xl text-slate-800 bg-white/95
                                  border-2 border-transparent focus:border-secondary focus:outline-none transition-all
                                  placeholder:text-slate-400 text-sm">
                </div>

                {{-- Bouton --}}
                <button type="submit"
                    class="w-full py-3 rounded-xl font-semibold text-white text-sm transition-all hover:opacity-90 hover:scale-[1.01]"
                    style="background: linear-gradient(135deg, #7cb604, #5d8a03);">
                    <i class="fa-solid fa-check mr-2"></i>
                    Valider et accéder à mon espace
                </button>
            </form>

            {{-- Déconnexion --}}
            <form action="{{ route('assure.logout') }}" method="POST" class="mt-5 text-center">
                @csrf
                <button type="submit" class="text-white/50 text-sm hover:text-white/80 transition-all">
                    <i class="fa-solid fa-right-from-bracket mr-1 text-xs"></i>
                    Se déconnecter
                </button>
            </form>
        </div>

    </div>
</body>

</html>