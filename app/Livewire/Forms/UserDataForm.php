<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class UserDataForm extends Form
{
    #[Validate('required|regex:/^[\pL\s]+$/u', message: [
        'nombres.required' => 'El campo nombres es requerido',
        'nombres.regex' => 'El campo nombres solo debe contener letras',
    ])]
    public $nombres = '';
    #[Validate('required|regex:/^[\pL\s]+$/u', message: [
        'apellidos.required' => 'El campo apellidos es requerido',
        'apellidos.regex' => 'El campo apellidos solo debe contener letras',
    ])]
    public $apellidos = '';
    #[Validate('required', message: 'El campo sexo es requerido')]
    public $sexo = null;
    #[Validate('required', message: 'El campo fecha de nacimiento es requerido')]
    public $fecha_nacimiento = null;
    #[Validate('required', message: 'El campo nacionalidad es requerido')]
    public $nacionalidad = null;
    #[Validate('required', message: 'El campo estado civil es requerido')]
    public $estado_civil = null;
    public $conyugue = '';
    #[Validate('required', message: 'El campo direccion es requerido')]
    public $direccion = '';
    #[Validate('required', message: 'El campo telefono es requerido')]
    public $telefono = '';
}
