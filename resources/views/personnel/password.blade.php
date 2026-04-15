@extends('personnel.layouts.template')

@section('title', 'Changer le mot de passe')
@section('page-title', 'Mot de passe')

@section('content')
    <div class="max-w-lg mx-auto animate-in">

        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800">Changer le mot de passe</h1>
            <p class="text-sm text-slate-400">Pour votre sécurité, choisissez un mot de passe fort.</p>
        </div>

        <form action="{{ route('personnel.password.update') }}" method="POST">
            @csrf

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                        Mot de passe actuel <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="current_password"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1d3557]/20 focus:border-[#1d3557]/40 transition"
                        required>
                    @error('current_password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                        Nouveau mot de passe <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1d3557]/20 focus:border-[#1d3557]/40 transition"
                        required minlength="8">
                    @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                        Confirmer le mot de passe <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1d3557]/20 focus:border-[#1d3557]/40 transition"
                        required minlength="8">
                </div>

            </div>

            <div class="mt-4 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#1d3557] text-white text-sm font-semibold hover:bg-[#152840] active:scale-95 transition shadow-sm">
                    <i class="fa-solid fa-lock text-xs"></i>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
@endsection
