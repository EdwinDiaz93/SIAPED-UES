<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodoEvaluacion extends Model
{
    protected $table = 'periodos_evaluacion';

    protected $fillable = [
        'anio',
        'ciclo',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'descripcion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin'    => 'date',
        ];
    }

    public function getLabelAttribute(): string
    {
        return "Ciclo {$this->ciclo} - {$this->anio}";
    }

    public function getEstadoBadgeColorAttribute(): string
    {
        return match ($this->estado) {
            'activo'    => 'green',
            'cerrado'   => 'red',
            default     => 'yellow',
        };
    }

    public function scopeActivo($query)
    {
        return $query->where('estado', 'activo');
    }
}
