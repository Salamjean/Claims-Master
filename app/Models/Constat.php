<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Constat extends Model
{
    protected $fillable = [
        'sinistre_id',
        'service_id',
        'type_constat',
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
    ];

    protected $casts = [
        'date_heure'          => 'datetime',
        'photos_plus'         => 'array',
        'terrain_valide'      => 'boolean',
        'redaction_validee'   => 'boolean',
        'redaction_validee_at'=> 'datetime',
        'recupere_par_assure' => 'boolean',
        'recupere_at'         => 'datetime',
        'date_livraison'      => 'date',
        'montant_timbres'     => 'integer',
        'montant_livraison'   => 'integer',
        'montant_a_payer'     => 'integer',
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
}
