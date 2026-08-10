@extends('layouts.app')

@section('title', 'Signaler une Urgence — Claims Master')
@section('description', 'Signalez un accident, un incendie ou une urgence sans connexion requise. Les Sapeurs-Pompiers seront alertés immédiatement.')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body, input, button, select, textarea { font-family: 'Inter', sans-serif; }

        body {
            background: #f8fafc;
            background-image: 
                radial-gradient(circle at 10% 5%, rgba(185, 18, 60, 0.04) 0%, transparent 40%),
                radial-gradient(circle at 90% 95%, rgba(37, 99, 235, 0.04) 0%, transparent 45%),
                linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
        }

        .urgence-wrapper {
            min-height: 100vh;
            padding: 2.5rem 1.5rem 4rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .urgence-container {
            width: 100%;
            max-width: 1100px;
        }

        /* Top Header */
        .top-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
        }

        .btn-back-home {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #475569;
            font-size: 0.8125rem;
            font-weight: 700;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            transition: all 0.2s ease;
        }

        .btn-back-home:hover {
            color: #0f172a;
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-1px);
        }

        .live-status-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #059669;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            padding: 7px 16px;
            border-radius: 999px;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2);
            animation: pulseGreen 1.4s infinite;
        }

        @keyframes pulseGreen {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.5; }
        }

        /* 2-Column Main Shell */
        .main-shell {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 32px;
            box-shadow: 0 20px 50px -15px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            display: grid;
            grid-template-columns: 0.95fr 1.05fr;
        }

        /* Left Hero Banner */
        .left-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #881337 100%);
            padding: 3rem 2.5rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .left-banner-glow {
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(185, 18, 60, 0.4) 0%, transparent 70%);
            pointer-events: none;
        }

        .banner-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 999px;
            margin-bottom: 1.5rem;
            width: fit-content;
        }

        .banner-hero-title {
            font-size: 2.5rem;
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: -0.03em;
            margin-bottom: 1rem;
        }

        .banner-hero-title span {
            color: #f87171;
        }

        .banner-hero-desc {
            font-size: 0.9375rem;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .feature-bullets {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2.5rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            font-size: 0.875rem;
            color: #e2e8f0;
            font-weight: 600;
        }

        .feature-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fca5a5;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .emergency-hotline-card {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .hotline-lbl {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
        }

        .hotline-num {
            font-size: 1.25rem;
            font-weight: 900;
            color: #ffffff;
        }

        .btn-call-18 {
            padding: 8px 16px;
            background: #ef4444;
            color: white;
            border-radius: 12px;
            font-size: 0.8125rem;
            font-weight: 800;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-call-18:hover {
            background: #dc2626;
            transform: scale(1.03);
        }

        /* Right Form Panel */
        .right-form-panel {
            padding: 2.75rem 2.5rem;
            background: #ffffff;
        }

        /* Stepper Header */
        .stepper-tabs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.625rem;
            margin-bottom: 2rem;
        }

        .step-tab {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.75rem 0.875rem;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .step-tab.active {
            background: #fff1f2;
            border-color: #fecdd3;
        }

        .step-tab.complete {
            background: #ecfdf5;
            border-color: #a7f3d0;
        }

        .step-num {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #cbd5e1;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .step-tab.active .step-num {
            background: #B9123C;
            color: #ffffff;
        }

        .step-tab.complete .step-num {
            background: #10b981;
            color: #ffffff;
        }

        .step-info {
            display: flex;
            flex-direction: column;
        }

        .step-info-kicker {
            font-size: 0.625rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
        }

        .step-info-title {
            font-size: 0.78125rem;
            font-weight: 800;
            color: #0f172a;
        }

        /* Form Panels */
        .form-step-panel {
            display: none;
            animation: fadeIn 0.25s ease;
        }

        .form-step-panel.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .panel-heading-title {
            font-size: 1.5rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin-bottom: 0.375rem;
        }

        .panel-heading-desc {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        /* Type Grid */
        .type-select-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.625rem;
            margin-bottom: 1.5rem;
        }

        .type-btn-card {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 0.875rem 0.5rem;
            cursor: pointer;
            transition: all 0.18s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.375rem;
            user-select: none;
        }

        .type-btn-card:hover {
            border-color: #B9123C;
            background: #fff1f2;
            transform: translateY(-2px);
        }

        .type-btn-card.selected {
            border-color: #B9123C;
            background: linear-gradient(135deg, #fff1f2, #ffe4e6);
            box-shadow: 0 6px 18px rgba(185, 18, 60, 0.15);
            transform: translateY(-2px);
        }

        .type-btn-card .emoji { font-size: 1.5rem; }

        .type-btn-card .title {
            font-size: 0.6875rem;
            font-weight: 800;
            color: #475569;
            text-align: center;
            line-height: 1.2;
        }

        .type-btn-card.selected .title { color: #B9123C; }

        /* Form Inputs */
        .form-field {
            margin-bottom: 1.25rem;
        }

        .form-label-custom {
            display: block;
            font-size: 0.78125rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.375rem;
        }

        .form-label-custom .req { color: #B9123C; }

        .form-control-custom {
            width: 100%;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            font-size: 0.875rem;
            color: #0f172a;
            padding: 0.75rem 0.9375rem;
            outline: none;
            transition: all 0.18s ease;
        }

        .form-control-custom:focus {
            border-color: #B9123C;
            box-shadow: 0 0 0 3px rgba(185, 18, 60, 0.1);
        }

        .form-control-custom::placeholder { color: #94a3b8; }

        textarea.form-control-custom {
            resize: vertical;
            min-height: 110px;
        }

        /* Geo Refresh Button Row */
        .geo-input-row {
            display: flex;
            gap: 0.625rem;
            align-items: center;
        }

        .geo-input-wrapper {
            position: relative;
            flex: 1;
        }

        .geo-dropdown-list {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.14);
            max-height: 220px;
            overflow-y: auto;
            z-index: 100;
            display: none;
        }

        .geo-dropdown-item {
            padding: 10px 14px;
            font-size: 0.8125rem;
            color: #334155;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease;
        }

        .geo-dropdown-item:last-child {
            border-bottom: none;
        }

        .geo-dropdown-item:hover {
            background: #fff1f2;
            color: #B9123C;
            font-weight: 700;
        }

        .btn-geo-refresh {
            height: 46px;
            padding: 0 16px;
            background: #eff6ff;
            border: 1.5px solid #bfdbfe;
            color: #2563eb;
            font-size: 0.8125rem;
            font-weight: 800;
            border-radius: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.18s ease;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .btn-geo-refresh:hover {
            background: #dbeafe;
            border-color: #93c5fd;
            color: #1d4ed8;
        }

        /* Photo Upload Dropzone */
        .photo-dropzone {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 20px;
            padding: 1.5rem 1.25rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            display: block;
        }

        .photo-dropzone:hover {
            border-color: #B9123C;
            background: #fff1f2;
        }

        .photo-dropzone input { display: none; }
        .photo-icon-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #B9123C;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin: 0 auto 0.625rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
        }

        .photo-dropzone-title { font-size: 0.875rem; font-weight: 800; color: #0f172a; }
        .photo-dropzone-sub { font-size: 0.75rem; color: #64748b; margin-top: 3px; }

        /* Thumbnails Preview Grid */
        .photos-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .thumb-preview-card {
            position: relative;
            height: 85px;
            border-radius: 14px;
            overflow: hidden;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .thumb-preview-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Two columns */
        .grid-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* SMS Box Notice */
        .sms-info-box {
            background: #fffbeb;
            border: 1.5px solid #fde68a;
            border-radius: 14px;
            padding: 0.875rem 1rem;
            font-size: 0.78125rem;
            color: #92400e;
            font-weight: 600;
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            margin-bottom: 1.25rem;
            line-height: 1.45;
        }

        /* Live Summary Box (Step 3) */
        .live-summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.25rem;
            margin-top: 1rem;
            margin-bottom: 1.5rem;
        }

        .live-summary-title {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            margin-bottom: 0.75rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.84375rem;
        }

        .summary-row:last-child { border-bottom: none; }
        .summary-row .label { color: #64748b; font-weight: 600; }
        .summary-row .value { color: #0f172a; font-weight: 800; text-align: right; }

        /* Step Footer Actions */
        .step-footer-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1.75rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f1f5f9;
        }

        .btn-wizard-prev {
            padding: 12px 20px;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            color: #475569;
            font-size: 0.8125rem;
            font-weight: 700;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-wizard-prev:hover {
            background: #f8fafc;
            color: #0f172a;
        }

        .btn-wizard-next {
            padding: 14px 24px;
            background: linear-gradient(135deg, #B9123C 0%, #881337 100%);
            border: none;
            color: #ffffff;
            font-size: 0.875rem;
            font-weight: 800;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 6px 18px rgba(185, 18, 60, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-wizard-next:hover {
            background: linear-gradient(135deg, #dc2626 0%, #B9123C 100%);
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(185, 18, 60, 0.35);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .main-shell { grid-template-columns: 1fr; }
            .left-banner { padding: 2rem 1.5rem; }
            .right-form-panel { padding: 2rem 1.25rem; }
            .type-select-grid { grid-template-columns: repeat(4, 1fr); }
        }

        @media (max-width: 500px) {
            .type-select-grid { grid-template-columns: repeat(2, 1fr); }
            .grid-2col { grid-template-columns: 1fr; }
            .stepper-tabs { grid-template-columns: repeat(3, 1fr); }
            .step-info { display: none; }
        }
    </style>
@endpush

@section('content')
<div class="urgence-wrapper">
    <div class="urgence-container">

        <!-- Barre Supérieure de Navigation -->
        <div class="top-header-bar">
            <a href="{{ route('home') }}" class="btn-back-home">
                <i class="fa-solid fa-arrow-left"></i> Retour à l'accueil
            </a>

            <div class="live-status-chip">
                <span class="live-dot"></span>
                <span>Réseau Secours Connecté</span>
            </div>
        </div>

        <!-- Coquille Principale 2 Colonnes -->
        <div class="main-shell">

            <!-- COLONNE GAUCHE (Bannière Visuelle & Numéro d'Urgence) -->
            <div class="left-banner">
                <div class="left-banner-glow"></div>

                <div>
                    <div class="banner-tag">
                        <i class="fa-solid fa-bolt"></i> Signalement Direct Sans Compte
                    </div>

                    <h1 class="banner-hero-title">
                        Alertez les <span>Sapeurs-Pompiers</span> immédiatement.
                    </h1>

                    <p class="banner-hero-desc">
                        Vous êtes témoin d'un accident, d'un incendie ou d'une urgence ? Déclarez la situation en 3 étapes simples. Les secours sont informés en temps réel.
                    </p>

                    <div class="feature-bullets">
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fa-solid fa-location-dot"></i></div>
                            <span>Géolocalisation GPS exacte et automatique</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fa-solid fa-comment-sms"></i></div>
                            <span>SMS de suivi en direct pour le déclarant</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="fa-solid fa-camera"></i></div>
                            <span>Upload de photos pour évaluation terrain</span>
                        </div>
                    </div>
                </div>

                <div class="emergency-hotline-card">
                    <div>
                        <div class="hotline-lbl">En cas de danger vital immédiat</div>
                        <div class="hotline-num">18 / 112</div>
                    </div>
                    <a href="tel:18" class="btn-call-18">
                        <i class="fa-solid fa-phone-volume"></i> Appeler 18
                    </a>
                </div>
            </div>

            <!-- COLONNE DROITE (Formulaire Wizard 3 Étapes) -->
            <div class="right-form-panel">

                <!-- En-tête des étapes (Stepper Tabs) -->
                <div class="stepper-tabs">
                    <div class="step-tab active" id="tab-step-1">
                        <div class="step-num" id="num-step-1">1</div>
                        <div class="step-info">
                            <span class="step-info-kicker">Étape 1</span>
                            <span class="step-info-title">Urgence</span>
                        </div>
                    </div>

                    <div class="step-tab" id="tab-step-2">
                        <div class="step-num" id="num-step-2">2</div>
                        <div class="step-info">
                            <span class="step-info-kicker">Étape 2</span>
                            <span class="step-info-title">Lieu & Photo</span>
                        </div>
                    </div>

                    <div class="step-tab" id="tab-step-3">
                        <div class="step-num" id="num-step-3">3</div>
                        <div class="step-info">
                            <span class="step-info-kicker">Étape 3</span>
                            <span class="step-info-title">Validation</span>
                        </div>
                    </div>
                </div>

                <!-- Messages Flash d'Erreur / Succès -->
                @if(session('success'))
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:14px; font-size:0.84375rem; font-weight:700; margin-bottom:1.5rem; display:flex; align-items:center; gap:8px;">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div style="background:#fff1f2; border:1px solid #fecdd3; color:#9f1239; padding:12px 16px; border-radius:14px; font-size:0.8125rem; font-weight:700; margin-bottom:1.5rem;">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Corrigez les erreurs suivantes :
                        <ul style="padding-left:1.25rem; margin-top:4px; font-weight:500;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Formulaire Principal -->
                <form action="{{ route('alerte.store') }}" method="POST" enctype="multipart/form-data" id="alerte-form">
                    @csrf

                    <!-- ══════════ ÉTAPE 1 : NATURE DE L'URGENCE ══════════ -->
                    <div class="form-step-panel active" id="panel-step-1">
                        <div class="panel-heading-title">Sélectionnez le type d'urgence</div>
                        <div class="panel-heading-desc">Indiquez la nature de l'événement et décrivez ce que vous observez sur place.</div>

                        <div class="form-field">
                            <label class="form-label-custom">Type d'incident <span class="req">*</span></label>
                            <input type="hidden" name="type_sinistre" id="type_sinistre" value="{{ old('type_sinistre') }}" required>
                            
                            <div class="type-select-grid">
                                @foreach([
                                    ['val' => 'Accident de circulation', 'icon' => '🚗', 'label' => 'Accident'],
                                    ['val' => 'Incendie',               'icon' => '🔥', 'label' => 'Incendie'],
                                    ['val' => 'Malaise / Secours',      'icon' => '🩺', 'label' => 'Malaise'],
                                    ['val' => 'Noyade / Sauvetage',     'icon' => '🛟', 'label' => 'Sauvetage'],
                                    ['val' => 'Inondation',             'icon' => '🌊', 'label' => 'Inondation'],
                                    ['val' => 'Fuite de gaz',           'icon' => '💨', 'label' => 'Gaz'],
                                    ['val' => 'Effondrement',           'icon' => '🏚️', 'label' => 'Effondrement'],
                                    ['val' => 'Autre urgence',          'icon' => '🆘', 'label' => 'Autre'],
                                ] as $t)
                                    <button type="button"
                                            class="type-btn-card {{ old('type_sinistre') === $t['val'] ? 'selected' : '' }}"
                                            onclick="selectType(this, '{{ $t['val'] }}')"
                                            id="type-{{ Str::slug($t['val']) }}">
                                        <span class="emoji">{{ $t['icon'] }}</span>
                                        <span class="title">{{ $t['label'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                            <p id="type-error" style="display:none; font-size:0.75rem; color:#B9123C; font-weight:700; margin-top:4px;">
                                ← Veuillez sélectionner le type d'urgence
                            </p>
                        </div>

                        <div class="form-field">
                            <label class="form-label-custom" for="description">
                                Description de la situation <span style="font-size:0.7rem; color:#64748b; font-weight:600;">(optionnel)</span>
                            </label>
                            <textarea name="description" id="description" rows="4"
                                class="form-control-custom"
                                placeholder="Précisez la situation si possible : nombre de blessés, risques apparents, véhicule impliqué..."
                                oninput="updateSummary()">{{ old('description') }}</textarea>
                        </div>

                        <div class="step-footer-actions">
                            <div></div>
                            <button type="button" onclick="goToStep(2)" class="btn-wizard-next">
                                Continuer (Lieu & Photo) <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ══════════ ÉTAPE 2 : LOCALISATION & PHOTO ══════════ -->
                    <div class="form-step-panel" id="panel-step-2">
                        <div class="panel-heading-title">Lieu & Photo du sinistre</div>
                        <div class="panel-heading-desc">Votre position GPS est détectée automatiquement. Ajoutez au moins une photo du lieu.</div>

                        <!-- Champ Localisation GPS + Bouton Actualiser à côté -->
                        <div class="form-field">
                            <label class="form-label-custom" for="lieu">
                                Lieu exact (Détection GPS) <span class="req">*</span>
                            </label>
                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                            <div class="geo-input-row">
                                <div class="geo-input-wrapper">
                                    <input type="text" name="lieu" id="lieu" required
                                        class="form-control-custom"
                                        placeholder="Saisissez une adresse ou attendez la détection GPS..."
                                        value="{{ old('lieu') }}"
                                        oninput="handleLieuInput(this); updateSummary();"
                                        autocomplete="off">
                                    <div id="geo-dropdown-list" class="geo-dropdown-list"></div>
                                </div>

                                <button type="button" class="btn-geo-refresh" onclick="getLocation()" title="Actualiser la position GPS">
                                    <i class="fa-solid fa-arrows-rotate" id="geo-spin-icon"></i>
                                    <span>Actualiser</span>
                                </button>
                            </div>
                            <div id="geo-status" style="margin-top:4px;"></div>
                        </div>

                        <!-- Champ Photo + Prévisualisation des images -->
                        <div class="form-field">
                            <label class="form-label-custom">
                                Photo(s) du sinistre <span class="req">*</span>
                            </label>
                            <label class="photo-dropzone" for="photos" id="photo-drop-zone">
                                <input type="file" name="photos[]" id="photos" multiple accept="image/*" onchange="updatePhotoLabel(this)" required>
                                <div class="photo-icon-circle"><i class="fa-solid fa-camera"></i></div>
                                <div class="photo-dropzone-title" id="photo-drop-text">Cliquez ou glissez au moins 1 photo ici</div>
                                <div class="photo-dropzone-sub">Formats acceptés : JPG, PNG, WEBP (max 3 photos • 5 Mo par photo)</div>
                            </label>

                            <!-- Galerie de prévisualisation des photos sélectionnées -->
                            <div id="photos-preview-grid" class="photos-preview-grid" style="display:none;"></div>
                        </div>

                        <div class="step-footer-actions">
                            <button type="button" onclick="goToStep(1)" class="btn-wizard-prev">
                                <i class="fa-solid fa-arrow-left"></i> Retour
                            </button>
                            <button type="button" onclick="goToStep(3)" class="btn-wizard-next">
                                Continuer (Validation) <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ══════════ ÉTAPE 3 : VOS INFOS & VALIDATION ══════════ -->
                    <div class="form-step-panel" id="panel-step-3">
                        <div class="panel-heading-title">Vos informations & Transmission</div>
                        <div class="panel-heading-desc">Vérifiez les données avant d'envoyer l'alerte aux Sapeurs-Pompiers.</div>

                        <div class="sms-info-box">
                            <i class="fa-solid fa-comment-sms text-amber-600" style="font-size:1.1rem; flex-shrink:0; margin-top:2px;"></i>
                            <span>Indiquez votre numéro pour recevoir un <strong>SMS instantané</strong> avec le lien de suivi de l'intervention.</span>
                        </div>

                        <div class="grid-2col">
                            <div class="form-field">
                                <label class="form-label-custom" for="declarant_nom">Votre nom & prénom (optionnel)</label>
                                <input type="text" name="declarant_nom" id="declarant_nom"
                                    class="form-control-custom" placeholder="Ex: Jean Kouadio"
                                    value="{{ old('declarant_nom') }}"
                                    oninput="updateSummary()">
                            </div>
                            <div class="form-field">
                                <label class="form-label-custom" for="declarant_contact">Votre numéro de téléphone <span class="req">*</span></label>
                                <input type="tel" name="declarant_contact" id="declarant_contact" required
                                    class="form-control-custom" placeholder="Ex: 07 07 07 07 07"
                                    value="{{ old('declarant_contact') }}"
                                    oninput="updateSummary()">
                            </div>
                        </div>

                        <!-- Card de Récapitulatif -->
                        <div class="live-summary-card">
                            <div class="live-summary-title">Récapitulatif de votre alerte</div>
                            <div class="summary-row">
                                <span class="label">Incident</span>
                                <span class="value" id="sum-type">Non sélectionné</span>
                            </div>
                            <div class="summary-row">
                                <span class="label">Lieu</span>
                                <span class="value" id="sum-lieu">Non renseigné</span>
                            </div>
                            <div class="summary-row">
                                <span class="label">Déclarant</span>
                                <span class="value" id="sum-declarant">Passant Anonyme</span>
                            </div>
                            <div class="summary-row">
                                <span class="label">Photos</span>
                                <span class="value" id="sum-photos">Aucune photo</span>
                            </div>
                        </div>

                        <div class="step-footer-actions">
                            <button type="button" onclick="goToStep(2)" class="btn-wizard-prev">
                                <i class="fa-solid fa-arrow-left"></i> Retour
                            </button>
                            <button type="submit" class="btn-wizard-next" id="submit-btn">
                                <i class="fa-solid fa-paper-plane"></i> Transmettre l'Alerte Immédiatement
                            </button>
                        </div>
                    </div>

                </form>

            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentStepNum = 1;

    function selectType(btn, value) {
        document.querySelectorAll('.type-btn-card').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        document.getElementById('type_sinistre').value = value;
        document.getElementById('type-error').style.display = 'none';
        updateSummary();
    }

    function goToStep(step) {
        if (step > 1) {
            const type = document.getElementById('type_sinistre').value;
            if (!type) {
                document.getElementById('type-error').style.display = 'block';
                return;
            }
        }

        if (step > 2) {
            const lieu = document.getElementById('lieu').value.trim();
            const photos = document.getElementById('photos').files.length;

            if (!lieu || lieu === 'Détection de votre position...') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Lieu requis',
                    text: 'Veuillez indiquer le lieu de l’urgence.',
                    confirmButtonColor: '#B9123C'
                });
                return;
            }

            if (!photos) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Photo requise',
                    text: 'Veuillez ajouter au moins 1 photo du sinistre.',
                    confirmButtonColor: '#B9123C'
                });
                return;
            }
        }

        document.querySelectorAll('.form-step-panel').forEach(p => p.classList.remove('active'));
        document.getElementById('panel-step-' + step).classList.add('active');

        for (let i = 1; i <= 3; i++) {
            const tab = document.getElementById('tab-step-' + i);
            tab.classList.remove('active', 'complete');
            if (i < step) tab.classList.add('complete');
            else if (i === step) tab.classList.add('active');
        }

        currentStepNum = step;
        updateSummary();

        // Détection automatique du lieu lors du passage à l'étape 2 s'il n'est pas encore renseigné
        if (step === 2 && !document.getElementById('latitude').value) {
            getLocation();
        }
    }

    function updateSummary() {
        const type = document.getElementById('type_sinistre').value;
        const lieu = document.getElementById('lieu').value;
        const nom = document.getElementById('declarant_nom').value;
        const contact = document.getElementById('declarant_contact').value;
        const photos = document.getElementById('photos').files.length;

        document.getElementById('sum-type').textContent = type || 'Non sélectionné';
        document.getElementById('sum-lieu').textContent = lieu || 'Non renseigné';
        document.getElementById('sum-declarant').textContent = (nom || contact) ? (nom + (contact ? ' (' + contact + ')' : '')) : 'Passant Anonyme';
        document.getElementById('sum-photos').textContent = photos > 0 ? photos + ' photo(s) jointe(s)' : 'Aucune photo';
    }

    let autocompleteTimeout = null;

    function handleLieuInput(input) {
        const query = input.value.trim();
        const dropdown = document.getElementById('geo-dropdown-list');

        clearTimeout(autocompleteTimeout);

        if (query.length < 3) {
            dropdown.style.display = 'none';
            return;
        }

        autocompleteTimeout = setTimeout(() => {
            const searchUrl = 'https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query) + '&accept-language=fr&limit=5';

            fetch(searchUrl, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    dropdown.innerHTML = '';
                    if (data && data.length > 0) {
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'geo-dropdown-item';
                            div.innerHTML = '<i class="fa-solid fa-location-dot" style="color:#B9123C; flex-shrink:0;"></i> <span>' + item.display_name + '</span>';
                            div.onclick = function() {
                                document.getElementById('lieu').value = item.display_name;
                                document.getElementById('latitude').value = item.lat;
                                document.getElementById('longitude').value = item.lon;
                                dropdown.style.display = 'none';
                                updateSummary();
                            };
                            dropdown.appendChild(div);
                        });
                        dropdown.style.display = 'block';
                    } else {
                        dropdown.style.display = 'none';
                    }
                })
                .catch(() => {
                    dropdown.style.display = 'none';
                });
        }, 300);
    }

    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('geo-dropdown-list');
        const lieuInput = document.getElementById('lieu');
        if (dropdown && !dropdown.contains(e.target) && e.target !== lieuInput) {
            dropdown.style.display = 'none';
        }
    });

    function getLocation() {
        const statusEl = document.getElementById('geo-status');
        const lieuInput = document.getElementById('lieu');
        const spinIcon = document.getElementById('geo-spin-icon');
        
        lieuInput.value = 'Détection de votre position GPS...';
        lieuInput.readOnly = true;
        if (spinIcon) spinIcon.classList.add('fa-spin');
        statusEl.innerHTML = '<span style="color:#2563eb; font-size:0.75rem;"><i class="fa-solid fa-spinner fa-spin"></i> Localisation GPS en cours...</span>';

        if (!navigator.geolocation) {
            statusEl.innerHTML = '<span style="color:#B9123C; font-size:0.75rem;">Géolocalisation non supportée par votre navigateur. Saisissez le lieu manuellement.</span>';
            lieuInput.value = '';
            lieuInput.readOnly = false;
            if (spinIcon) spinIcon.classList.remove('fa-spin');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                const lat = pos.coords.latitude;
                const lon = pos.coords.longitude;
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lon;

                const reverseUrl = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lat + '&lon=' + lon + '&accept-language=fr';

                fetch(reverseUrl, { headers: { 'Accept': 'application/json' } })
                    .then(res => res.json())
                    .then(data => {
                        const placeName = data?.display_name || ('GPS : ' + lat.toFixed(5) + ', ' + lon.toFixed(5));
                        lieuInput.value = placeName;
                        lieuInput.readOnly = false;
                        if (spinIcon) spinIcon.classList.remove('fa-spin');
                        statusEl.innerHTML = '<span style="color:#059669; font-size:0.75rem;"><i class="fa-solid fa-circle-check"></i> Position GPS acquise avec succès</span>';
                        updateSummary();
                    })
                    .catch(() => {
                        lieuInput.value = 'GPS : ' + lat.toFixed(5) + ', ' + lon.toFixed(5);
                        lieuInput.readOnly = false;
                        if (spinIcon) spinIcon.classList.remove('fa-spin');
                        statusEl.innerHTML = '<span style="color:#059669; font-size:0.75rem;"><i class="fa-solid fa-circle-check"></i> Coordonnées GPS capturées</span>';
                        updateSummary();
                    });
            },
            function() {
                statusEl.innerHTML = '<span style="color:#B9123C; font-size:0.75rem;">Impossible de vous localiser. Veuillez saisir l\'adresse manuellement.</span>';
                lieuInput.value = '';
                lieuInput.readOnly = false;
                if (spinIcon) spinIcon.classList.remove('fa-spin');
            },
            { timeout: 10000, enableHighAccuracy: true }
        );
    }

    function updatePhotoLabel(input) {
        const count = input.files.length;
        const zone = document.getElementById('photo-drop-zone');
        const grid = document.getElementById('photos-preview-grid');
        
        document.getElementById('photo-drop-text').textContent = count > 0 ? count + ' photo(s) sélectionnée(s)' : 'Cliquez ou glissez au moins 1 photo ici';
        zone.style.borderColor = count > 0 ? '#B9123C' : '#cbd5e1';
        zone.style.background = count > 0 ? '#fff1f2' : '#f8fafc';

        // Génération des aperçus d'images
        grid.innerHTML = '';
        if (count > 0) {
            grid.style.display = 'grid';
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const card = document.createElement('div');
                    card.className = 'thumb-preview-card';
                    card.innerHTML = '<img src="' + e.target.result + '" class="thumb-preview-img" alt="Aperçu">';
                    grid.appendChild(card);
                };
                reader.readAsDataURL(file);
            });
        } else {
            grid.style.display = 'none';
        }

        updateSummary();
    }

    const dropZone = document.getElementById('photo-drop-zone');
    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.style.borderColor = '#B9123C'; });
    dropZone.addEventListener('dragleave', () => { dropZone.style.borderColor = '#cbd5e1'; });
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        document.getElementById('photos').files = e.dataTransfer.files;
        updatePhotoLabel(document.getElementById('photos'));
    });

    document.getElementById('alerte-form').addEventListener('submit', function(e) {
        const type = document.getElementById('type_sinistre').value;
        const lieu = document.getElementById('lieu').value.trim();
        const photos = document.getElementById('photos').files.length;
        const contact = document.getElementById('declarant_contact').value.trim();

        if (!type || !lieu || !photos || !contact) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Informations manquantes',
                text: 'Veuillez vérifier tous les champs obligatoires (Type, Lieu, Photo, Numéro de téléphone).',
                confirmButtonColor: '#B9123C'
            });
            return;
        }

        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Transmission en cours...';
    });

    document.addEventListener('DOMContentLoaded', function() {
        const oldType = '{{ old("type_sinistre") }}';
        if (oldType) {
            const btn = document.querySelector('#type-' + oldType.replace(/\s+/g, '-').toLowerCase().replace(/[^a-z0-9-]/g, ''));
            if (btn) btn.classList.add('selected');
            document.getElementById('type_sinistre').value = oldType;
        }
        
        // Déclenchement automatique immédiat de la géolocalisation dès l'ouverture de la page
        getLocation();
        updateSummary();
    });
</script>
@endpush
