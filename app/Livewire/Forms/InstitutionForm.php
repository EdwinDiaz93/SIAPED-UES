<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class InstitutionForm extends Form
{
    #[Validate('required', message: 'El campo  grado academico es requerido')]
    public $grado_academico = null;
    #[Validate('required', message: 'El campo  institucion es requerido')]
    public $institucion_educativa = null;
    #[Validate('required', message: 'El campo fecha graduacion es requerido')]
    public $fecha_graduacion = null;
    #[Validate('required', message: 'El campo escuela o unidad es requerido')]
    public $escuela_unidad = null;
    #[Validate('required', message: 'El campo categoria escalafonaria es requerido')]
    public $categoria_escalafonaria = null;
    #[Validate('required', message: 'El campo area de desempeño es requerido')]
    public $area_desempeño = null;
}
