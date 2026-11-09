<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Offer;
use App\Models\User;

/**
 * Entreprise. Elle est créée automatiquement quand un utilisateur
 * s'inscrit avec le rôle "company" (voir CreateNewUser).
 * Table : companies.
 */
class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    /**
     * Les offres publiées par cette entreprise.
     * Utilisation : $company->offers
     */
    public function offers()
    {
        return $this->hasMany(Offer::class, 'company_id');
    }

    /**
     * Le compte utilisateur lié à l'entreprise (users.company_id).
     */
    public function User()
    {
        return $this->hasOne(User::class,'company_id') ;
    }

    // Champs autorisés pour Company::create() et $company->update().
    protected $fillable =  [
        'name',
    ];
}
