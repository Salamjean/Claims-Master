{{-- ══════════ HERO CAROUSEL PREMIUM ══════════ --}}
<div class="carousel" id="hero-carousel">
    <div class="carousel-track" id="carousel-track">

        {{-- Slide 1 — Déclaration --}}
        <div class="carousel-slide active">
            <div class="carousel-bg-wrap">
                <img src="{{ asset('assets/images/declara.png') }}" alt="Sinistre automobile" class="carousel-bg">
            </div>
            <div class="carousel-content">
                <div class="carousel-tag animate-item stagger-1">
                    <i class="fa-solid fa-shield-halved"></i> Plateforme officielle
                </div>
                <h1 class="animate-item stagger-2">Déclarez votre sinistre en quelques minutes</h1>
                <p class="animate-item stagger-3">Claims Master digitalise la gestion des sinistres. Soumettez votre
                    déclaration en ligne, suivez son traitement en temps réel.</p>
                <div class="carousel-actions animate-item stagger-4">
                    <a href="{{ route('assure.register.form') }}" class="carousel-btn-primary">
                        <i class="fa-solid fa-file-circle-plus"></i> Déclarer un sinistre
                    </a>
                    <a href="#comment" class="carousel-btn-ghost">
                        <i class="fa-solid fa-play"></i> Comment ça marche
                    </a>
                </div>
            </div>
        </div>

        {{-- Slide 2 — Sécurité --}}
        <div class="carousel-slide">
            <div class="carousel-bg-wrap">
                <img src="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=1600&q=80"
                    alt="Sécurité des données" class="carousel-bg">
            </div>
            <div class="carousel-content">
                <div class="carousel-tag animate-item stagger-1"
                    style="background:rgba(5,150,105,.25);border-color:rgba(5,150,105,.5);color:#6ee7b7;">
                    <i class="fa-solid fa-lock"></i> 100% sécurisé
                </div>
                <h1 class="animate-item stagger-2">Vos données <span style="color:#34d399;">protégées</span> et
                    confidentielles</h1>
                <p class="animate-item stagger-3">Nos systèmes de sécurité avancés garantissent la confidentialité de
                    vos informations et la protection de vos données personnelles.</p>
                <div class="carousel-actions animate-item stagger-4">
                    <a href="{{ route('home.securite') }}" class="carousel-btn-primary"
                        style="background:#059669;box-shadow:0 8px 30px rgba(5,150,105,.4);">
                        <i class="fa-solid fa-shield-check"></i> Découvrir la sécurité
                    </a>
                    <a href="{{ route('login') }}" class="carousel-btn-ghost">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Se connecter
                    </a>
                </div>
            </div>
        </div>

        {{-- Slide 3 — Multi-services --}}
        <div class="carousel-slide">
            <div class="carousel-bg-wrap">
                <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1600&q=80"
                    alt="Coordination entre services" class="carousel-bg">
            </div>
            <div class="carousel-content">
                <div class="carousel-tag animate-item stagger-1"
                    style="background:rgba(124,58,237,.25);border-color:rgba(124,58,237,.5);color:#c4b5fd;">
                    <i class="fa-solid fa-network-wired"></i> Multi-services
                </div>
                <h1 class="animate-item stagger-2">Police, Gendarmerie & <span style="color:#a78bfa;">Assurances</span>
                    connectés</h1>
                <p class="animate-item stagger-3">Une coordination parfaite entre tous les acteurs impliqués dans le
                    traitement de votre sinistre pour une résolution rapide.</p>
                <div class="carousel-actions animate-item stagger-4">
                    <a href="{{ route('home.services') }}" class="carousel-btn-primary"
                        style="background:#7c3aed;box-shadow:0 8px 30px rgba(124,58,237,.4);">
                        <i class="fa-solid fa-diagram-project"></i> Voir les services
                    </a>
                    <a href="{{ route('assure.register.form') }}" class="carousel-btn-ghost">
                        <i class="fa-solid fa-user-plus"></i> Créer un compte
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- Contrôles --}}
    <button class="carousel-prev" onclick="prevSlide()" aria-label="Précédent">
        <i class="fa-solid fa-chevron-left"></i>
    </button>
    <button class="carousel-next" onclick="nextSlide()" aria-label="Suivant">
        <i class="fa-solid fa-chevron-right"></i>
    </button>

    {{-- Indicateurs de progression --}}
    <div class="carousel-indicators" role="tablist">
        <div class="carousel-indicator active" onclick="goToSlide(0)" role="tab">
            <div class="progress-bar"></div>
        </div>
        <div class="carousel-indicator" onclick="goToSlide(1)" role="tab">
            <div class="progress-bar"></div>
        </div>
        <div class="carousel-indicator" onclick="goToSlide(2)" role="tab">
            <div class="progress-bar"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const track = document.getElementById('carousel-track');
            const slides = document.querySelectorAll('.carousel-slide');
            const indicators = document.querySelectorAll('.carousel-indicator');
            const carousel = document.getElementById('hero-carousel');

            const total = slides.length;
            let current = 0;
            let autoplayInterval;
            const slideDuration = 4500; // 4.5 secondes par slide
            let isPaused = false;

            function updateCarousel(resetProgress = true) {
                // Remove active classes
                slides.forEach(s => s.classList.remove('active'));
                indicators.forEach(i => {
                    i.classList.remove('active');
                    if (resetProgress) {
                        const bar = i.querySelector('.progress-bar');
                        // Hack pour redémarrer l'animation CSS
                        bar.style.animation = 'none';
                        bar.offsetHeight; /* trigger reflow */
                        bar.style.animation = null;
                    }
                });

                // Set new active classes
                track.style.transform = `translateX(-${current * 100}%)`;
                slides[current].classList.add('active');
                indicators[current].classList.add('active');

                if (resetProgress) startAutoplay(); // Restart the timer
            }

            window.nextSlide = () => { current = (current + 1) % total; updateCarousel(); };
            window.prevSlide = () => { current = (current - 1 + total) % total; updateCarousel(); };
            window.goToSlide = (n) => {
                if (current === n) return;
                current = n;
                updateCarousel();
            };

            function startAutoplay() {
                clearInterval(autoplayInterval);
                if (!isPaused) {
                    const activeBar = indicators[current].querySelector('.progress-bar');
                    if (activeBar) activeBar.style.animationPlayState = 'running';
                    autoplayInterval = setInterval(window.nextSlide, slideDuration);
                }
            }

            function pauseAutoplay() {
                isPaused = true;
                clearInterval(autoplayInterval);
                const activeBar = indicators[current].querySelector('.progress-bar');
                if (activeBar) activeBar.style.animationPlayState = 'paused';
            }

            function resumeAutoplay() {
                isPaused = false;
                startAutoplay();
            }

            // Pause au hover
            carousel.addEventListener('mouseenter', pauseAutoplay);
            carousel.addEventListener('mouseleave', resumeAutoplay);

            // Init
            updateCarousel();
        });
    </script>
@endpush