<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Constat extends Model
{
    protected $fillable = [
        'sinistre_id',
        'service_id',
        'hospital_id',
        'type_constat',
        'methode_redaction',
        'agent_nom',
        'agent_grade',
        'agent_matricule',
        'lieu',
        'date_heure',
        'description_faits',
        'dommages',
        'observations',
        'temoins',
        'croquis',
        'ass1_photo',
        'ass2_photo',
        'photos_plus',
        'terrain_valide',
        'redaction_contenu',
        'redaction_pdf',
        'redaction_validee',
        'redaction_validee_at',
        'recupere_par_assure',
        'recupere_at',
        'mode_retrait',
        'nom_destinataire',
        'prenom_destinataire',
        'email_destinataire',
        'telephone_destinataire',
        'adresse_livraison',
        'ville_livraison',
        'commune_livraison',
        'quartier_livraison',
        'date_livraison',
        'heure_livraison',
        'montant_timbres',
        'montant_livraison',
        'statut_paiement',
        'montant_a_payer',
        'wave_session_id',
        'agent_unlocked',
        'agent_unlocked_at',
        'agent_unlocked_by',

        // Véhicule A
        'veh_a_marque', 'veh_a_type', 'veh_a_etat_general', 'veh_a_pneumatiques',
        'veh_a_conducteur_nom', 'veh_a_conducteur_date_naissance', 'veh_a_conducteur_lieu_naissance',
        'veh_a_conducteur_pere', 'veh_a_conducteur_mere', 'veh_a_conducteur_nationalite', 'veh_a_conducteur_tel',
        'veh_a_conducteur_profession', 'veh_a_conducteur_domicile',
        'veh_a_permis_numero', 'veh_a_permis_date', 'veh_a_permis_lieu', 'veh_a_permis_categories', 'veh_a_permis_validite',
        'veh_a_proprietaire_nom', 'veh_a_proprietaire_bp', 'veh_a_proprietaire_tel',
        'veh_a_assurance_nom', 'veh_a_assurance_debut', 'veh_a_assurance_fin',
        'veh_a_police_numero', 'veh_a_attestation_numero', 'veh_a_degats_materiels',

        // Véhicule B
        'veh_b_marque', 'veh_b_type', 'veh_b_etat_general', 'veh_b_pneumatiques',
        'veh_b_conducteur_nom', 'veh_b_conducteur_date_naissance', 'veh_b_conducteur_lieu_naissance',
        'veh_b_conducteur_pere', 'veh_b_conducteur_mere', 'veh_b_conducteur_nationalite', 'veh_b_conducteur_tel',
        'veh_b_conducteur_profession', 'veh_b_conducteur_domicile',
        'veh_b_permis_numero', 'veh_b_permis_date', 'veh_b_permis_lieu', 'veh_b_permis_categories', 'veh_b_permis_validite',
        'veh_b_proprietaire_nom', 'veh_b_proprietaire_bp', 'veh_b_proprietaire_tel',
        'veh_b_assurance_nom', 'veh_b_assurance_debut', 'veh_b_assurance_fin',
        'veh_b_police_numero', 'veh_b_attestation_numero', 'veh_b_degats_materiels',

        // Victime
        'victime_nom', 'victime_date_naissance', 'victime_lieu_naissance', 'victime_nationalite',
        'victime_pere', 'victime_mere', 'victime_profession', 'victime_domicile',
        'victime_blessures', 'victime_passager_vehicule',
    ];

    protected $casts = [
        'date_heure'          => 'datetime',
        'photos_plus'         => 'array',
        'terrain_valide'      => 'boolean',
        'redaction_validee'   => 'boolean',
        'redaction_validee_at' => 'datetime',
        'recupere_par_assure' => 'boolean',
        'recupere_at'         => 'datetime',
        'date_livraison'      => 'date',
        'montant_timbres'     => 'integer',
        'montant_livraison'   => 'integer',
        'montant_a_payer'     => 'integer',
        'agent_unlocked'      => 'boolean',
        'agent_unlocked_at'   => 'datetime',

        'veh_a_conducteur_date_naissance' => 'date',
        'veh_a_permis_date'               => 'date',
        'veh_a_permis_validite'           => 'date',
        'veh_a_assurance_debut'           => 'date',
        'veh_a_assurance_fin'             => 'date',

        'veh_b_conducteur_date_naissance' => 'date',
        'veh_b_permis_date'               => 'date',
        'veh_b_permis_validite'           => 'date',
        'veh_b_assurance_debut'           => 'date',
        'veh_b_assurance_fin'             => 'date',

        'victime_date_naissance'          => 'date',
    ];

    public function sinistre()
    {
        return $this->belongsTo(Sinistre::class);
    }

    public function service()
    {
        return $this->belongsTo(User::class, 'service_id');
    }

    public function payments()
    {
        return $this->hasMany(ConstatPayment::class);
    }

    public function hospital()
    {
        return $this->belongsTo(User::class, 'hospital_id');
    }
}
