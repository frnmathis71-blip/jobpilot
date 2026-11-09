<?php

namespace App\Actions\Fortify;

use App\Models\Candidat;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Concerns\PasswordValidationRules;

/**
 * Inscription d'un nouvel utilisateur (appelée par Fortify sur POST /register).
 * Branchée dans FortifyServiceProvider::configureActions().
 */
class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        // 1. Validation du formulaire d'inscription.
        //    Le rôle "admin" est volontairement interdit ici.
        Validator::make($input, [
            'role' => ['required', Rule::in(['candidat', 'company'])],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
        ])->validate();

        // 2. Création dans une transaction : si une étape échoue,
        //    rien n'est enregistré (pas d'entreprise sans utilisateur).
        return DB::transaction(function () use ($input) {
            $data = [
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'role' => $input['role'],
            ];

            // 3. Selon le rôle, on crée la fiche liée (Company ou Candidat)
            //    et on mémorise son id dans l'utilisateur.
            if ($input['role'] === 'company') {
                $data['company_id'] = Company::create()->id;
            } else {
                $data['candidat_id'] = Candidat::create()->id;
            };

            return User::create($data);
        });
    }
}