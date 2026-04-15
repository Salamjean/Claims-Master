<?php

use App\Http\Controllers\Admin\AdminDashboard;
use App\Http\Controllers\Admin\AssuranceController;
use App\Http\Controllers\Admin\ServiceConstatController;
use App\Http\Controllers\Assurance\AssuranceDashboard;
use App\Http\Controllers\Assurance\AssureController;
use App\Http\Controllers\Assurance\DocumentRequisController;
use App\Http\Controllers\Assure\AssureDashboardController;
use App\Http\Controllers\Assure\ContratController;
use App\Http\Controllers\Assure\SinistreController;
use App\Http\Controllers\Assure\SinistreDocumentController;
use App\Http\Controllers\Assurance\ExpertController;
use App\Http\Controllers\Assurance\GarageController;
use App\Http\Controllers\Assurance\PersonnelController;
use App\Http\Controllers\Assurance\SinistreController as AssuranceSinistreController;
use App\Http\Controllers\Agent\AgentDashboardController;
use App\Http\Controllers\Gendarmerie\GendarmerieController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Police\PoliceController;
use App\Http\Controllers\Service\AgentController;
use App\Http\Controllers\User\RegisterAssureController;
use App\Http\Controllers\User\UserAuthenticate;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/nos-services', [HomeController::class, 'services'])->name('home.services');
Route::get('/comment-ca-marche', [HomeController::class, 'comment'])->name('home.comment');
Route::get('/securite', [HomeController::class, 'securite'])->name('home.securite');
Route::get('/contact', [HomeController::class, 'contact'])->name('home.contact');


//Les routes d'authentification
Route::prefix('/')->group(function () {
    // Inscription Assuré
    Route::get('/inscription', [RegisterAssureController::class, 'showRegistrationForm'])->name('assure.register.form');
    Route::post('/inscription', [RegisterAssureController::class, 'register'])->name('assure.register.submit');

    // Connexion Assuré
    Route::get('/login', [UserAuthenticate::class, 'showAssureLogin'])->name('login');
    Route::post('/login', [UserAuthenticate::class, 'handleAssureLogin'])->name('user.login');

    // Mot de passe oublié
    Route::get('/forgot-password', [UserAuthenticate::class, 'forgotPassword'])->name('password.forgot');
    Route::post('/forgot-password', [UserAuthenticate::class, 'handleForgotPassword'])->name('password.email');
    Route::get('/reset-password/{email}', [UserAuthenticate::class, 'resetPassword'])->name('password.reset');
    Route::post('/reset-password', [UserAuthenticate::class, 'handleResetPassword'])->name('password.update');

    // Activation du compte (lien email)
    Route::get('/validate-user-account/{email}', [UserAuthenticate::class, 'defineAccess'])->name('account.define');
    Route::post('/validate-user-account', [UserAuthenticate::class, 'submitDefineAccess'])->name('account.submit');
});

// Les routes d'authentification PROFESSIONNELLE (Portail)
Route::prefix('portail')->group(function () {
    Route::get('/login', [UserAuthenticate::class, 'showPortalLogin'])->name('portal.login');
    Route::get('/password/forgot', [UserAuthenticate::class, 'showPortalForgotPassword'])->name('portal.password.forgot');
    Route::post('/password/forgot', [UserAuthenticate::class, 'handlePortalForgotPassword'])->name('portal.password.submit');
    Route::get('/password/reset/{email}', [UserAuthenticate::class, 'resetPassword'])->name('portal.password.reset');
    Route::post('/password/update', [UserAuthenticate::class, 'handleResetPassword'])->name('portal.password.update');
    Route::post('/login', [UserAuthenticate::class, 'handlePortalLogin'])->name('portal.login.submit');
});

