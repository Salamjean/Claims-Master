@extends('layouts.app')

@section('title', 'Contact — Claims Master')
@section('description', 'Contactez l\'équipe Claims Master pour toute question sur la plateforme de gestion des sinistres.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <style>
        /* ── PAGE CONTACT ── */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 60px;
            align-items: start;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .contact-info-card {
            display: flex;
            gap: 16px;
            padding: 22px;
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: 16px;
            transition: box-shadow .3s, transform .3s;
        }

        .contact-info-card:hover {
            box-shadow: 0 8px 32px rgba(37, 99, 235, .08);
            transform: translateY(-2px);
        }

        .contact-info-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .contact-info-card h4 {
            font-size: 15px;
            font-weight: 700;
            color: var(--slate-800);
            margin-bottom: 4px;
        }

        .contact-info-card p {
            font-size: 14px;
            color: var(--slate-500);
            line-height: 1.5;
        }

        .contact-info-card a {
            color: var(--blue);
            text-decoration: none;
        }

        /* ── FORMULAIRE ── */
        .contact-form {
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .04);
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--slate-700);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid var(--slate-200);
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: var(--slate-800);
            transition: border-color .2s, box-shadow .2s;
            outline: none;
            background: var(--slate-50);
        }

        .form-control:focus {
            border-color: #213685;
            box-shadow: 0 0 0 3px rgba(33, 54, 133, .1);
            background: #fff;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 140px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #213685;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: background .2s, transform .2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 16px rgba(33, 54, 133, .3);
        }

        .btn-submit:hover {
            background: #1a2b6b;
            transform: translateY(-1px);
        }

        @media (max-width: 900px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')

    {{-- ══════ HERO ══════ --}}
    <div class="page-hero">
        <div class="page-hero-content">
            <div class="carousel-tag"><i class="fa-solid fa-envelope"></i> Contact</div>
            <h1>Parlons de votre <span>sinistre</span></h1>
            <p>Notre équipe est disponible pour répondre à toutes vos questions sur la plateforme Claims Master.</p>
        </div>
    </div>

    {{-- ══════ CONTACT SECTION ══════ --}}
    <section style="background:#fff;">
        <div class="contact-grid">

            {{-- Informations de contact --}}
            <div>
                <div class="section-tag" style="margin-bottom:24px;"><i class="fa-solid fa-address-book"></i> Nos
                    coordonnées</div>
                <h2 class="section-title" style="margin-bottom:12px;">Contactez-nous</h2>
                <p style="font-size:15px;color:var(--slate-500);line-height:1.7;margin-bottom:36px;">Notre équipe est à
                    votre disposition du lundi au vendredi, de 8h à 18h.</p>

                <div class="contact-info">
                    <div class="contact-info-card fade-in">
                        <div class="contact-info-icon" style="background:rgba(33, 54, 133, 0.1);color:#213685;"><i
                                class="fa-solid fa-envelope"></i></div>
                        <div>
                            <h4>Email</h4>
                            <p><a href="mailto:contact@claimsmaster.ci">contact@claimsmaster.ci</a></p>
                        </div>
                    </div>
                    <div class="contact-info-card fade-in">
                        <div class="contact-info-icon" style="background:rgba(122, 170, 37, 0.1);color:#7aaa25;"><i
                                class="fa-solid fa-phone"></i></div>
                        <div>
                            <h4>Téléphone</h4>
                            <p><a href="tel:+22500000000">+225 00 00 00 00</a></p>
                        </div>
                    </div>
                    <div class="contact-info-card fade-in">
                        <div class="contact-info-icon" style="background:rgba(33, 54, 133, 0.15);color:#213685;"><i
                                class="fa-solid fa-location-dot"></i></div>
                        <div>
                            <h4>Adresse</h4>
                            <p>Abidjan, Plateau<br>Côte d'Ivoire</p>
                        </div>
                    </div>
                    <div class="contact-info-card fade-in">
                        <div class="contact-info-icon" style="background:rgba(122, 170, 37, 0.15);color:#7aaa25;"><i
                                class="fa-solid fa-clock"></i></div>
                        <div>
                            <h4>Horaires d'ouverture</h4>
                            <p>Lundi – Vendredi : 8h00 – 18h00<br>Samedi : 9h00 – 13h00</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formulaire de contact --}}
            <div class="contact-form fade-in">
                <h3 style="font-size:22px;font-weight:800;color:var(--slate-900);margin-bottom:6px;">Envoyez-nous un message
                </h3>
                <p style="font-size:14px;color:var(--slate-500);margin-bottom:28px;">Nous vous répondrons dans les plus
                    brefs délais.</p>

                <form action="#" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom</label>
                            <input type="text" id="nom" name="nom" class="form-control" placeholder="Votre nom" required>
                        </div>
                        <div class="form-group">
                            <label for="prenom">Prénom</label>
                            <input type="text" id="prenom" name="prenom" class="form-control" placeholder="Votre prénom"
                                required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="votremail@exemple.ci"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="sujet">Sujet</label>
                        <select id="sujet" name="sujet" class="form-control">
                            <option value="">Sélectionner un sujet</option>
                            <option value="sinistre">Question sur un sinistre</option>
                            <option value="compte">Problème de compte</option>
                            <option value="technique">Problème technique</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" class="form-control" placeholder="Décrivez votre demande..."
                            required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-paper-plane"></i> Envoyer le message
                    </button>
                </form>
            </div>

        </div>
    </section>

@endsection