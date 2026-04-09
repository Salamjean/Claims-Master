{{-- ══════════ FOOTER ══════════ --}}
<footer>
    <div class="footer-grid">
        <div class="footer-brand">
            <div class="logo-text">Claims<span>Master</span></div>
            <p style="font-size:14px;line-height:1.7;margin-bottom:20px;">
                Plateforme digitale de gestion des sinistres connectant assurés,
                Police, Gendarmerie et compagnies d'assurance.
            </p>
            <div class="footer-socials">
                <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                <a href="#" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
            </div>
        </div>

        <div class="footer-col">
            <h5>Plateforme</h5>
            <a href="#services">Nos services</a>
            <a href="#comment">Comment ça marche</a>
            <a href="#securite">Sécurité</a>
        </div>

        <div class="footer-col">
            <h5>Accès</h5>
            <a href="{{ route('login') }}">Se connecter</a>
            <a href="{{ route('assure.register.form') }}">S'inscrire</a>
        </div>

        <div class="footer-col">
            <h5>Contact</h5>
            <a href="mailto:contact@claimsmaster.ci">contact@claimsmaster.ci</a>
            <a href="tel:+22500000000">+225 00 00 00 00</a>
            <a href="#">Abidjan, Côte d'Ivoire</a>
        </div>
    </div>

    <div class="footer-bottom">
        <span>© {{ date('Y') }} ClaimsMaster. Tous droits réservés.</span>
        <div style="display:flex;gap:20px;">
            <a href="#" style="color:rgba(255,255,255,.4);text-decoration:none;font-size:13px;">Confidentialité</a>
            <a href="#" style="color:rgba(255,255,255,.4);text-decoration:none;font-size:13px;">Conditions
                d'utilisation</a>
        </div>
    </div>
</footer>