//Les routes de gestion de l'administrateur
Route::middleware(['auth:user', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'dashboard'])->name('admin.dashboard');
    Route::match(['get', 'post'], '/logout', [AdminDashboard::class, 'logout'])->name('admin.logout');

    // Gestion des assurances
    Route::get('/assurances', [AssuranceController::class, 'index'])->name('admin.assurances.index');
    Route::get('/assurances/create', [AssuranceController::class, 'create'])->name('admin.assurances.create');
    Route::post('/assurances', [AssuranceController::class, 'store'])->name('admin.assurances.store');
    Route::get('/assurances/{user}', [AssuranceController::class, 'show'])->name('admin.assurances.show');
    Route::delete('/assurances/{user}', [AssuranceController::class, 'destroy'])->name('admin.assurances.destroy');

    // Gestion des Services de Constats (Police / Gendarmerie)
    Route::get('/services', [ServiceConstatController::class, 'index'])->name('admin.services.index');
    Route::get('/services/create', [ServiceConstatController::class, 'create'])->name('admin.services.create');
    Route::post('/services', [ServiceConstatController::class, 'store'])->name('admin.services.store');
});

//Les routes de gestion de l'assurance
Route::middleware(['auth:user', 'assurance'])->prefix('assurance')->group(function () {
    Route::get('/dashboard', [AssuranceDashboard::class, 'dashboard'])->name('assurance.dashboard');
    Route::match(['get', 'post'], '/logout', [AssuranceDashboard::class, 'logout'])->name('assurance.logout');

    // Gestion des assurés
    Route::get('/assures', [AssureController::class, 'index'])->name('assurance.assures.index');
    Route::get('/assures/create', [AssureController::class, 'create'])->name('assurance.assures.create');
    Route::post('/assures', [AssureController::class, 'store'])->name('assurance.assures.store');
    Route::get('/assures/{user}', [AssureController::class, 'show'])->name('assurance.assures.show');
    Route::delete('/assures/{user}', [AssureController::class, 'destroy'])->name('assurance.assures.destroy');

    // Gestion des Experts
    Route::resource('experts', ExpertController::class)->names('assurance.experts');

    // Gestion des Garages
    Route::resource('garages', GarageController::class)->names('assurance.garages');
    Route::delete('/assures/{user}', [AssureController::class, 'destroy'])->name('assurance.assures.destroy');

    // Gestion du Personnel
    Route::get('/personnel', [PersonnelController::class, 'index'])->name('assurance.personnel.index');
    Route::get('/personnel/ajouter', [PersonnelController::class, 'create'])->name('assurance.personnel.create');
    Route::post('/personnel', [PersonnelController::class, 'store'])->name('assurance.personnel.store');
    Route::delete('/personnel/{personnel}', [PersonnelController::class, 'destroy'])->name('assurance.personnel.destroy');

    // Gestion des documents requis
    Route::get('/documents-requis', [DocumentRequisController::class, 'index'])->name('assurance.documents-requis.index');
    Route::get('/documents-requis/{type_sinistre}', [DocumentRequisController::class, 'show'])->name('assurance.documents-requis.show');
    Route::post('/documents-requis/{type_sinistre}', [DocumentRequisController::class, 'update'])->name('assurance.documents-requis.update');

    // Gestion de l'expertise et des sinistres (Review IA)
    Route::get('/sinistres', [AssuranceSinistreController::class, 'index'])->name('assurance.sinistres.index');
    Route::get('/recherche', [AssuranceSinistreController::class, 'search'])->name('assurance.search');
    Route::get('/sinistres/{sinistre}', [AssuranceSinistreController::class, 'show'])->name('assurance.sinistres.show');
    Route::post('/sinistres/{sinistre}/review-doc/{documentAttendu}', [AssuranceSinistreController::class, 'reviewDoc'])->name('assurance.sinistres.review-doc');
    Route::post('/sinistres/{sinistre}/decision', [AssuranceSinistreController::class, 'decision'])->name('assurance.sinistres.decision');

    // Nouveaux endpoints pour le workflow défini dans le cahier des charges
    Route::post('/sinistres/{sinistre}/verify-garanties', [AssuranceSinistreController::class, 'verifyGaranties'])->name('assurance.sinistres.verify_garanties');
    Route::post('/sinistres/{sinistre}/assign-expert-garage', [AssuranceSinistreController::class, 'assignExpertGarage'])->name('assurance.sinistres.assign_expert_garage');

    // Endpoints pour la génération de PDF ou vues imprimables
    Route::get('/sinistres/{sinistre}/pdf/dossier', [AssuranceSinistreController::class, 'pdfDossier'])->name('assurance.sinistres.pdf.dossier');
    Route::get('/sinistres/{sinistre}/pdf/prise-en-charge', [AssuranceSinistreController::class, 'pdfPriseEnCharge'])->name('assurance.sinistres.pdf.prise_en_charge');
    Route::get('/sinistres/{sinistre}/pdf/bon-sortie', [AssuranceSinistreController::class, 'pdfBonSortie'])->name('assurance.sinistres.pdf.bon_sortie');

    // Profil Assurance
    Route::get('/profil', [AssuranceDashboard::class, 'profile'])->name('assurance.profile');
    Route::post('/profil', [AssuranceDashboard::class, 'updateProfile'])->name('assurance.profile.update');
    Route::get('/changer-mot-de-passe', [AssuranceDashboard::class, 'showChangePassword'])->name('assurance.password.change');
    Route::post('/changer-mot-de-passe', [AssuranceDashboard::class, 'updatePassword'])->name('assurance.password.update');
});
// Les routes de l'espace assuré
Route::middleware(['auth:user', 'assure'])->prefix('mon-espace')->group(function () {
    // Changement de mot de passe (accessible même si must_change_password = true)
    Route::get('/changer-mot-de-passe', [AssureDashboardController::class, 'showChangePassword'])->name('assure.password.change');
    Route::post('/changer-mot-de-passe', [AssureDashboardController::class, 'updatePassword'])->name('assure.password.update');
    Route::post('/logout', [AssureDashboardController::class, 'logout'])->name('assure.logout');

    // Dashboard et autres routes (protégées par force.password)
    Route::middleware(['force.password'])->group(function () {
        Route::get('/dashboard', [AssureDashboardController::class, 'dashboard'])->name('assure.dashboard');
        Route::get('/profil', [AssureDashboardController::class, 'profile'])->name('assure.profile');
        Route::post('/profil', [AssureDashboardController::class, 'updateProfile'])->name('assure.profile.update');

        // Mes Assurances
        Route::get('/mes-assurances', [ContratController::class, 'index'])->name('assure.contrats.index');
        Route::get('/mes-assurances/ajouter', [ContratController::class, 'create'])->name('assure.contrats.create');
        Route::post('/mes-assurances/ajouter', [ContratController::class, 'store'])->name('assure.contrats.store');
        Route::delete('/mes-assurances/{contrat}', [ContratController::class, 'destroy'])->name('assure.contrats.destroy');

        // Déclaration de sinistre
        Route::get('/sinistres/declarer', [SinistreController::class, 'create'])->name('assure.sinistres.create');
        Route::get('/sinistres/services-proches', [SinistreController::class, 'getNearestServices'])->name('assure.sinistres.services-proches');
        Route::post('/sinistres/declarer', [SinistreController::class, 'store'])->name('assure.sinistres.store');
        Route::get('/sinistres/en-attente', [SinistreController::class, 'enAttente'])->name('assure.sinistres.en_attente');
        Route::get('/sinistres/en-cours', [SinistreController::class, 'enCours'])->name('assure.sinistres.en_cours');
        Route::get('/sinistres/historique', [SinistreController::class, 'historique'])->name('assure.sinistres.historique');
        // Liste globale des documents en attente
        Route::get('/sinistres/documents-requis', [SinistreController::class, 'documentsRequis'])->name('assure.sinistres.documents');

        // Upload des documents requis via IA
        Route::get('/sinistres/{sinistre}/upload-docs', [SinistreDocumentController::class, 'index'])->name('assure.sinistres.upload-docs');
        Route::post('/sinistres/upload-doc/{documentAttendu}', [SinistreDocumentController::class, 'upload'])->name('assure.sinistres.upload-doc');

        Route::get('/sinistres/{sinistre}', [SinistreController::class, 'show'])->name('assure.sinistres.show');
        Route::get('/sinistres/{sinistre}/constat/download', [SinistreController::class, 'downloadConstat'])->name('assure.sinistres.constat.download');
        Route::get('/sinistres/{sinistre}/suivi', [SinistreController::class, 'tracking'])->name('assure.sinistres.tracking');
        Route::get('/sinistres/{sinistre}/agent-location', [AgentDashboardController::class, 'getAgentLocation'])->name('assure.sinistres.agent_location');
        Route::delete('/sinistres/{sinistre}', [SinistreController::class, 'destroy'])->name('assure.sinistres.destroy');

        // Nouveaux : Constats Prêts & Paiement/Livraison
        Route::get('/constats-prets', [SinistreController::class, 'constatsPrets'])->name('assure.constats.prets');
        Route::get('/sinistres/{sinistre}/paiement', [SinistreController::class, 'showPaiementRetrait'])->name('assure.constats.paiement');
        Route::post('/sinistres/{sinistre}/paiement', [SinistreController::class, 'processPaiementRetrait'])->name('assure.constats.paiement.store');

        // Support 24/7
        Route::get('/support', [AssureDashboardController::class, 'support'])->name('assure.support');
    });
});

