<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contrat extends Model
{
    protected $fillable = [
        'client_id',
        'assurance_id',
        'numero_contrat',
        'type_contrat',
        'date_debut',
        'date_fin',
        'prime',
        'statut',
        'document_pdf',
        'resume_ia',
        'garanties',
        'franchise',
        'prime_payee',
        'plaque',
        'marque',
        'modele',
        'type_vehicule',
        'immatriculation',
        'attestation_assurance',
        'attestation_ai_status',
        'attestation_ai_feedback',
        'carte_grise',
        'visite_technique',
        'permis_conduire',
        'nom_assureur',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'prime' => 'decimal:2',
        'franchise' => 'decimal:2',
        'garanties' => 'array',
        'prime_payee' => 'boolean',
    ];

    /**
     * Relation : un contrat appartient à un assuré (client)
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Relation : un contrat appartient à un assureur
     */
    public function assureur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assurance_id');
    }
}
