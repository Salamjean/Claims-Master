<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'prenom',
        'email',
        'contact',
        'commune',
        'adresse',
        'latitude',
        'longitude',
        'role',
        'code_user',
        'profile_picture',
        'password',
        'must_change_password',
        'email_verified_at',
        'assurance_id',
        'service_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    /**
     * Profil assurance lié à cet utilisateur (si rôle 'assurance')
     */
    public function assuranceProfile()
    {
        return $this->hasOne(AssuranceProfile::class);
    }

    /**
     * Contrats liés à cet assuré
     */
    public function contrats()
    {
        return $this->hasMany(Contrat::class, 'client_id');
    }

    /**
     * Pour les rôles 'expert' et 'garage' : l'assurance qui les a créés
     */
    public function createurAssurance()
    {
        return $this->belongsTo(User::class, 'assurance_id');
    }

    /**
     * Pour le rôle 'assurance' : ses experts
     */
    public function experts()
    {
        return $this->hasMany(User::class, 'assurance_id')->where('role', 'expert');
    }

    /**
     * Pour le rôle 'assurance' : ses garages
     */
    public function garages()
    {
        return $this->hasMany(User::class, 'assurance_id')->where('role', 'garage');
    }

    /**
     * Pour les rôles 'police' et 'gendarmerie' : leurs agents
     */
    public function agents()
    {
        return $this->hasMany(User::class, 'service_id')->where('role', 'agent');
    }

    /**
     * Pour le rôle 'agent' : le service qui l'a créé
     */
    public function service()
    {
        return $this->belongsTo(User::class, 'service_id');
    }
}
