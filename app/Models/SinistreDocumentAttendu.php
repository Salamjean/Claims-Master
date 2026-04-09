<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SinistreDocumentAttendu extends Model
{
    use HasFactory;

    protected $fillable = [
        'sinistre_id',
        'nom_document',
        'type_champ',
        'is_mandatory',
        'status_client'
    ];

    public function sinistre()
    {
        return $this->belongsTo(Sinistre::class);
    }

    public function documentsSoumis()
    {
        return $this->hasMany(SinistreDocumentSoumis::class, 'sinistre_document_attendu_id');
    }
}