// Les routes de l'espace Personnel (employés assurance)
use App\Http\Controllers\Personnel\PersonnelDashboardController;

Route::middleware(['auth:user', 'personnel'])->prefix('personnel')->group(function () {
    Route::post('/logout', [PersonnelDashboardController::class, 'logout'])->name('personnel.logout');
    Route::middleware(['force.password'])->group(function () {
        Route::get('/dashboard', [PersonnelDashboardController::class, 'dashboard'])->name('personnel.dashboard');

        // Dossiers sinistres
        Route::get('/sinistres', [PersonnelDashboardController::class, 'sinistres'])->name('personnel.sinistres.index');
        Route::get('/mes-dossiers', [PersonnelDashboardController::class, 'mesDossiers'])->name('personnel.mes-dossiers');
        Route::get('/recherche', [PersonnelDashboardController::class, 'search'])->name('personnel.search');
        Route::get('/sinistres/{sinistre}', [PersonnelDashboardController::class, 'showSinistre'])->name('personnel.sinistres.show');
        Route::get('/sinistres/{sinistre}/examiner', [PersonnelDashboardController::class, 'reviewSinistre'])->name('personnel.sinistres.review');
        Route::post('/sinistres/{sinistre}/review-doc/{documentAttendu}', [PersonnelDashboardController::class, 'reviewDoc'])->name('personnel.sinistres.review-doc');
        Route::post('/sinistres/{sinistre}/verify-garanties', [PersonnelDashboardController::class, 'verifyGaranties'])->name('personnel.sinistres.verify_garanties');
        Route::post('/sinistres/{sinistre}/assign-expert-garage', [PersonnelDashboardController::class, 'assignExpertGarage'])->name('personnel.sinistres.assign_expert_garage');
        Route::post('/sinistres/{sinistre}/decision', [PersonnelDashboardController::class, 'decision'])->name('personnel.sinistres.decision');
        Route::post('/sinistres/{sinistre}/claim', [PersonnelDashboardController::class, 'claim'])->name('personnel.sinistres.claim');
        Route::post('/sinistres/{sinistre}/release', [PersonnelDashboardController::class, 'release'])->name('personnel.sinistres.release');

        // Profil & mot de passe
        Route::get('/profil', [PersonnelDashboardController::class, 'profile'])->name('personnel.profile');
        Route::post('/profil', [PersonnelDashboardController::class, 'updateProfile'])->name('personnel.profile.update');
        Route::get('/changer-mot-de-passe', [PersonnelDashboardController::class, 'showChangePassword'])->name('personnel.password.change');
        Route::post('/changer-mot-de-passe', [PersonnelDashboardController::class, 'updatePassword'])->name('personnel.password.update');
    });
});

