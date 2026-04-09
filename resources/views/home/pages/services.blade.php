@extends('layouts.app')

@section('title', 'Nos Services — Claims Master')
@section('description', 'Découvrez tous les espaces de la plateforme Claims Master : Assuré, Assurance, Police, Gendarmerie et Administration.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')

    {{-- ══════ HERO ══════ --}}
    <div class="page-hero">
        <div class="page-hero-content">
            <div class="carousel-tag"><i class="fa-solid fa-grip"></i> Nos services</div>
            <h1>Une solution pour <span>chaque intervenant</span></h1>
            <p>Claims Master centralise la gestion des sinistres et coordonne tous les acteurs concernés dans un seul espace
                digital.</p>
        </div>
    </div>

    {{-- ══════ SERVICES GRID ══════ --}}
    <section id="services" style="background:#fff;">
        <div class="services-grid" style="margin-top:0;">
            <div class="service-card fade-in">
                <div class="service-icon" style="background:rgba(33, 54, 133, 0.1);color:#213685;"><i
                        class="fa-solid fa-user-shield"></i>
                </div>
                <h3>Espace Assuré</h3>
                <p>Déclarez vos sinistres en ligne avec géolocalisation et photos, suivez votre dossier en temps réel et
                    recevez des notifications à chaque étape.</p>
            </div>
            <div class="service-card fade-in">
                <div class="service-icon" style="background:rgba(122, 170, 37, 0.1);color:#7aaa25;"><i
                        class="fa-solid fa-building-shield"></i></div>
                <h3>Espace Assurance</h3>
                <p>Gérez les dossiers de vos clients, accédez aux constats établis par la Police et la Gendarmerie, et
                    prenez des décisions éclairées.</p>
            </div>
            <div class="service-card fade-in">
                <div class="service-icon" style="background:rgba(33, 54, 133, 0.15);color:#213685;"><i
                        class="fa-solid fa-shield-halved"></i></div>
                <h3>Espace Police</h3>
                <p>Recevez les déclarations assignées à votre commissariat, rédigez les constats d'accident et clôturez les
                    dossiers efficacement.</p>
            </div>
            <div class="service-card fade-in">
                <div class="service-icon" style="background:rgba(33, 54, 133, 0.15);color:#213685;"><i
                        class="fa-solid fa-user-group"></i>
                </div>
                <h3>Espace Gendarmerie</h3>
                <p>Traitez les sinistres de votre zone de compétence, établissez les procès-verbaux et coordonnez avec les
                    services d'assurance.</p>
            </div>
            <div class="service-card fade-in">
                <div class="service-icon" style="background:rgba(122, 170, 37, 0.15);color:#7aaa25;"><i
                        class="fa-solid fa-chart-pie"></i>
                </div>
                <h3>Tableau de bord Admin</h3>
                <p>Supervision globale de l'activité, gestion des utilisateurs et attribution des sinistres aux services
                    compétents.</p>
            </div>
            <div class="service-card fade-in">
                <div class="service-icon" style="background:rgba(33, 54, 133, 0.08);color:#213685;"><i
                        class="fa-solid fa-bell"></i></div>
                <h3>Notifications SMS</h3>
                <p>Alertes SMS automatiques pour informer les assurés à chaque évolution de leur dossier, sans action
                    supplémentaire.</p>
            </div>
        </div>
    </section>

    {{-- ══════ CTA ══════ --}}
    <div class="cta-modern">
        <div style="position:relative;z-index:1;">
            <h2>Prêt à commencer ?</h2>
            <p>Créez votre compte assuré et déclarez votre premier sinistre en quelques minutes.</p>
            <div class="cta-btns">
                <a href="{{ route('assure.register.form') }}" class="btn-primary"><i class="fa-solid fa-user-plus"></i>
                    S'inscrire</a>
                <a href="{{ route('login') }}" class="btn-outline"
                    style="border-color: rgba(255,255,255,0.2); color: #213685;"><i
                        class="fa-solid fa-arrow-right-to-bracket"></i> Se connecter</a>
            </div>
        </div>
    </div>

@endsection