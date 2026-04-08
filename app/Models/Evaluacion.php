<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    protected $table = 'evaluaciones';

    protected $fillable = [
        'docente_id',
        'periodo_id',
        'estado',
        'nota_estudiante',
        'nota_jefe',
        'nota_auto',
        'nota_promedio',
        'puntaje',
    ];

    protected function casts(): array
    {
        return [
            'nota_estudiante' => 'decimal:2',
            'nota_jefe'       => 'decimal:2',
            'nota_auto'       => 'decimal:2',
            'nota_promedio'   => 'decimal:2',
            'puntaje'         => 'decimal:2',
        ];
    }

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    public function periodo()
    {
        return $this->belongsTo(PeriodoEvaluacion::class, 'periodo_id');
    }

    public function laborAcademica()
    {
        return $this->hasMany(EvaluacionLaborAcademica::class, 'evaluacion_id');
    }

    public function cuestionarioEstudiante()
    {
        return $this->hasOne(EvaluacionLaborAcademica::class, 'evaluacion_id')->where('tipo', 'estudiante');
    }

    public function cuestionarioJefe()
    {
        return $this->hasOne(EvaluacionLaborAcademica::class, 'evaluacion_id')->where('tipo', 'jefe');
    }

    public function cuestionarioAuto()
    {
        return $this->hasOne(EvaluacionLaborAcademica::class, 'evaluacion_id')->where('tipo', 'auto');
    }

    // ─── Cálculo de puntaje ───────────────────────────────────────────────────

    /**
     * Recalcula nota_promedio y puntaje desde las 3 notas parciales.
     * Solo calcula si las 3 están presentes.
     */
    public function recalcularPuntaje(): void
    {
        if ($this->nota_estudiante !== null
            && $this->nota_jefe !== null
            && $this->nota_auto !== null) {

            $promedio = ($this->nota_estudiante + $this->nota_jefe + $this->nota_auto) / 3;
            $this->nota_promedio = round($promedio, 2);
            $this->puntaje       = round($promedio * 0.50, 2);
            $this->estado        = 'completada';
        } else {
            $this->estado = 'en_progreso';
        }

        $this->save();
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Nota mínima requerida según categoría escalafonaria del docente.
     */
    public function getNotaMinimaRequerida(): float
    {
        $categoria = $this->docente->institution?->categoria?->value ?? '';

        return match (true) {
            str_contains($categoria, 'pu-iv')  => 8.0,
            str_contains($categoria, 'pu-iii') => 7.5,
            str_contains($categoria, 'pu-ii')  => 7.0,
            default                            => 7.0,
        };
    }

    public function cumpleNotaMinima(): bool
    {
        if ($this->nota_promedio === null) return false;
        return $this->nota_promedio >= $this->getNotaMinimaRequerida();
    }
}
