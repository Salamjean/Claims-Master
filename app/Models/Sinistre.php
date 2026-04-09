<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sinistre extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($sinistre) {
            if (empty($sinistre->numero_sinistre)) {
                $sinistre->numero_sinistre = 'SI-' . strtoupper(\Illuminate\Support\Str::random(8));
            }
        });
    }

    protected $fillable = [
        'user_id',
        'type_sinistre',
        'description',
        'latitude',
        'longitude',
        'photos',
        'status',
        'assurance_id',
        'assigned_service_id',
        'ai_analysis_status',
        'ai_analysis_report',
        'workflow_step',
        'numero_sinistre',
        'date_survenance',
        'date_declaration',
        'date_ouverture',
        'moyen_declaration',
        'est_couvert',
        'motif_rejet',
        'expert_id',
        'garage_id',
        'date_mandat_expert',
        'date_rapport_expert',
        'date_bon_prise_charge',
        'date_bon_sortie',
        'methode_constat',
        'assistance_sollicitee',
        'nom_assisteur',
        'contrat_id',
        'assigned_agent_id',
        'nearby_units',
        'agent_start_lat',
        'agent_start_lng',
    ];

    protected $casts = [
        'photos' => 'array',
        'ai_analysis_report' => 'array',
        'date_survenance' => 'datetime',
        'date_declaration' => 'datetime',
        'date_ouverture' => 'datetime',
        'date_mandat_expert' => 'datetime',
        'date_rapport_expert' => 'datetime',
        'date_bon_prise_charge' => 'datetime',
        'date_bon_sortie' => 'datetime',
        'est_couvert' => 'boolean',
        'nearby_units' => 'array',
    ];

    /**
     * Filtre les sinistres où l'utilisateur est impliqué (Poste ou Agent).
     * S'applique pour la visibilité partagée des 3 unités localisées.
     */
    public function scopeWhereInvolved($query, $id, $serviceId = null)
    {
        // On rassemble tous les IDs liés à l'utilisateur (Agent + Poste)
        $userIds = array_filter([(int)$id, (int)$serviceId]);

        return $query->where(function ($q) use ($userIds) {
            // CAS 1 : LE SINISTRE EST ASSIGNÉ À UN AGENT -> Visibilité EXCLUSIVE
            $q->where(function ($sub) use ($userIds) {
                $sub->whereNotNull('assigned_agent_id')
                    ->where(function ($inner) use ($userIds) {
                        $inner->whereIn('assigned_agent_id', $userIds)
                              ->orWhereIn('assigned_service_id', $userIds);
                    });
            })
            // CAS 2 : LE SINISTRE EST LIBRE -> Visibilité partagée des 3 localisés
            ->orWhere(function ($sub) use ($userIds) {
                $sub->whereNull('assigned_agent_id')
                    ->where('status', 'en_attente')
                    ->where(function ($inner) use ($userIds) {
                        foreach ($userIds as $uid) {
                            $inner->orWhere('assigned_service_id', $uid)
                                  ->orWhereJsonContains('nearby_units', [['id' => $uid]])
                                  ->orWhereJsonContains('nearby_units', [['parent_service_id' => $uid]]);
                        }
                    });
            })
            // CAS 3 : LE SINISTRE EST ASSIGNÉ À UN POSTE MAIS PAS ENCORE À UN AGENT 
            // (Scenario où le poste a été localisé n°1 ou assigné par l'admin)
            ->orWhere(function ($sub) use ($userIds) {
                $sub->whereNull('assigned_agent_id')
                    ->whereIn('assigned_service_id', $userIds);
            });
        });
    }

    public function assure()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assurance()
    {
        return $this->belongsTo(User::class, 'assurance_id');
    }

    public function expert()
    {
        return $this->belongsTo(User::class, 'expert_id');
    }

    public function garage()
    {
        return $this->belongsTo(User::class, 'garage_id');
    }

    public function constat()
    {
        return $this->hasOne(\App\Models\Constat::class, 'sinistre_id');
    }

    public function service()
    {
        return $this->belongsTo(User::class, 'assigned_service_id');
    }

    public function documentsAttendus()
    {
        return $this->hasMany(SinistreDocumentAttendu::class);
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'contrat_id');
    }

    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }
}
