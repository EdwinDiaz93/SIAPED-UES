<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluacionLaborAcademica extends Model
{
    protected $table = 'evaluacion_labor_academica';

    protected $fillable = [
        'evaluacion_id',
        'tipo',
        'evaluador_id',
        'criterios',
        'nota_ponderada',
    ];

    protected function casts(): array
    {
        return [
            'criterios'      => 'array',
            'nota_ponderada' => 'decimal:2',
        ];
    }

    // ─── Pesos por cuestionario (según Manual de Evaluación UES-FIA) ──────────

    public const PESOS_ESTUDIANTE = [
        'planificacion'              => 0.15,
        'metodologia'                => 0.15,
        'desarrollo_contenidos'      => 0.20,
        'relacion_docente_estudiante'=> 0.10,
        'evaluacion'                 => 0.10,
        'actitudes'                  => 0.15,
        'responsabilidad'            => 0.15,
    ];

    public const PESOS_JEFE = [
        'planificacion'              => 0.15,
        'desempeno_administrativo'   => 0.30,
        'relacion_comunidad'         => 0.10,
        'evaluacion'                 => 0.10,
        'actitudes'                  => 0.10,
        'responsabilidad'            => 0.15,
        'participacion_proyectos'    => 0.10,
    ];

    public const PESOS_AUTO = [
        'planificacion'              => 0.20,
        'desempeno_administrativo'   => 0.25,
        'capacitacion_pedagogica'    => 0.30,
        'relaciones'                 => 0.10,
        'actitudes'                  => 0.15,
    ];

    public const ETIQUETAS_ESTUDIANTE = [
        'planificacion'               => 'Planificación',
        'metodologia'                 => 'Metodología',
        'desarrollo_contenidos'       => 'Desarrollo de Contenidos',
        'relacion_docente_estudiante' => 'Relación Docente-Estudiante',
        'evaluacion'                  => 'Evaluación',
        'actitudes'                   => 'Actitudes',
        'responsabilidad'             => 'Responsabilidad',
    ];

    public const ETIQUETAS_JEFE = [
        'planificacion'              => 'Planificación',
        'desempeno_administrativo'   => 'Desempeño Administrativo',
        'relacion_comunidad'         => 'Relación con la Comunidad',
        'evaluacion'                 => 'Evaluación',
        'actitudes'                  => 'Actitudes',
        'responsabilidad'            => 'Responsabilidad',
        'participacion_proyectos'    => 'Participación en Proyectos',
    ];

    public const ETIQUETAS_AUTO = [
        'planificacion'              => 'Planificación',
        'desempeno_administrativo'   => 'Desempeño Administrativo',
        'capacitacion_pedagogica'    => 'Capacitación Pedagógica',
        'relaciones'                 => 'Relaciones',
        'actitudes'                  => 'Actitudes',
    ];

    // ─── Helpers estáticos ───────────────────────────────────────────────────

    public static function pesosParaTipo(string $tipo): array
    {
        return match ($tipo) {
            'estudiante' => self::PESOS_ESTUDIANTE,
            'jefe'       => self::PESOS_JEFE,
            'auto'       => self::PESOS_AUTO,
            default      => [],
        };
    }

    public static function etiquetasParaTipo(string $tipo): array
    {
        return match ($tipo) {
            'estudiante' => self::ETIQUETAS_ESTUDIANTE,
            'jefe'       => self::ETIQUETAS_JEFE,
            'auto'       => self::ETIQUETAS_AUTO,
            default      => [],
        };
    }

    /**
     * Calcula la nota ponderada (0-10) desde un array de criterios.
     */
    public static function calcularNotaPonderada(string $tipo, array $criterios): float
    {
        $pesos  = self::pesosParaTipo($tipo);
        $total  = 0.0;

        foreach ($pesos as $key => $peso) {
            $score  = isset($criterios[$key]) ? (float) $criterios[$key] : 0;
            $total += $score * $peso;
        }

        return round($total, 2);
    }

    // ─── Relaciones ──────────────────────────────────────────────────────────

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }

    public function evaluador()
    {
        return $this->belongsTo(User::class, 'evaluador_id');
    }
}
