<?php

namespace App\Policies;

use App\Models\MODELE;
use App\Models\User;

class MODELEPolicy
{
    // Exécutée AVANT les autres méthodes : l'admin a tous les droits
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MODELE $SINGULIER): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'ROLE';
    }

    public function update(User $user, MODELE $SINGULIER): bool
    {
        // Pour "seulement SES éléments", remplace par :
        // return $user->role === 'ROLE' && $user->id === $SINGULIER->user_id;
        return $user->role === 'ROLE';
    }

    public function delete(User $user, MODELE $SINGULIER): bool
    {
        return $user->role === 'ROLE';
    }
}