// Les routes de l'espace Police
Route::middleware(['auth:user', 'police'])->prefix('police')->group(function () {
    Route::middleware(['force.password'])->group(function () {
        Route::get('/dashboard', [PoliceController::class, 'dashboard'])->name('police.dashboard');
        Route::get('/sinistres/json', [PoliceController::class, 'sinistresJson'])->name('police.sinistres.json');
        Route::get('/sinistres/en-attente', [PoliceController::class, 'enAttente'])->name('police.sinistres.en_attente');
        Route::get('/sinistres/historique', [PoliceController::class, 'historique'])->name('police.sinistres.historique');
        Route::get('/sinistres/{sinistre}/constat', [PoliceController::class, 'createConstat'])->name('police.sinistres.constat.create');
        Route::post('/sinistres/{sinistre}/constat', [PoliceController::class, 'storeConstat'])->name('police.sinistres.constat.store');
        Route::get('/sinistres/{sinistre}/constat/details', [PoliceController::class, 'showConstat'])->name('police.sinistres.constat.show');
        Route::get('/sinistres/{sinistre}/details', [PoliceController::class, 'showSinistre'])->name('police.sinistres.show');

        // Gestion des Agents
        Route::get('/agents', [AgentController::class, 'index'])->name('police.agents.index');
        Route::get('/agents/create', [AgentController::class, 'create'])->name('police.agents.create');
        Route::post('/agents', [AgentController::class, 'store'])->name('police.agents.store');
        Route::delete('/agents/{agent}', [AgentController::class, 'destroy'])->name('police.agents.destroy');

        // Profil Police
        Route::get('/profil', [PoliceController::class, 'profile'])->name('police.profile');
        Route::post('/profil', [PoliceController::class, 'updateProfile'])->name('police.profile.update');

        // Portefeuille Police
        Route::get('/portefeuille', [PoliceController::class, 'wallet'])->name('police.wallet');
    });
    Route::post('/logout', [PoliceController::class, 'logout'])->name('police.logout');
});

