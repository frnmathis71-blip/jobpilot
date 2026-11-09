<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Offer;
use App\Models\User;
/**
 * Candidat. Il est créé automatiquement quand un utilisateur
 * s'inscrit avec le rôle "candidat" (voir CreateNewUser).
 * Table : candidats.
 */
class Candidat extends Model
{
    /** @use HasFactory<\Database\Factories\CandidatFactory> */
    use HasFactory;


    /**
     * Les offres liées au candidat.
     * ⚠ La table offers n'a pas de colonne candidat_id : cette relation
     * ne fonctionne pas encore.
     */
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    /**
     * Le compte utilisateur lié au candidat (users.candidat_id).
     */
    public function User()
    {
        return $this->hasOne(User::class,'candidat_id');
    }


    // Champs autorisés pour Candidat::create() et $candidat->update().
    protected $fillable =  [
        'fullname',
    ];
}
