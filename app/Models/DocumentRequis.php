<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequis extends Model
{
    use HasFactory;

    protected $table = 'documents_requis';

    protected $fillable = [
        'user_id',
        'type_sinistre',
        'nom_document',
        'type_champ',
    ];

    /**
     * L'assurance (user) qui a défini cette exigence.
     */
    public function assurance()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
