<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Candidature : un utilisateur (candidat) qui postule à une offre.
 * Table : job_postings (id, offer_id, user_id, timestamps).
 */
class JobPosting extends Model
{
    // Champs autorisés pour JobPosting::create()
    protected $fillable = [
        'offer_id',
        'user_id',
    ];

    // Le candidat qui a postulé (job_postings.user_id → users.id)
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // L'offre visée (job_postings.offer_id → offers.id)
    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }
}
