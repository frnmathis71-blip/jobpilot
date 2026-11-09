<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;

/**
 * Offre d'emploi publiée par une entreprise.
 * Table : offers (id, company_id, Salary, Name, Description, timestamps).
 */
class Offer extends Model
{
    /** @use HasFactory<\Database\Factories\OfferFactory> */
    use HasFactory;

    /**
     * L'entreprise qui propose l'offre (offers.company_id → companies.id).
     * Utilisation : $offer->company->name
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'job_postings');
    }

    public function jobposting()
    {
        return $this->hasMany(JobPosting::class, 'offer_id');
    }

    // Champs autorisés pour Offer::create() et $offer->update().
    // Attention : les noms commencent par une majuscule, comme les colonnes.
    protected $fillable = [
        'Name',
        'Salary',
        'company_id',
        'Description',
    ];
}
