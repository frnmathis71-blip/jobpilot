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

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'role' => ['required', Rule::in(['candidat', 'company'])],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input) {
            $data = [
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'role' => $input['role'],
            ];

            if ($input['role'] === 'company') {
                $data['company_id'] = Company::create()->id;
            } else {
                $data['candidat_id'] = Candidat::create()->id;
            };

            return User::create($data);
        });
    }
}