// Les routes de l'espace Gendarmerie
Route::middleware(['auth:user', 'gendarmerie'])->prefix('gendarmerie')->group(function () {
    Route::middleware(['force.password'])->group(function () {
        Route::get('/dashboard', [GendarmerieController::class, 'dashboard'])->name('gendarmerie.dashboard');
        Route::get('/sinistres/json', [GendarmerieController::class, 'sinistresJson'])->name('gendarmerie.sinistres.json');
        Route::get('/sinistres/en-attente', [GendarmerieController::class, 'enAttente'])->name('gendarmerie.sinistres.en_attente');
        Route::get('/sinistres/historique', [GendarmerieController::class, 'historique'])->name('gendarmerie.sinistres.historique');
        Route::get('/sinistres/{sinistre}/constat', [GendarmerieController::class, 'createConstat'])->name('gendarmerie.sinistres.constat.create');
        Route::post('/sinistres/{sinistre}/constat', [GendarmerieController::class, 'storeConstat'])->name('gendarmerie.sinistres.constat.store');
        Route::get('/sinistres/{sinistre}/constat/details', [GendarmerieController::class, 'showConstat'])->name('gendarmerie.sinistres.constat.show');
        Route::get('/sinistres/{sinistre}/details', [GendarmerieController::class, 'showSinistre'])->name('gendarmerie.sinistres.show');

        // Gestion des Agents
        Route::get('/agents', [AgentController::class, 'index'])->name('gendarmerie.agents.index');
        Route::get('/agents/create', [AgentController::class, 'create'])->name('gendarmerie.agents.create');
        Route::post('/agents', [AgentController::class, 'store'])->name('gendarmerie.agents.store');
        Route::delete('/agents/{agent}', [AgentController::class, 'destroy'])->name('gendarmerie.agents.destroy');

        // Profil Gendarmerie
        Route::get('/profil', [GendarmerieController::class, 'profile'])->name('gendarmerie.profile');
        Route::post('/profil', [GendarmerieController::class, 'updateProfile'])->name('gendarmerie.profile.update');

        // Portefeuille Gendarmerie
        Route::get('/portefeuille', [GendarmerieController::class, 'wallet'])->name('gendarmerie.wallet');
    });
    Route::post('/logout', [GendarmerieController::class, 'logout'])->name('gendarmerie.logout');
});

