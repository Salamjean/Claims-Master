<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtatDesLieux extends Model
{
    protected $table = 'etat_des_lieux';

    protected $fillable = [
        'sinistre_id',
        'groupe_id',

        // 1. Informations générales
        'numero_intervention',
        'date_heure_alerte',
        'heure_depart_caserne',
        'heure_arrivee_lieux',
        'heure_fin_intervention',
        'lieu_exact',

        // 2. Nature de l'intervention
        'nature_intervention',

        // 3. Informations sur le sinistre
        'description_situation',
        'cause_presumee',
        'niveau_gravite',
        'risques_identifies',
        'conditions_meteo',

        // 4. Victimes
        'victimes',

        // 5. Véhicules impliqués
        'vehicules_impliques',

        // 6. Dégâts matériels
        'biens_endommages',
        'batiments_touches',
        'surface_brulee',
        'estimation_degats',
        'biens_sauves',

        // 7. Moyens engagés
        'casernes_mobilisees',
        'vehicules_utilises',
        'nombre_pompiers',
        'materiel_utilise',
        'quantite_eau_utilisee',
        'produits_extincteurs_utilises',

        // 8. Actions réalisées
        'actions_realisees',

        // 9. Autorités présentes
        'autorites_presentes',

        // 10. Témoins
        'temoins',

        // 11. Chronologie
        'chronologie',

        // 12. Conclusion
        'situation_maitrisee',
        'cause_probable',
        'recommandations',
        'suites_a_donner',
    ];

    protected $casts = [
        'date_heure_alerte' => 'datetime',
        'victimes' => 'array',
        'vehicules_impliques' => 'array',
        'vehicules_utilises' => 'array',
        'actions_realisees' => 'array',
        'autorites_presentes' => 'array',
        'temoins' => 'array',
        'chronologie' => 'array',
    ];

    public function sinistre()
    {
        return $this->belongsTo(Sinistre::class);
    }

    public function groupe()
    {
        return $this->belongsTo(User::class, 'groupe_id');
    }
}
