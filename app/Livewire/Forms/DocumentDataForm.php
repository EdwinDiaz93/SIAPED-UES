<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class DocumentDataForm extends Form
{
    #[Validate('required', message: 'El campo tipo documento es requerido')]
    public $document_type = null;
    #[Validate('required', message: 'El campo numero documento es requerido')]
    public $value = "";
    #[Validate('required|date', message: 'La fecha de expedición es requerida')]
    public $fecha_expedicion = null;
    #[Validate('required', message: 'El lugar de expedición es requerido')]
    public $lugar_expedicion = "";
    #[Validate('required|date', message: 'La fecha de expiración es requerida')]
    public $fecha_expiracion = null;
    public $institucion = "";
}
