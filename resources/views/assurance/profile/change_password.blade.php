@extends('assurance.layouts.template')

@section('title', 'Modifier mon mot de passe')
@section('page-title', 'Modifier mon mot de passe')

@section('content')
    <div class="max-w-xl mx-auto space-y-8 animate-in">
        
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                    <i class="fa-solid fa-key text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Sécurité du compte</h2>
                    <p class="text-slate-500 text-sm">Modifiez votre mot de passe pour sécuriser votre accès.</p>
                </div>
            </div>

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-100 text-red-600 text-sm rounded-2xl px-4 py-3 flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                    <div>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('assurance.password.update') }}" method="POST" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 ml-1">MOT DE PASSE ACTUEL</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center text-slate-300">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="current_password" required
                            class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none text-sm font-semibold text-slate-700"
                            placeholder="Entrez votre mot de passe actuel">
                    </div>
                </div>

                <div class="my-4 border-t border-slate-50"></div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 ml-1">NOUVEAU MOT DE PASSE</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center text-slate-300">
                            <i class="fa-solid fa-shield-halved text-sm"></i>
                        </span>
                        <input type="password" name="new_password" required
                            class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none text-sm font-semibold text-slate-700"
                            placeholder="Minimum 8 caractères">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 ml-1">CONFIRMER LE NOUVEAU MOT DE PASSE</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center text-slate-300">
                            <i class="fa-solid fa-check-double text-sm"></i>
                        </span>
                        <input type="password" name="new_password_confirmation" required
                            class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none text-sm font-semibold text-slate-700"
                            placeholder="Répétez le nouveau mot de passe">
                    </div>
                </div>

                <div class="pt-4 flex flex-col gap-3">
                    <button type="submit"
                        class="w-full py-4 bg-primary hover:bg-primary-700 text-white font-bold rounded-2xl shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-3">
                        <i class="fa-solid fa-save"></i>
                        Mettre à jour le mot de passe
                    </button>
                    <a href="{{ route('assurance.profile') }}"
                        class="w-full py-4 bg-slate-50 hover:bg-slate-100 text-slate-500 font-bold rounded-2xl transition-all flex items-center justify-center gap-2">
                        Annuler
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-amber-50 rounded-3xl p-6 border border-amber-100 flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-amber-900 mb-1">Important</h4>
                <p class="text-xs text-amber-700 leading-relaxed">
                    Une fois modifié, vous serez redirigé vers votre profil. Veillez à bien mémoriser votre nouveau mot de passe car il sera requis pour vos prochaines connexions et modifications de profil.
                </p>
            </div>
        </div>

    </div>
@endsection
