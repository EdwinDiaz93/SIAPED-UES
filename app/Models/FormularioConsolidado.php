<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormularioConsolidado extends Model
{
    protected $table = 'formularios_consolidados';

    protected $fillable = [
        'docente_id',
        'periodo_id',
        'generado_por',
        'aspectos',
        'total_ganado',
        'total_maximo',
        'categoria_actual',
        'siguiente_categoria',
        'cumple_ascenso',
    ];

    protected function casts(): array
    {
        return [
            'aspectos'       => 'array',
            'cumple_ascenso' => 'boolean',
            'total_ganado'   => 'decimal:2',
            'total_maximo'   => 'decimal:2',
        ];
    }

    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    public function periodo()
    {
        return $this->belongsTo(PeriodoEvaluacion::class, 'periodo_id');
    }

    public function generadoPor()
    {
        return $this->belongsTo(User::class, 'generado_por');
    }
}
