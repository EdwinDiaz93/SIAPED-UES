<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\Models\Role;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {

        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ], [
            'email.required' => 'El correo electrónico es requerido.',
            'email.email'    => 'Ingresa un correo electrónico válido.',
            'email.unique'   => 'Este correo electrónico ya está registrado.',
        ])->validate();
        $user = User::create([
            // 'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);
        $rolInactivo = Role::findByName('inactivo');
        $user->assignRole($rolInactivo);
        return $user;
    }
}
