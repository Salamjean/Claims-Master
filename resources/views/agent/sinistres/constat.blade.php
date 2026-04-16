@extends('agent.layouts.template')
@section('title', 'Faire le constat')
@section('page-title', 'Constat')

@section('content')
    <div class="mx-auto space-y-5" style="width: 100%;">

        {{-- En-tête --}}
        <div class="flex items-center gap-4 flex-wrap">
            <a href="{{ route('agent.sinistres.en_attente') }}"
                class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-sm">
                <i class="fa-solid fa-arrow-left text-slate-500 text-sm"></i>
            </a>
            <div>
                <h2 class="text-xl font-extrabold text-slate-800">
                    {{ $isAccident ? "Constat d'accident" : "Constat d'incident" }}
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">
                    Sinistre #{{ $sinistre->id }} &mdash;
                    <span class="font-semibold text-slate-700">{{ $sinistre->assure->name ?? '' }}</span>
                    &mdash; {{ str_replace('_', ' ', $sinistre->type_sinistre) }}
                </p>
            </div>
        </div>
        <form action="{{ route('agent.sinistres.constat.store', $sinistre->id) }}" method="POST"
            enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                    <i class="fa-solid fa-file-signature text-blue-600"></i>
                    <h3 class="font-bold text-slate-800">Informations générales</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label">Lieu des faits</label>
                            <input type="text" name="lieu" class="input"
                                placeholder="Ex: Avenue Habib Bourguiba, Tunis" required>
                        </div>
                        <div>
                            <label class="label">Date et Heure</label>
                            <input type="datetime-local" name="date_heure" class="input"
                                value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Description & Témoins --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                        <i class="fa-solid fa-align-left text-blue-600"></i>
                        <h3 class="font-bold text-slate-800">Description & Témoins</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="label">Description des faits</label>
                            <textarea name="description_faits" rows="4" class="input" placeholder="Détaillez ce qui s'est passé..." required></textarea>
                        </div>
                        <div>
                            <label class="label">Témoins (Nom, contact)</label>
                            <textarea name="temoins" rows="2" class="input" placeholder="Informations sur d'éventuels témoins..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Dommages & Observations --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                        <i class="fa-solid fa-eye text-blue-600"></i>
                        <h3 class="font-bold text-slate-800">Dommages & Observations</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="label">Dommages constatés</label>
                            <textarea name="dommages" rows="4" class="input" placeholder="Décrivez les dégâts matériels..." required></textarea>
                        </div>
                        <div>
                            <label class="label">Observations de l'Agent</label>
                            <textarea name="observations" rows="2" class="input" placeholder="Vos remarques particulières..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Croquis --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div
                        class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-pen-nib text-blue-600"></i>
                            <h3 class="font-bold text-slate-800">Croquis (Dessiner ou Photo)</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" id="agent-sketch-draw-btn" onclick="setAgentSketchMode('draw')"
                                class="text-[9px] px-2 py-1 rounded-lg bg-blue-600 text-white font-bold transition-all">✏
                                Dessiner</button>
                            <button type="button" id="agent-sketch-move-btn" onclick="setAgentSketchMode('select')"
                                class="text-[9px] px-2 py-1 rounded-lg bg-slate-200 text-slate-600 font-bold transition-all">↖
                                Déplacer</button>
                            <button type="button" onclick="clearCanvas()"
                                class="text-xs text-rose-600 font-bold hover:underline">Effacer</button>
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        {{-- Palette de symboles --}}
                        <div id="agent-sketch-palette" class="flex items-center gap-1.5 overflow-x-auto pb-1.5 select-none"
                            style="scrollbar-width:thin;">
                            <span class="text-[8px] text-slate-400 font-medium whitespace-nowrap shrink-0">Cliquer ou
                                glisser :</span>

                            {{-- Auto --}}
                            <div class="agent-sym-item shrink-0 cursor-grab flex flex-col items-center gap-0.5 px-2 py-1.5 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-all"
                                draggable="true" data-sym="auto" onclick="addAgentSymbol('auto')" title="Auto">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 64" width="26" height="26">
                                    <rect x="5" y="12" width="30" height="40" rx="6" fill="#64748b"
                                        stroke="#1e293b" stroke-width="1.5" />
                                    <rect x="9" y="15" width="22" height="10" rx="3" fill="#bfdbfe" />
                                    <rect x="9" y="40" width="22" height="8" rx="3" fill="#bfdbfe" />
                                    <rect x="1" y="15" width="5" height="9" rx="2" fill="#1e293b" />
                                    <rect x="34" y="15" width="5" height="9" rx="2" fill="#1e293b" />
                                    <rect x="1" y="38" width="5" height="9" rx="2" fill="#1e293b" />
                                    <rect x="34" y="38" width="5" height="9" rx="2" fill="#1e293b" />
                                </svg>
                                <span class="text-[7px] text-slate-500 font-semibold whitespace-nowrap">Auto</span>
                            </div>

                            {{-- 2 Roues --}}
                            <div class="agent-sym-item shrink-0 cursor-grab flex flex-col items-center gap-0.5 px-2 py-1.5 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-all"
                                draggable="true" data-sym="deux_roues" onclick="addAgentSymbol('deux_roues')"
                                title="2 Roues">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 64" width="14"
                                    height="26">
                                    <rect x="5" y="6" width="12" height="52" rx="5" fill="#475569"
                                        stroke="#1e293b" stroke-width="1.5" />
                                    <rect x="1" y="9" width="5" height="12" rx="2.5" fill="#1e293b" />
                                    <rect x="16" y="9" width="5" height="12" rx="2.5" fill="#1e293b" />
                                    <rect x="1" y="43" width="5" height="12" rx="2.5" fill="#1e293b" />
                                    <rect x="16" y="43" width="5" height="12" rx="2.5" fill="#1e293b" />
                                </svg>
                                <span class="text-[7px] text-slate-500 font-semibold whitespace-nowrap">2 Roues</span>
                            </div>

                            {{-- Camion --}}
                            <div class="agent-sym-item shrink-0 cursor-grab flex flex-col items-center gap-0.5 px-2 py-1.5 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-all"
                                draggable="true" data-sym="camion" onclick="addAgentSymbol('camion')" title="Camion">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 90" width="22"
                                    height="26">
                                    <rect x="4" y="4" width="42" height="20" rx="4" fill="#6b7280"
                                        stroke="#1e293b" stroke-width="1.5" />
                                    <rect x="8" y="7" width="34" height="11" rx="3" fill="#bfdbfe" />
                                    <rect x="3" y="28" width="44" height="58" rx="3" fill="#9ca3af"
                                        stroke="#1e293b" stroke-width="1.5" />
                                    <line x1="9" y1="57" x2="41" y2="57" stroke="#6b7280"
                                        stroke-width="1.5" stroke-dasharray="4,3" />
                                    <rect x="0" y="8" width="5" height="12" rx="2" fill="#1e293b" />
                                    <rect x="45" y="8" width="5" height="12" rx="2" fill="#1e293b" />
                                    <rect x="0" y="63" width="5" height="12" rx="2" fill="#1e293b" />
                                    <rect x="45" y="63" width="5" height="12" rx="2" fill="#1e293b" />
                                </svg>
                                <span class="text-[7px] text-slate-500 font-semibold whitespace-nowrap">Camion</span>
                            </div>

                            {{-- Piéton --}}
                            <div class="agent-sym-item shrink-0 cursor-grab flex flex-col items-center gap-0.5 px-2 py-1.5 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-all"
                                draggable="true" data-sym="pieton" onclick="addAgentSymbol('pieton')" title="Piéton">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 46" width="18"
                                    height="26">
                                    <circle cx="15" cy="7" r="6" fill="#e2e8f0" stroke="#1e293b"
                                        stroke-width="1.5" />
                                    <line x1="15" y1="13" x2="15" y2="30" stroke="#1e293b"
                                        stroke-width="2.5" stroke-linecap="round" />
                                    <line x1="5" y1="21" x2="25" y2="21" stroke="#1e293b"
                                        stroke-width="2.5" stroke-linecap="round" />
                                    <line x1="15" y1="30" x2="7" y2="44" stroke="#1e293b"
                                        stroke-width="2.5" stroke-linecap="round" />
                                    <line x1="15" y1="30" x2="23" y2="44" stroke="#1e293b"
                                        stroke-width="2.5" stroke-linecap="round" />
                                </svg>
                                <span class="text-[7px] text-slate-500 font-semibold whitespace-nowrap">Piéton</span>
                            </div>

                            {{-- Feu tricolore --}}
                            <div class="agent-sym-item shrink-0 cursor-grab flex flex-col items-center gap-0.5 px-2 py-1.5 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-all"
                                draggable="true" data-sym="feu" onclick="addAgentSymbol('feu')" title="Feu tricolore">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 58" width="12"
                                    height="26">
                                    <rect x="2" y="2" width="20" height="40" rx="4" fill="#1e293b"
                                        stroke="#374151" stroke-width="1" />
                                    <circle cx="12" cy="10" r="5" fill="#ef4444" />
                                    <circle cx="12" cy="22" r="5" fill="#f59e0b" />
                                    <circle cx="12" cy="34" r="5" fill="#22c55e" />
                                    <rect x="10" y="42" width="4" height="12" fill="#374151" />
                                    <rect x="4" y="52" width="16" height="4" rx="2" fill="#374151" />
                                </svg>
                                <span class="text-[7px] text-slate-500 font-semibold whitespace-nowrap">Feu</span>
                            </div>

                            {{-- Balise de priorité --}}
                            <div class="agent-sym-item shrink-0 cursor-grab flex flex-col items-center gap-0.5 px-2 py-1.5 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-all"
                                draggable="true" data-sym="balise" onclick="addAgentSymbol('balise')"
                                title="Balise de priorité">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 64" width="22"
                                    height="26">
                                    <polygon points="25,3 47,46 3,46" fill="white" stroke="#1e293b"
                                        stroke-width="2" />
                                    <polygon points="25,11 41,43 9,43" fill="none" stroke="#e11d48"
                                        stroke-width="1.5" />
                                    <line x1="25" y1="46" x2="25" y2="60" stroke="#374151"
                                        stroke-width="3" stroke-linecap="round" />
                                    <circle cx="25" cy="63" r="3" fill="#374151" />
                                </svg>
                                <span class="text-[7px] text-slate-500 font-semibold whitespace-nowrap">Balise</span>
                            </div>

                            {{-- Stop --}}
                            <div class="agent-sym-item shrink-0 cursor-grab flex flex-col items-center gap-0.5 px-2 py-1.5 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-all"
                                draggable="true" data-sym="stop" onclick="addAgentSymbol('stop')" title="Stop">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 64" width="22"
                                    height="26">
                                    <polygon points="15,2 35,2 48,15 48,35 35,48 15,48 2,35 2,15" fill="#dc2626"
                                        stroke="#1e293b" stroke-width="1.5" />
                                    <text x="25" y="31" text-anchor="middle" fill="white" font-size="12"
                                        font-family="Arial" font-weight="bold">STOP</text>
                                    <line x1="25" y1="48" x2="25" y2="60" stroke="#374151"
                                        stroke-width="3" stroke-linecap="round" />
                                    <circle cx="25" cy="63" r="3" fill="#374151" />
                                </svg>
                                <span class="text-[7px] text-slate-500 font-semibold whitespace-nowrap">Stop</span>
                            </div>
                        </div>

                        {{-- Canvas Fabric.js --}}
                        <div id="agent-sketch-wrapper"
                            class="relative rounded-xl overflow-hidden border-2 border-dashed border-slate-200 bg-slate-50"
                            style="height:340px;">
                            <canvas id="croquis-canvas"></canvas>
                            <p id="agent-sketch-hint"
                                class="absolute bottom-3 right-4 text-[8px] text-slate-300 italic pointer-events-none select-none">
                                Dessinez ou déposez des symboles</p>
                        </div>
                        <input type="hidden" name="croquis_data" id="croquis-data">

                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div class="w-full sm:w-1/2">
                                <label class="label text-[10px]">Ou importer une photo du croquis</label>
                                <input type="file" name="croquis_file" class="input text-xs" accept="image/*">
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold tracking-tight text-center sm:text-left">
                            Priorité à la photo si les deux sont fournis</p>
                    </div>
                </div>

                {{-- Pièces jointes --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                        <i class="fa-solid fa-camera text-blue-600"></i>
                        <h3 class="font-bold text-slate-800">Photos & Pièces Jointes</h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Assurance Partie A</label>
                                <input type="file" name="ass1_photo" class="input text-xs" accept="image/*">
                            </div>
                            <div>
                                <label class="label">Assurance Partie B</label>
                                <input type="file" name="ass2_photo" class="input text-xs" accept="image/*">
                            </div>
                        </div>
                        <hr class="border-slate-100">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="label">Autres justificatifs / Photos du lieu</label>
                                <button type="button" onclick="addPhotoField()"
                                    class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-200 transition-colors">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                </button>
                            </div>
                            <div id="photos-container" class="space-y-2">
                                <input type="file" name="photos_plus[]" class="input text-xs" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pb-8">
                <a href="{{ route('agent.sinistres.en_attente') }}"
                    class="px-6 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">Annuler</a>
                <button type="submit"
                    class="px-10 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-extrabold transition-all shadow-lg active:scale-95">
                    Clôturer le constat
                </button>
            </div>
        </form>
    </div>

    <style>
        .label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .input {
            display: block;
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 13px;
            color: #1e293b;
            background: #fff;
            outline: none;
            transition: all .2s;
        }

        .input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, .1);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/fabric@5.3.0/dist/fabric.min.js"></script>
    <script>
        function addPhotoField() {
            const container = document.getElementById('photos-container');
            const div = document.createElement('div');
            div.className = 'flex items-center gap-2 mt-2';
            div.innerHTML = `
                <input type="file" name="photos_plus[]" class="input text-xs flex-1" accept="image/*">
                <button type="button" onclick="this.parentElement.remove()" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-100 transition-colors">
                    <i class="fa-solid fa-trash-can text-[10px]"></i>
                </button>
            `;
            container.appendChild(div);
        }

        // ---- Symboles SVG pour le croquis agent ----
        const AGENT_SYMBOLS = {
            auto: {
                scale: 0.75,
                svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 64" width="40" height="64">
                    <rect x="5" y="12" width="30" height="40" rx="6" fill="#64748b" stroke="#1e293b" stroke-width="1.5"/>
                    <rect x="9" y="15" width="22" height="10" rx="3" fill="#bfdbfe"/>
                    <rect x="9" y="40" width="22" height="8" rx="3" fill="#bfdbfe"/>
                    <rect x="1" y="15" width="5" height="9" rx="2" fill="#1e293b"/>
                    <rect x="34" y="15" width="5" height="9" rx="2" fill="#1e293b"/>
                    <rect x="1" y="38" width="5" height="9" rx="2" fill="#1e293b"/>
                    <rect x="34" y="38" width="5" height="9" rx="2" fill="#1e293b"/>
                </svg>`
            },
            deux_roues: {
                scale: 0.75,
                svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 64" width="22" height="64">
                    <rect x="5" y="6" width="12" height="52" rx="5" fill="#475569" stroke="#1e293b" stroke-width="1.5"/>
                    <rect x="1" y="9" width="5" height="12" rx="2.5" fill="#1e293b"/>
                    <rect x="16" y="9" width="5" height="12" rx="2.5" fill="#1e293b"/>
                    <rect x="1" y="43" width="5" height="12" rx="2.5" fill="#1e293b"/>
                    <rect x="16" y="43" width="5" height="12" rx="2.5" fill="#1e293b"/>
                </svg>`
            },
            camion: {
                scale: 0.65,
                svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 90" width="50" height="90">
                    <rect x="4" y="4" width="42" height="20" rx="4" fill="#6b7280" stroke="#1e293b" stroke-width="1.5"/>
                    <rect x="8" y="7" width="34" height="11" rx="3" fill="#bfdbfe"/>
                    <rect x="3" y="28" width="44" height="58" rx="3" fill="#9ca3af" stroke="#1e293b" stroke-width="1.5"/>
                    <line x1="9" y1="57" x2="41" y2="57" stroke="#6b7280" stroke-width="1.5" stroke-dasharray="4,3"/>
                    <rect x="0" y="8" width="5" height="12" rx="2" fill="#1e293b"/>
                    <rect x="45" y="8" width="5" height="12" rx="2" fill="#1e293b"/>
                    <rect x="0" y="63" width="5" height="12" rx="2" fill="#1e293b"/>
                    <rect x="45" y="63" width="5" height="12" rx="2" fill="#1e293b"/>
                </svg>`
            },
            pieton: {
                scale: 0.9,
                svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 46" width="30" height="46">
                    <circle cx="15" cy="7" r="6" fill="#e2e8f0" stroke="#1e293b" stroke-width="1.5"/>
                    <line x1="15" y1="13" x2="15" y2="30" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="5" y1="21" x2="25" y2="21" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="15" y1="30" x2="7" y2="44" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="15" y1="30" x2="23" y2="44" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round"/>
                </svg>`
            },
            feu: {
                scale: 0.9,
                svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 58" width="24" height="58">
                    <rect x="2" y="2" width="20" height="40" rx="4" fill="#1e293b" stroke="#374151" stroke-width="1"/>
                    <circle cx="12" cy="10" r="5" fill="#ef4444"/>
                    <circle cx="12" cy="22" r="5" fill="#f59e0b"/>
                    <circle cx="12" cy="34" r="5" fill="#22c55e"/>
                    <rect x="10" y="42" width="4" height="12" fill="#374151"/>
                    <rect x="4" y="52" width="16" height="4" rx="2" fill="#374151"/>
                </svg>`
            },
            balise: {
                scale: 0.75,
                svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 64" width="50" height="64">
                    <polygon points="25,3 47,46 3,46" fill="white" stroke="#1e293b" stroke-width="2"/>
                    <polygon points="25,11 41,43 9,43" fill="none" stroke="#e11d48" stroke-width="1.5"/>
                    <line x1="25" y1="46" x2="25" y2="60" stroke="#374151" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="25" cy="63" r="3" fill="#374151"/>
                </svg>`
            },
            stop: {
                scale: 0.75,
                svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 64" width="50" height="64">
                    <polygon points="15,2 35,2 48,15 48,35 35,48 15,48 2,35 2,15" fill="#dc2626" stroke="#1e293b" stroke-width="1.5"/>
                    <text x="25" y="31" text-anchor="middle" fill="white" font-size="12" font-family="Arial" font-weight="bold">STOP</text>
                    <line x1="25" y1="48" x2="25" y2="60" stroke="#374151" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="25" cy="63" r="3" fill="#374151"/>
                </svg>`
            }
        };

        let agentFabricCanvas = null;

        function initAgentSketch() {
            const wrapper = document.getElementById('agent-sketch-wrapper');
            if (!wrapper) return;
            if (agentFabricCanvas) {
                agentFabricCanvas.dispose();
                agentFabricCanvas = null;
            }
            agentFabricCanvas = new fabric.Canvas('croquis-canvas', {
                width: wrapper.clientWidth,
                height: wrapper.clientHeight,
                isDrawingMode: true,
                backgroundColor: 'transparent',
                selection: true,
            });
            agentFabricCanvas.freeDrawingBrush.color = '#1e293b';
            agentFabricCanvas.freeDrawingBrush.width = 3;
            agentFabricCanvas.freeDrawingBrush.decimate = 4;

            // Mettre à jour l'input caché à chaque modification
            const saveData = () => {
                if (agentFabricCanvas.getObjects().length > 0) {
                    document.getElementById('croquis-data').value = agentFabricCanvas.toDataURL({
                        format: 'png',
                        multiplier: 1
                    });
                    document.getElementById('agent-sketch-hint').classList.add('hidden');
                } else {
                    document.getElementById('croquis-data').value = '';
                    document.getElementById('agent-sketch-hint').classList.remove('hidden');
                }
            };
            agentFabricCanvas.on('object:added', saveData);
            agentFabricCanvas.on('object:modified', saveData);
            agentFabricCanvas.on('object:removed', saveData);
            agentFabricCanvas.on('path:created', saveData);

            // Drag-and-drop
            wrapper.addEventListener('dragover', e => e.preventDefault());
            wrapper.addEventListener('drop', e => {
                e.preventDefault();
                const sym = e.dataTransfer.getData('text/plain');
                if (!sym) return;
                const rect = wrapper.getBoundingClientRect();
                addAgentSymbol(sym, e.clientX - rect.left, e.clientY - rect.top);
            });

            document.querySelectorAll('.agent-sym-item').forEach(item => {
                item.addEventListener('dragstart', e => {
                    e.dataTransfer.setData('text/plain', item.dataset.sym);
                    e.dataTransfer.effectAllowed = 'copy';
                });
            });

            // Touche Suppr
            document.addEventListener('keydown', e => {
                if (!agentFabricCanvas) return;
                if ((e.key === 'Delete' || e.key === 'Backspace') && !agentFabricCanvas.isDrawingMode) {
                    const obj = agentFabricCanvas.getActiveObject();
                    if (obj) {
                        agentFabricCanvas.remove(obj);
                        agentFabricCanvas.renderAll();
                    }
                }
            });
        }

        window.addAgentSymbol = function(type, dropX, dropY) {
            if (!agentFabricCanvas) return;
            const sym = AGENT_SYMBOLS[type];
            if (!sym) return;
            fabric.loadSVGFromString(sym.svg, function(objects, options) {
                const group = fabric.util.groupSVGElements(objects, options);
                const cx = dropX !== undefined ? dropX : agentFabricCanvas.width / 2;
                const cy = dropY !== undefined ? dropY : agentFabricCanvas.height / 2;
                group.set({
                    left: cx - (group.width * sym.scale) / 2,
                    top: cy - (group.height * sym.scale) / 2,
                    scaleX: sym.scale,
                    scaleY: sym.scale,
                    hasControls: true,
                    hasBorders: true,
                    cornerSize: 8,
                    cornerColor: '#3b82f6',
                    borderColor: '#3b82f6',
                });
                agentFabricCanvas.add(group);
                agentFabricCanvas.setActiveObject(group);
                setAgentSketchMode('select');
                agentFabricCanvas.renderAll();
            });
        };

        window.setAgentSketchMode = function(mode) {
            if (!agentFabricCanvas) return;
            if (mode === 'draw') {
                agentFabricCanvas.isDrawingMode = true;
                document.getElementById('agent-sketch-draw-btn').className =
                    'text-[9px] px-2 py-1 rounded-lg bg-blue-600 text-white font-bold transition-all';
                document.getElementById('agent-sketch-move-btn').className =
                    'text-[9px] px-2 py-1 rounded-lg bg-slate-200 text-slate-600 font-bold transition-all';
            } else {
                agentFabricCanvas.isDrawingMode = false;
                document.getElementById('agent-sketch-draw-btn').className =
                    'text-[9px] px-2 py-1 rounded-lg bg-slate-200 text-slate-600 font-bold transition-all';
                document.getElementById('agent-sketch-move-btn').className =
                    'text-[9px] px-2 py-1 rounded-lg bg-blue-600 text-white font-bold transition-all';
            }
        };

        function clearCanvas() {
            if (agentFabricCanvas) {
                agentFabricCanvas.clear();
                agentFabricCanvas.backgroundColor = 'transparent';
                agentFabricCanvas.renderAll();
                document.getElementById('croquis-data').value = '';
                document.getElementById('agent-sketch-hint').classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', initAgentSketch);
    </script>
@endsection
