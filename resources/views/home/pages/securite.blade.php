@extends('layouts.app')

@section('title', 'Sécurité & Confidentialité — Claims Master')
@section('description', 'Claims Master garantit la sécurité et la confidentialité de vos données personnelles grâce à des technologies avancées.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')

    {{-- ══════ HERO ══════ --}}
    {{-- ══════ HERO ══════ --}}
    <div class="page-hero">
        <div class="page-hero-content">
            <div class="carousel-tag"><i class="fa-solid fa-lock"></i> Sécurité & Confidentialité</div>
            <h1>Vos données <span>protégées</span> et confidentielles</h1>
            <p>Claims Master utilise les technologies de sécurité les plus avancées pour garantir la confidentialité et
                l'intégrité de vos informations personnelles.</p>
        </div>
    </div>

    {{-- ══════ SECURITE SECTION ══════ --}}
    <section id="securite">
        <div class="security-grid">
            <div class="security-image fade-in">
                <img src="https://images.unsplash.com/photo-1563986768494-4dee2763ff3f?w=900&q=80"
                    alt="Sécurité des données">
                <div class="badge-overlay">
                    <div
                        style="width:44px;height:44px;border-radius:12px;background:rgba(122, 170, 37, 0.1);display:flex;align-items:center;justify-content:center;color:#7aaa25;font-size:20px;">
                        <i class="fa-solid fa-shield-check"></i>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:700;color:#1e293b;">Données chiffrées</div>
                        <div style="font-size:12px;color:#64748b;">Selon les standards internationaux</div>
                    </div>
                    <div style="margin-left:auto;color:#7aaa25;font-size:20px;"><i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
            </div>
            <div>
                <div class="section-tag"><i class="fa-solid fa-shield-halved"></i> Nos engagements</div>
                <h2 class="section-title">4 piliers de sécurité</h2>
                <p class="section-sub" style="margin-bottom:32px;">Chaque aspect de notre plateforme a été conçu avec la
                    sécurité comme priorité absolue.</p>
                <div class="security-features">
                    <div class="feature-row fade-in">
                        <div class="feature-icon-wrap" style="background:rgba(33, 54, 133, 0.1);color:#213685;"><i
                                class="fa-solid fa-key"></i></div>
                        <div>
                            <h4>Authentification sécurisée</h4>
                            <p>Accès protégé par des codes d'accès uniques et une gestion des mots de passe renforcée.</p>
                        </div>
                    </div>
                    <div class="feature-row fade-in">
                        <div class="feature-icon-wrap" style="background:rgba(122, 170, 37, 0.1);color:#7aaa25;"><i
                                class="fa-solid fa-eye-slash"></i></div>
                        <div>
                            <h4>Confidentialité garantie</h4>
                            <p>Chaque espace est cloisonné — un assuré ne peut voir que ses propres dossiers.</p>
                        </div>
                    </div>
                    <div class="feature-row fade-in">
                        <div class="feature-icon-wrap" style="background:rgba(33, 54, 133, 0.15);color:#213685;"><i
                                class="fa-solid fa-server"></i></div>
                        <div>
                            <h4>Stockage fiable</h4>
                            <p>Vos documents et photos sont stockés de manière sécurisée et pérenne.</p>
                        </div>
                    </div>
                    <div class="feature-row fade-in">
                        <div class="feature-icon-wrap" style="background:rgba(122, 170, 37, 0.15);color:#7aaa25;"><i
                                class="fa-solid fa-rotate"></i></div>
                        <div>
                            <h4>Traçabilité complète</h4>
                            <p>Chaque action sur un dossier est enregistrée pour garantir une transparence totale.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════ GARANTIES ══════ --}}
    <section style="background:var(--slate-50);">
        <div class="text-center" style="margin-bottom:50px;">
            <div class="section-tag"><i class="fa-solid fa-certificate"></i> Garanties</div>
            <h2 class="section-title">Ce que nous garantissons</h2>
        </div>
        <div class="services-grid" style="margin-top:0;">
            <div class="service-card fade-in">
                <div class="service-icon" style="background:rgba(33, 54, 133, 0.1);color:#213685;"><i
                        class="fa-solid fa-user-lock"></i>
                </div>
                <h3>Accès restreint</h3>
                <p>Seul le propriétaire d'un dossier et les autorités compétentes peuvent y accéder. Aucune fuite possible.
                </p>
            </div>
            <div class="service-card fade-in">
                <div class="service-icon" style="background:rgba(122, 170, 37, 0.1);color:#7aaa25;"><i
                        class="fa-solid fa-database"></i>
                </div>
                <h3>Données chiffrées</h3>
                <p>Toutes vos données sont chiffrées en transit et au repos selon les normes de sécurité internationales.
                </p>
            </div>
            <div class="service-card fade-in">
                <div class="service-icon" style="background:rgba(33, 54, 133, 0.15);color:#213685;"><i
                        class="fa-solid fa-clock-rotate-left"></i></div>
                <h3>Sauvegardes régulières</h3>
                <p>Vos dossiers sont sauvegardés automatiquement pour éviter toute perte de données.</p>
            </div>
        </div>
    </section>

    {{-- ══════ CTA ══════ --}}
    <div class="cta-modern">
        <div style="position:relative;z-index:1;">
            <h2>Faites confiance à Claims Master</h2>
            <p>Votre sécurité est notre priorité. Rejoignez des milliers d'utilisateurs qui nous font confiance.</p>
            <div class="cta-btns">
                <a href="{{ route('assure.register.form') }}" class="btn-primary"><i class="fa-solid fa-user-plus"></i>
                    Créer un compte</a>
                <a href="{{ route('home.contact') }}" class="btn-outline"
                    style="border-color: rgba(255,255,255,0.2); color: #213685;"><i class="fa-solid fa-envelope"></i> Nous
                    contacter</a>
            </div>
        </div>
    </div>

@endsection