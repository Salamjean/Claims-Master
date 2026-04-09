@extends('layouts.app')

@section('title', 'Comment ça marche — Claims Master')
@section('description', 'Découvrez le processus simplifié en 4 étapes pour déclarer et suivre votre sinistre sur Claims Master.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')

    {{-- ══════ HERO ══════ --}}
    {{-- ══════ HERO ══════ --}}
    <div class="page-hero">
        <div class="page-hero-content">
            <div class="carousel-tag"><i class="fa-solid fa-list-ol"></i> Processus</div>
            <h1>Comment ça <span>fonctionne ?</span></h1>
            <p>Un processus simple et transparent en 4 étapes pour déclarer votre sinistre et obtenir votre indemnisation
                rapidement.</p>
        </div>
    </div>

    {{-- ══════ STEPS SECTION ══════ --}}
    <section id="comment" style="background:#fff;">
        <div class="text-center">
            <div class="section-tag"><i class="fa-solid fa-route"></i> Étapes</div>
            <h2 class="section-title">4 étapes simples</h2>
            <p class="section-sub">Du sinistre à l'indemnisation, Claims Master gère toutes les étapes pour vous.</p>
        </div>
        <div class="steps-grid">
            <div class="step fade-in">
                <div class="step-num" style="background:rgba(33, 54, 133, 0.1); color:#213685;">1</div>
                <h4>Inscription</h4>
                <p>Créez votre compte en quelques minutes avec vos informations personnelles et vos données d'assurance.</p>
            </div>
            <div class="step fade-in">
                <div class="step-num" style="background:rgba(122, 170, 37, 0.1); color:#7aaa25;">2</div>
                <h4>Déclaration</h4>
                <p>Soumettez votre sinistre avec localisation GPS et photos directement depuis votre téléphone ou
                    ordinateur.</p>
            </div>
            <div class="step fade-in">
                <div class="step-num" style="background:rgba(33, 54, 133, 0.15); color:#213685;">3</div>
                <h4>Traitement</h4>
                <p>La Police ou Gendarmerie compétente établit le constat officiel et traite votre dossier rapidement.</p>
            </div>
            <div class="step fade-in">
                <div class="step-num" style="background:rgba(122, 170, 37, 0.15); color:#7aaa25;">4</div>
                <h4>Clôture</h4>
                <p>Votre assurance reçoit le constat et procède au traitement final de votre indemnisation.</p>
            </div>
        </div>
    </section>

    {{-- ══════ DÉTAILS ══════ --}}
    <section style="background:var(--slate-50);">
        <div class="text-center" style="margin-bottom:50px;">
            <div class="section-tag"><i class="fa-solid fa-circle-info"></i> En détail</div>
            <h2 class="section-title">Ce que nous faisons pour vous</h2>
        </div>
        <div class="services-grid" style="margin-top:0;">
            <div class="service-card fade-in">
                <div class="service-icon" style="background:rgba(33, 54, 133, 0.1);color:#213685;"><i
                        class="fa-solid fa-map-location-dot"></i></div>
                <h3>Géolocalisation automatique</h3>
                <p>La position GPS du sinistre est capturée automatiquement pour faciliter l'assignation au service
                    compétent.</p>
            </div>
            <div class="service-card fade-in">
                <div class="service-icon" style="background:rgba(122, 170, 37, 0.1);color:#7aaa25;"><i
                        class="fa-solid fa-camera"></i></div>
                <h3>Photos en temps réel</h3>
                <p>Téléchargez des photos du sinistre directement depuis votre appareil pour documenter les dégâts.</p>
            </div>
            <div class="service-card fade-in">
                <div class="service-icon" style="background:rgba(33, 54, 133, 0.15);color:#213685;"><i
                        class="fa-solid fa-bell"></i></div>
                <h3>Notifications automatiques</h3>
                <p>Recevez des alertes SMS et en ligne à chaque étape du traitement de votre dossier.</p>
            </div>
        </div>
    </section>

    {{-- ══════ CTA ══════ --}}
    <div class="cta-modern">
        <div style="position:relative;z-index:1;">
            <h2>Commencez maintenant</h2>
            <p>Inscrivez-vous gratuitement et déclarez votre sinistre en quelques minutes.</p>
            <div class="cta-btns">
                <a href="{{ route('assure.register.form') }}" class="btn-primary"><i class="fa-solid fa-user-plus"></i>
                    Créer un compte</a>
                <a href="{{ route('home.services') }}" class="btn-outline"
                    style="border-color: rgba(255,255,255,0.2); color: #213685;"><i class="fa-solid fa-grip"></i> Voir nos
                    services</a>
            </div>
        </div>
    </div>

@endsection