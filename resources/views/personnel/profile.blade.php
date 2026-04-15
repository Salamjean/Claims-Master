@extends('personnel.layouts.template')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')

@section('content')
    <div class="max-w-2xl mx-auto space-y-5 animate-in">

        <div>
            <h1 class="text-xl font-bold text-slate-800">Mon Profil</h1>
            <p class="text-sm text-slate-400">Mettez à jour vos informations personnelles.</p>
        </div>

        <form action="{{ route('personnel.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">

                {{-- Photo de profil --}}
                <div class="flex items-center gap-5">
                    <div
                        class="w-16 h-16 rounded-2xl overflow-hidden bg-[#1d3557]/10 flex items-center justify-center text-[#1d3557] font-black text-2xl border border-slate-200 shrink-0">
                        @if ($personnel->profile_picture)
                            <img src="{{ asset('storage/' . $personnel->profile_picture) }}"
                                class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($personnel->name, 0, 1)) }}
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Photo de profil</label>
                        <input type="file" name="profile_picture" accept="image/*"
                            class="text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#1d3557]/10 file:text-[#1d3557] hover:file:bg-[#1d3557]/20 transition">
                        <p class="text-[10px] text-slate-400 mt-1">JPG, PNG ou WEBP — max 2 Mo</p>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Nom <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $personnel->name) }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1d3557]/20 focus:border-[#1d3557]/40 transition">
                        @error('name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom', $personnel->prenom) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1d3557]/20 focus:border-[#1d3557]/40 transition">
                    </div>
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Contact</label>
                        <input type="text" name="contact" value="{{ old('contact', $personnel->contact) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1d3557]/20 focus:border-[#1d3557]/40 transition">
                    </div>
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Adresse</label>
                        <input type="text" name="adresse" value="{{ old('adresse', $personnel->adresse) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1d3557]/20 focus:border-[#1d3557]/40 transition">
                    </div>
                </div>

                {{-- Code (lecture seule) --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Code
                        personnel</label>
                    <input type="text" value="{{ $personnel->code_user }}" readonly
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-500 bg-slate-50 font-mono cursor-not-allowed">
                </div>

            </div>

            <div class="mt-4 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#1d3557] text-white text-sm font-semibold hover:bg-[#152840] active:scale-95 transition shadow-sm">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
@endsection
