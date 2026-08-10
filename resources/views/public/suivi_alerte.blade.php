@extends('layouts.app')

@section('title', 'Suivi de l\'Alerte ' . $sinistre->numero_sinistre . ' — Claims Master')
@section('description', 'Suivi en temps réel de l\'intervention d\'urgence des Sapeurs-Pompiers')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body, input, button, select, textarea { font-family: 'Inter', sans-serif; }

    .suivi-page-light {
        min-height: 100vh;
        background: #f8fafc;
        background-image: 
            radial-gradient(circle at 10% 5%, rgba(185, 18, 60, 0.03) 0%, transparent 40%),
            radial-gradient(circle at 90% 95%, rgba(37, 99, 235, 0.03) 0%, transparent 45%),
            linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 2.5rem 1.5rem 4rem;
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }

    .suivi-wrapper {
        width: 100%;
        max-width: 1100px;
    }

    /* Top Navigation Header */
    .top-nav-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: all 0.2s ease;
    }

    .btn-back-home:hover {
        color: #0f172a;
        border-color: #cbd5e1;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        transform: translateY(-1px);
    }

    .live-timer-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: #0284c7;
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        padding: 8px 18px;
        border-radius: 999px;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.06);
    }

    /* Hero Banner Header Card */
    .hero-banner-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 1.75rem 2rem;
        box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.05);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .status-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        padding: 7px 18px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .status-badge-pill.status-en_attente { background: #fffbe6; border: 1px solid #fde68a; color: #b45309; }
    .status-badge-pill.status-ambulance_en_route { background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; }
    .status-badge-pill.status-arrive { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; }
    .status-badge-pill.status-termine { background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; }

    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
        animation: pulseLight 1.4s ease-in-out infinite;
    }

    @keyframes pulseLight {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.4); opacity: 0.4; }
    }

    .ref-title-group h1 {
        font-size: 1.75rem;
        font-weight: 900;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .ref-title-group h1 span { color: #B9123C; }

    .ref-meta-sub {
        font-size: 0.8125rem;
        color: #64748b;
        margin-top: 2px;
    }

    .token-copy-box {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 8px 14px;
        border-radius: 12px;
        font-family: monospace;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .token-copy-box:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #0f172a;
    }

    /* Split 2-Column Layout Grid */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1.25fr 1fr;
        gap: 2rem;
        align-items: start;
    }

    /* Cards */
    .panel-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 28px;
        padding: 2rem;
        box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.05);
        margin-bottom: 2rem;
    }

    .panel-card:last-child {
        margin-bottom: 0;
    }

    .panel-title {
        font-size: 0.875rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #475569;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Live Summary Box */
    .summary-card {
        padding: 1.5rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        border: 1.5px solid transparent;
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
    }

    .summary-card.box-en_attente { background: #fffbe6; border-color: #fde68a; color: #92400e; }
    .summary-card.box-ambulance_en_route { background: #fff1f2; border-color: #fecdd3; color: #9f1239; }
    .summary-card.box-arrive { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }
    .summary-card.box-termine { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }

    .summary-icon {
        font-size: 2rem;
        line-height: 1;
        flex-shrink: 0;
    }

    .summary-title { font-size: 1.125rem; font-weight: 900; margin-bottom: 0.25rem; }
    .summary-desc { font-size: 0.84375rem; opacity: 0.9; line-height: 1.5; }

    /* Timeline Stepper */
    .stepper-list {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .step-row {
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
        position: relative;
        padding-bottom: 1.75rem;
    }

    .step-row:last-child { padding-bottom: 0; }

    .step-row:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 21px;
        top: 44px;
        width: 2px;
        height: calc(100% - 24px);
        background: #e2e8f0;
        z-index: 1;
    }

    .step-row.completed:not(:last-child)::after { background: #10b981; }
    .step-row.active:not(:last-child)::after { background: linear-gradient(180deg, #2563eb 0%, #e2e8f0 100%); }

    .step-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
        flex-shrink: 0;
        position: relative;
        z-index: 2;
        transition: all 0.3s ease;
    }

    .step-row.completed .step-circle {
        background: #10b981;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }

    .step-row.active .step-circle {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 0 0 6px rgba(37, 99, 235, 0.15), 0 4px 14px rgba(37, 99, 235, 0.3);
        animation: ringPulse 2s infinite;
    }

    @keyframes ringPulse {
        0%, 100% { box-shadow: 0 0 0 6px rgba(37, 99, 235, 0.15), 0 4px 14px rgba(37, 99, 235, 0.3); }
        50% { box-shadow: 0 0 0 10px rgba(37, 99, 235, 0.08), 0 6px 18px rgba(37, 99, 235, 0.4); }
    }

    .step-row.pending .step-circle {
        background: #ffffff;
        border: 2px solid #cbd5e1;
        color: #94a3b8;
    }

    .step-body { flex: 1; padding-top: 2px; }

    .step-heading {
        font-size: 0.9375rem;
        font-weight: 800;
        color: #94a3b8;
        margin-bottom: 2px;
    }

    .step-row.completed .step-heading { color: #0f172a; }
    .step-row.active .step-heading { color: #1d4ed8; font-size: 1rem; }

    .step-text { font-size: 0.8125rem; color: #64748b; line-height: 1.5; }
    .step-row.completed .step-text { color: #475569; }
    .step-row.active .step-text { color: #334155; font-weight: 500; }

    .step-timestamp {
        font-size: 0.75rem;
        font-weight: 700;
        color: #059669;
        margin-top: 4px;
        display: inline-block;
    }

    /* Details List (Right Column) */
    .detail-item {
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .detail-item:last-child { border-bottom: none; padding-bottom: 0; }
    .detail-item:first-child { padding-top: 0; }

    .detail-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
        flex-shrink: 0;
    }

    .icon-type { background: #fff1f2; color: #be123c; border: 1px solid #ffe4e6; }
    .icon-loc { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
    .icon-sp { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
    .icon-user { background: #faf5ff; color: #9333ea; border: 1px solid #f3e8ff; }

    .detail-content { flex: 1; min-width: 0; }
    .detail-lbl { font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; margin-bottom: 2px; }
    .detail-val { font-size: 0.9375rem; font-weight: 800; color: #0f172a; word-break: break-word; }
    .detail-sub { font-size: 0.78125rem; color: #64748b; margin-top: 2px; }

    .detail-link {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: #2563eb;
        text-decoration: none;
        margin-top: 4px;
    }

    .detail-link:hover { text-decoration: underline; }

    /* Action Buttons Stack */
    .action-stack {
        display: flex;
        flex-direction: column;
        gap: 0.875rem;
    }

    .btn-light-secondary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.625rem;
        padding: 14px 20px;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        color: #334155;
        font-size: 0.875rem;
        font-weight: 700;
        border-radius: 16px;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        cursor: pointer;
        width: 100%;
    }

    .btn-light-secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
        transform: translateY(-1px);
    }

    .btn-light-danger {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.625rem;
        padding: 14px 20px;
        background: #fff1f2;
        border: 1.5px solid #fecdd3;
        color: #be123c;
        font-size: 0.875rem;
        font-weight: 800;
        border-radius: 16px;
        text-decoration: none;
        transition: all 0.2s ease;
        width: 100%;
    }

    .btn-light-danger:hover {
        background: #ffe4e6;
        border-color: #fda4af;
        transform: translateY(-1px);
    }

    .btn-primary-crimson {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.625rem;
        padding: 16px 24px;
        background: linear-gradient(135deg, #B9123C 0%, #881337 100%);
        border: none;
        color: #ffffff;
        font-size: 0.9375rem;
        font-weight: 800;
        border-radius: 16px;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 8px 20px rgba(185, 18, 60, 0.25);
        width: 100%;
    }

    .btn-primary-crimson:hover {
        background: linear-gradient(135deg, #dc2626 0%, #B9123C 100%);
        box-shadow: 0 12px 28px rgba(185, 18, 60, 0.35);
        transform: translateY(-1px);
    }

    /* Photo thumbnails */
    .photos-flex {
        display: flex;
        gap: 0.625rem;
        flex-wrap: wrap;
        margin-top: 0.75rem;
    }

    .photo-thumb {
        width: 75px;
        height: 60px;
        border-radius: 12px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }

    .photo-thumb:hover { transform: scale(1.06); border-color: #B9123C; }

    /* Responsive */
    @media (max-width: 900px) {
        .dashboard-grid { grid-template-columns: 1fr; gap: 1.5rem; }
        .hero-banner-card { flex-direction: column; text-align: center; }
    }
</style>
@endpush

@section('content')
@php
    $status = $sinistre->hospital_status ?? 'en_attente';
    $steps = [
        'en_attente'           => 0,
        'ambulance_en_route'   => 1,
        'arrive'               => 2,
        'termine'              => 3,
    ];
    $currentStep = $steps[$status] ?? 0;
@endphp

<div class="suivi-page-light">
    <div class="suivi-wrapper">

        <!-- Barre de navigation supérieure -->
        <div class="top-nav-bar">
            <a href="{{ route('home') }}" class="btn-back-home">
                <i class="fa-solid fa-arrow-left"></i> Accueil Claims Master
            </a>

            <div class="live-timer-chip">
                <i class="fa-solid fa-arrows-rotate fa-spin" style="animation-duration: 4s;"></i>
                <span>En direct • Actualisation <span id="timer-sec">15</span>s</span>
            </div>
        </div>

        <!-- En-tête / Hero Banner -->
        <div class="hero-banner-card">
            <div class="ref-title-group">
                <div class="status-badge-pill status-{{ $status }}" style="margin-bottom: 0.625rem;">
                    <span class="pulse-dot"></span>
                    @if($status === 'en_attente')
                        Alerte Transmise • Attente Prise en Charge
                    @elseif($status === 'ambulance_en_route')
                        Secours Dépêchés • Unité en Route
                    @elseif($status === 'arrive')
                        Sur les Lieux • Intervention en Cours
                    @elseif($status === 'termine')
                        Intervention Clôturée avec Succès
                    @endif
                </div>

                <h1>Urgence <span>{{ $sinistre->numero_sinistre }}</span></h1>
                <div class="ref-meta-sub">
                    Signalez le {{ $sinistre->date_declaration?->format('d/m/Y à H:i') ?? $sinistre->created_at->format('d/m/Y à H:i') }}
                </div>
            </div>

            <div>
                <div class="token-copy-box" onclick="copyToken('{{ $token }}')" title="Cliquer pour copier le code de suivi">
                    <i class="fa-solid fa-key text-slate-400"></i> Code: {{ substr($token, 0, 8) }}...
                    <i class="fa-regular fa-copy text-slate-400"></i>
                </div>
            </div>
        </div>

        <!-- Disposition 2 Colonnes (Split Dashboard) -->
        <div class="dashboard-grid">

            <!-- COLONNE GAUCHE (Progression & Statut Live) -->
            <div>
                <!-- Carte Résumé de Statut -->
                <div class="summary-card box-{{ $status }}">
                    <div class="summary-icon">
                        @if($status === 'termine') ✅ @elseif($status === 'arrive') 🚒 @elseif($status === 'ambulance_en_route') 🚨 @else ⏳ @endif
                    </div>
                    <div>
                        <div class="summary-title">
                            @if($status === 'termine')
                                Intervention Clôturée
                            @elseif($status === 'arrive')
                                Équipe Sapeurs-Pompiers sur Place
                            @elseif($status === 'ambulance_en_route')
                                Secours en Route vers le Sinistre
                            @else
                                Alerte Reçue — Affectation en Cours
                            @endif
                        </div>
                        <div class="summary-desc">
                            @if($status === 'termine')
                                La situation a été totalement prise en charge et sécurisée par les secours.
                            @elseif($status === 'arrive')
                                Les Sapeurs-Pompiers procèdent aux opérations d'assistance et de secours.
                            @elseif($status === 'ambulance_en_route')
                                Les véhicules d'urgence font route vers la localisation indiquée.
                            @else
                                Votre déclaration est transmise aux Sapeurs-Pompiers pour mobilisation immédiate.
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Timeline / Stepper des étapes -->
                <div class="panel-card">
                    <div class="panel-title">
                        <i class="fa-solid fa-clock-rotate-left text-slate-400"></i> Étapes de l'intervention
                    </div>

                    <div class="stepper-list">

                        <!-- Étape 1 : Réception -->
                        <div class="step-row {{ $currentStep >= 0 ? ($currentStep === 0 ? 'active' : 'completed') : 'pending' }}">
                            <div class="step-circle"><i class="fa-solid fa-bell"></i></div>
                            <div class="step-body">
                                <div class="step-heading">1. Alerte enregistrée & transmise</div>
                                <div class="step-text">L'urgence a été transmise instantanément au poste de commandement.</div>
                                <div class="step-timestamp"><i class="fa-regular fa-clock"></i> Reçu à {{ $sinistre->created_at->format('H:i') }}</div>
                            </div>
                        </div>

                        <!-- Étape 2 : Unité en route -->
                        <div class="step-row {{ $currentStep >= 1 ? ($currentStep === 1 ? 'active' : 'completed') : 'pending' }}">
                            <div class="step-circle"><i class="fa-solid fa-truck-medical"></i></div>
                            <div class="step-body">
                                <div class="step-heading">2. Départ des Sapeurs-Pompiers</div>
                                <div class="step-text">Une unité de secours est mobilisée et fait route vers le lieu d'urgence.</div>
                                @if($sinistre->hospital_dispatched_at)
                                    <div class="step-timestamp"><i class="fa-regular fa-clock"></i> Départ à {{ $sinistre->hospital_dispatched_at->format('H:i') }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Étape 3 : Arrivée sur place -->
                        <div class="step-row {{ $currentStep >= 2 ? ($currentStep === 2 ? 'active' : 'completed') : 'pending' }}">
                            <div class="step-circle"><i class="fa-solid fa-location-crosshairs"></i></div>
                            <div class="step-body">
                                <div class="step-heading">3. Prise en charge sur le terrain</div>
                                <div class="step-text">Les secours sont sur place et procèdent aux soins et sécurisation.</div>
                                @if($sinistre->hospital_arrived_at)
                                    <div class="step-timestamp"><i class="fa-regular fa-clock"></i> Arrivée à {{ $sinistre->hospital_arrived_at->format('H:i') }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Étape 4 : Clôture -->
                        <div class="step-row {{ $currentStep >= 3 ? 'completed' : 'pending' }}">
                            <div class="step-circle"><i class="fa-solid fa-flag-checkered"></i></div>
                            <div class="step-body">
                                <div class="step-heading">4. Fin d'intervention & Procès-verbal</div>
                                <div class="step-text">L'intervention est terminée. L'état des lieux officiel est validé.</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- COLONNE DROITE (Détails de l'Urgence & Actions) -->
            <div>
                <!-- Carte Informations Clés -->
                <div class="panel-card">
                    <div class="panel-title">
                        <i class="fa-solid fa-circle-info text-slate-400"></i> Détails du signalement
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon icon-type">
                            @if(str_contains(strtolower($sinistre->type_sinistre), 'incendie') || str_contains(strtolower($sinistre->type_sinistre), 'feu')) 🔥 @elseif(str_contains(strtolower($sinistre->type_sinistre), 'malaise')) 🩺 @elseif(str_contains(strtolower($sinistre->type_sinistre), 'sauvetage')) 🛟 @else 🚗 @endif
                        </div>
                        <div class="detail-content">
                            <div class="detail-lbl">Nature de l'incident</div>
                            <div class="detail-val">{{ $sinistre->type_sinistre }}</div>
                            <div class="detail-sub">{{ Str::limit($sinistre->description, 60) }}</div>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon icon-loc"><i class="fa-solid fa-location-dot"></i></div>
                        <div class="detail-content">
                            <div class="detail-lbl">Lieu exact</div>
                            <div class="detail-val">{{ $sinistre->lieu }}</div>
                            @if($sinistre->latitude && $sinistre->longitude)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $sinistre->latitude }},{{ $sinistre->longitude }}" target="_blank" class="detail-link">
                                    <i class="fa-solid fa-map-location-dot"></i> Ouvrir sur Google Maps
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon icon-sp"><i class="fa-solid fa-shield-halved"></i></div>
                        <div class="detail-content">
                            <div class="detail-lbl">Caserne mobilisée</div>
                            <div class="detail-val">{{ $sinistre->nearestHospital->name ?? 'GSPM / Sapeurs-Pompiers' }}</div>
                            <div class="detail-sub">{{ $sinistre->nearestHospital->commune ?? 'Secours territoriaux' }}</div>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon icon-user"><i class="fa-solid fa-user-shield"></i></div>
                        <div class="detail-content">
                            <div class="detail-lbl">Signalé par</div>
                            <div class="detail-val">{{ $sinistre->declarant_nom ?: 'Passant Anonyme' }}</div>
                            <div class="detail-sub">{{ $sinistre->declarant_contact ?: 'Sans coordonnées directes' }}</div>
                        </div>
                    </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyToken(token) {
        navigator.clipboard.writeText(token).then(() => {
            alert("Code de suivi copié dans le presse-papier !");
        });
    }

    let countdown = 15;
    const timerEl = document.getElementById('timer-sec');

    setInterval(() => {
        countdown--;
        if (timerEl) timerEl.textContent = countdown;
        if (countdown <= 0) {
            window.location.reload();
        }
    }, 1000);
</script>
@endpush
