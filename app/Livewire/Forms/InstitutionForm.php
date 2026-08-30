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
    #[Validate('required|date', message: [
        'fecha_graduacion.required' => 'El campo fecha graduacion es requerido',
        'fecha_graduacion.date' => 'El campo fecha graduacion no es una fecha válida',
    ])]
    public $fecha_graduacion = null;
    #[Validate('required', message: 'El campo escuela o unidad es requerido')]
    public $escuela_unidad = null;
    #[Validate('required', message: 'El campo categoria escalafonaria es requerido')]
    public $categoria_escalafonaria = null;
    #[Validate('required', message: 'El campo area de desempeño es requerido')]
    public $area_desempeño = null;
    #[Validate('required|date', message: [
        'fecha_ingreso.required' => 'El campo fecha de ingreso a la UES es requerido',
        'fecha_ingreso.date' => 'El campo fecha de ingreso a la UES no es una fecha válida',
    ])]
    public $fecha_ingreso = null;
    #[Validate('required', message: 'El campo tipo de nombramiento es requerido')]
    public $tipo_nombramiento = null;
}
