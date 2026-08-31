<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class DocumentDataForm extends Form
{
    #[Validate('required', message: 'El campo tipo documento es requerido')]
    public $document_type = null;
    #[Validate('required|regex:/^[0-9-]+$/', message: [
        'value.required' => 'El campo numero documento es requerido',
        'value.regex' => 'El campo numero documento solo debe contener números',
    ])]
    public $value = "";
    #[Validate('required|date', message: 'La fecha de expedición es requerida')]
    public $fecha_expedicion = null;
    #[Validate('required|regex:/^[\pL\s.,]+$/u', message: [
        'lugar_expedicion.required' => 'El lugar de expedición es requerido',
        'lugar_expedicion.regex' => 'El lugar de expedición solo debe contener letras',
    ])]
    public $lugar_expedicion = "";
    #[Validate('required|date', message: 'La fecha de expiración es requerida')]
    public $fecha_expiracion = null;
    public $institucion = "";
}
