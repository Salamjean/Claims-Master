@extends('layouts.app')

@section('title', 'Claims Master — Gestion des sinistres moderne et intuitive')
@section('description', 'Plateforme digitale premium de gestion des déclarations de sinistres. Rapide, centralisée, sécurisée.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <!-- Load FontAwesome if not already loaded globally -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')

    {{-- ══════════ NEXT-GEN HERO SECTION ══════════ --}}
    <!-- Replaced pure CSS styling for a background image with a dark overlay using inline-style -->
    <section class="hero-modern"
        style="background-image: url('{{ asset('assets/images/declara.png') }}'); background-size: cover; background-position: center; background-attachment: fixed;">
        <div class="hero-content">
            <div class="hero-tagline">
                <i class="fa-solid fa-bolt"></i>
                Le futur de l'assurance est là
            </div>

            <h1>L'écosystème unifié pour <br><span>vos sinistres</span>.</h1>

            <p>
                Claims Master connecte instantanément assurés, compagnies d'assurance,
                et forces de l'ordre sur une plateforme unique et sécurisée.
                Fini la paperasse, place à l'efficacité.
            </p>

            <div class="hero-actions">
                <a href="{{ route('assure.register.form') }}" class="btn-modern-primary">
                    Commencer maintenant <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="#comment" class="btn-modern-outline">
                    Comment ça marche
                </a>
            </div>
        </div>
    </section>

    {{-- ══════════ GLASS STATS ══════════ --}}
    <div class="stats-floating-container">
        <div class="stats-glass-panel">
            <div class="stat-modern">
                <div class="num">24h</div>
                <div class="label">Disponibilité</div>
            </div>
            <div class="stat-modern">
                <div class="num">100%</div>
                <div class="label">Digitalisé</div>
            </div>
            <div class="stat-modern">
                <div class="num">3</div>
                <div class="label">Acteurs connectés</div>
            </div>
            <div class="stat-modern">
                <div class="num">Zéro</div>
                <div class="label">Papier</div>
            </div>
        </div>
    </div>

    {{-- ══════════ BENTO GRID SERVICES ══════════ --}}
    <section id="services" class="section-modern section-modern-bg">
        <div class="section-header">
            <div class="section-badge"><i class="fa-solid fa-layer-group"></i> Nos Espaces</div>
            <h2>Une interface dédiée <br>pour chaque intervenant</h2>
            <p>Une expérience sur mesure, sécurisée et optimisée pour accélérer les processus à tous les niveaux.</p>
        </div>

        <div class="services-grid-row">

            <!-- Card 1: Assuré -->
            <div class="service-card-compact">
                <div class="service-icon-compact" style="background: rgba(33, 54, 133, 0.1); color: #213685;">
                    <!-- Brand Blue -->
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <h3>Espace Assuré</h3>
                <p>Déclarez un sinistre depuis un smartphone, avec géolocalisation, upload photos sécurisé et suivi complet.
                </p>
                <a href="{{ route('assure.register.form') }}" class="service-link-compact" style="color: #213685;">Mon
                    compte <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <!-- Card 2: Assurance -->
            <div class="service-card-compact">
                <div class="service-icon-compact" style="background: rgba(122, 170, 37, 0.1); color: #7aaa25;">
                    <!-- Brand Green -->
                    <i class="fa-solid fa-building-shield"></i>
                </div>
                <h3>Espace Assurance</h3>
                <p>Tableau de bord centralisé pour analyser les constats validés et gérer rapidement les indemnisations.</p>
                <a href="{{ route('login') }}" class="service-link-compact" style="color: #7aaa25;">Accès Courtier <i
                        class="fa-solid fa-arrow-right"></i></a>
            </div>

            <!-- Card 3: Police & Gendarmerie -->
            <div class="service-card-compact">
                <div class="service-icon-compact" style="background: rgba(33, 54, 133, 0.15); color: #213685;">
                    <!-- Brand Blue -->
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3>Forces de l'Ordre</h3>
                <p>Rédaction de PV numériques infalsifiables et déploiement plus rapide sur les axes routiers majeurs.</p>
                <a href="{{ route('login') }}" class="service-link-compact" style="color: #213685;">Accès Légal <i
                        class="fa-solid fa-arrow-right"></i></a>
            </div>

            <!-- Card 4: Admin & IA -->
            <div class="service-card-compact">
                <div class="service-icon-compact" style="background: rgba(122, 170, 37, 0.15); color: #7aaa25;">
                    <!-- Brand Green -->
                    <i class="fa-solid fa-microchip"></i>
                </div>
                <h3>Analyse IA & Admin</h3>
                <p>Orchestration réseau et pré-analyse de la complétude documentaire par notre Intelligence Artificielle.
                </p>
                <a href="{{ route('login') }}" class="service-link-compact" style="color: #7aaa25;">Supervision <i
                        class="fa-solid fa-arrow-right"></i></a>
            </div>

        </div>
    </section>

    {{-- ══════════ PROCESS TIMELINE ══════════ --}}
    <section id="comment" class="section-modern">
        <div class="section-header">
            <div class="section-badge" style="background: rgba(122, 170, 37, 0.1); color: #7aaa25;">
                <i class="fa-solid fa-stopwatch"></i> Workflow
            </div>
            <h2>Fluide du début à la fin</h2>
        </div>

        <div class="process-container">
            <div class="process-step">
                <div class="step-icon-modern" style="color: #213685;">
                    <i class="fa-solid fa-mobile-screen"></i>
                    <div class="step-number">1</div>
                </div>
                <h4>Déclaration Initiale</h4>
                <p>L'assuré capture les preuves via son téléphone et soumet son dossier instantanément sur le portail.</p>
            </div>

            <div class="process-step">
                <div class="step-icon-modern" style="color: #7aaa25;">
                    <i class="fa-solid fa-file-signature"></i>
                    <div class="step-number">2</div>
                </div>
                <h4>Constat Officiel</h4>
                <p>La Police ou la Gendarmerie reçoit, intervient si nécessaire et rédige le procès-verbal numérique
                    certifié.</p>
            </div>

            <div class="process-step">
                <div class="step-icon-modern" style="color: #213685;">
                    <i class="fa-solid fa-brain"></i>
                    <div class="step-number">3</div>
                </div>
                <h4>Validation Claims AI</h4>
                <p>Claims AI vérifie la cohérence des documents soumis par l'assuré avant de transmettre à l'assurance.</p>
            </div>

            <div class="process-step">
                <div class="step-icon-modern" style="color: #7aaa25;">
                    <i class="fa-solid fa-check-double"></i>
                    <div class="step-number">4</div>
                </div>
                <h4>Indemnisation</h4>
                <p>L'assurance clôture le dossier avec toutes les pièces conformes, déclenchant le processus de paiement.
                </p>
            </div>
        </div>
    </section>

    {{-- ══════════ APPLE-STYLE SECURITY ══════════ --}}
    <section id="securite" class="section-modern">
        <div class="security-showcase">
            <div class="security-text">
                <h2>Conçu avec la <br>sécurité au cœur.</h2>
                <p>Dans le domaine des sinistres et des constats légaux, la confiance est absolue. Claims Master déploie une
                    architecture cryptographique garantissant la virginité et la traçabilité de chaque document.</p>

                <div class="security-list">
                    <div class="security-item">
                        <i class="fa-solid fa-shield-halved" style="color: #4f6bff;"></i>
                        <!-- Lighter blue for dark background visibility -->
                        <div>
                            <h4>Chiffrement de bout en bout</h4>
                            <p>Les données sensibles sont invisibles en transit et au repos.</p>
                        </div>
                    </div>
                    <div class="security-item">
                        <i class="fa-solid fa-eye-slash" style="color: #4f6bff;"></i>
                        <div>
                            <h4>Cloisonnement strict</h4>
                            <p>Architecture multi-tenant isolant rigoureusement les données de chaque acteur.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="security-visual">
                <div class="glass-card-dark">
                    <div class="security-badge-float float-1"
                        style="background: rgba(122, 170, 37, 0.15); border-color: rgba(122, 170, 37, 0.3); color: #a1d148;">
                        <i class="fa-solid fa-check-circle"></i> ISO 27001 Ready
                    </div>
                    <div class="security-badge-float float-2"
                        style="background: rgba(33, 54, 133, 0.3); border-color: rgba(33, 54, 133, 0.5); color: #7f99ff;">
                        <i class="fa-solid fa-lock"></i> AES-256
                    </div>

                    <!-- Stylized code/server representation -->
                    <div
                        style="background: #0f172a; border-radius: 16px; padding: 20px; font-family: monospace; color: #a1d148; font-size: 14px; line-height: 1.8;">
                        > INITIALIZING SECURE TUNNEL...<br>
                        > [OK] TLS 1.3 ESTABLISHED<br>
                        > VERIFYING IDENTITY TOKEN...<br>
                        > <span style="color: #7f99ff">[SUCCESS] ACCÈS AUTORISÉ</span><br>
                        > <span style="color: #94a3b8">UPLOADING ENCRYPTED PAYLOAD</span><br>
                        <div
                            style="height: 4px; background: rgba(255,255,255,0.1); margin-top: 10px; border-radius: 2px; overflow: hidden;">
                            <div style="height: 100%; width: 75%; background: #a1d148;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════ MODERN CTA BOTTOM ══════════ --}}
    <section class="section-modern" style="padding-top: 0;">
        <div class="cta-modern">
            <h2>Préparez l'avenir de votre réseau d'assurance</h2>
            <p>Rejoignez un écosystème moderne conçu pour éliminer les frictions administratives et accélérer la
                satisfaction client.</p>
            <div class="hero-actions">
                <a href="{{ route('assure.register.form') }}" class="btn-modern-primary">
                    Créer mon compte Assuré
                </a>
                <a href="{{ route('login') }}" class="btn-modern-outline" style="background: #fff; color: #213685;">
                    Accès Professionnels
                </a>
            </div>
        </div>
    </section>

@endsection