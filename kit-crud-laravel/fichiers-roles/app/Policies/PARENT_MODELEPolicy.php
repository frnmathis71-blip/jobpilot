<?php

namespace App\Policies;

use App\Models\PARENT_MODELE;
use App\Models\User;

class PARENT_MODELEPolicy
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

    public function view(User $user, PARENT_MODELE $PARENT_SINGULIER): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'ROLE';
    }

    public function update(User $user, PARENT_MODELE $PARENT_SINGULIER): bool
    {
        // Pour "seulement SES éléments", remplace par :
        // return $user->role === 'ROLE' && $user->id === $PARENT_SINGULIER->user_id;
        return $user->role === 'ROLE';
    }

    public function delete(User $user, PARENT_MODELE $PARENT_SINGULIER): bool
    {
        return $user->role === 'ROLE';
    }
}
