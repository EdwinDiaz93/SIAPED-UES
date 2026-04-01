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
    public $fecha_expedicion = null;
    public $lugar_expedicion = "";
    public $fecha_expiracion = null;
    public $institucion = "";
}
