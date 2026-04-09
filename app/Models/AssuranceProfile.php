<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssuranceProfile extends Model
{
    use HasFactory;

    protected $table = 'assurance_profiles';

    protected $fillable = [
        'user_id',
        'numero_rccm',
        'path_rccm',
        'numero_dfe',
        'path_dfe',
    ];

    /**
     * Relation : appartient à un utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