// --------------------------------------------------------------------------
// ESPACE AGENT (REDACTION CONSTATS)
// --------------------------------------------------------------------------
Route::middleware(['auth:user', 'agent', 'force.password'])->prefix('agent')->group(function () {
    Route::get('/dashboard', [AgentDashboardController::class, 'dashboard'])->name('agent.dashboard');
    Route::get('/sinistres/json', [AgentDashboardController::class, 'sinistresJson'])->name('agent.sinistres.json');
    Route::get('/sinistres/en-attente', [AgentDashboardController::class, 'enAttente'])->name('agent.sinistres.en_attente');
    Route::get('/sinistres/mes-dossiers', [AgentDashboardController::class, 'mesDossiers'])->name('agent.sinistres.mes_dossiers');
    Route::get('/sinistres/historique', [AgentDashboardController::class, 'historique'])->name('agent.sinistres.historique');
    Route::post('/sinistres/{sinistre}/claim', [AgentDashboardController::class, 'claimSinistre'])->name('agent.sinistres.claim');
    Route::get('/sinistres/{sinistre}', [AgentDashboardController::class, 'showSinistre'])->name('agent.sinistres.show');
    Route::get('/sinistres/{sinistre}/constat/create', [AgentDashboardController::class, 'createConstat'])->name('agent.sinistres.constat.create');
    Route::post('/sinistres/{sinistre}/constat/store', [AgentDashboardController::class, 'storeConstat'])->name('agent.sinistres.constat.store');
    Route::get('/sinistres/{sinistre}/constat/show', [AgentDashboardController::class, 'showConstat'])->name('agent.sinistres.constat.show');
    Route::get('/sinistres/{sinistre}/redaction', [AgentDashboardController::class, 'showRedaction'])->name('agent.sinistres.redaction');
    Route::post('/sinistres/{sinistre}/redaction', [AgentDashboardController::class, 'storeRedaction'])->name('agent.sinistres.redaction.store');
    Route::get('/constats-rediges', [AgentDashboardController::class, 'constatsRediges'])->name('agent.constats.rediges');
    Route::post('/sinistres/{sinistre}/constat/mark-recovered', [AgentDashboardController::class, 'markAsRecovered'])->name('agent.constats.mark_recovered');
    Route::get('/portefeuille', [AgentDashboardController::class, 'wallet'])->name('agent.wallet');
    Route::post('/logout', [AgentDashboardController::class, 'logout'])->name('agent.logout');

    // Profil Agent
    Route::get('/profil', [AgentDashboardController::class, 'profile'])->name('agent.profile');
    Route::post('/profil', [AgentDashboardController::class, 'updateProfile'])->name('agent.profile.update');
});
// Webhook Wave (Public)
Route::post('/webhook/wave', [SinistreController::class, 'waveWebhook'])->name('webhook.wave');
