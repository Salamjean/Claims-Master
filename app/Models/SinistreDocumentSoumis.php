<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SinistreDocumentSoumis extends Model
{
    use HasFactory;

    protected $fillable = [
        'sinistre_document_attendu_id',
        'file_path',
        'file_value',
        'ai_compliance_status',
        'ai_feedback',
        'manager_override_status'
    ];

    public function documentAttendu()
    {
        return $this->belongsTo(SinistreDocumentAttendu::class, 'sinistre_document_attendu_id');
    }
}
