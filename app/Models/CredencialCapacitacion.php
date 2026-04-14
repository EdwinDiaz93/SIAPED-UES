<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CredencialCapacitacion extends Model
{
    protected $table = 'credenciales_capacitacion';

    protected $fillable = [
        'docente_id', 'tipo', 'nombre', 'institucion',
        'horas', 'fecha_inicio', 'fecha_fin', 'puntaje', 'estado',
        'archivo_path', 'archivo_descripcion',
    ];

    protected function casts(): array
    {
        return ['fecha_inicio' => 'date', 'fecha_fin' => 'date'];
    }

    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    /**
     * Calcula el puntaje individual según reglamento:
     * - diplomado/maestría docencia: 8.0 pts
     * - curso >40 hrs:
     *     41–80 h  → 1.0 | 81–120 h → 1.5 | 121–200 h → 2.0 | 201–300 h → 2.5 | >300 h → 3.0
     */
    public static function calcularPuntaje(string $tipo, ?int $horas): float
    {
        if ($tipo === 'diplomado_maestria') return 8.0;

        if ($horas === null || $horas <= 40) return 0.0;

        return match (true) {
            $horas <= 80  => 1.0,
            $horas <= 120 => 1.5,
            $horas <= 200 => 2.0,
            $horas <= 300 => 2.5,
            default       => 3.0,
        };
    }

    /**
     * Puntaje total del docente en este aspecto.
     * Regla: máx 3 cursos (los de mayor puntaje). Diplomado/maestría cuenta aparte.
     * Solo últimos 5 años.
     */
    public static function puntajeTotalDocente(int $docenteId): float
    {
        $corte = Carbon::now()->subYears(5);

        $registros = self::where('docente_id', $docenteId)
            ->where('fecha_fin', '>=', $corte)
            ->where('estado', 'aprobado')
            ->get();

        // Diplomado/maestría docencia (se suma directamente, sin límite)
        $diplomados = $registros->where('tipo', 'diplomado_maestria')
            ->sum('puntaje');

        // Cursos: máximo 3, ordenados de mayor a menor puntaje
        $cursos = $registros->where('tipo', 'curso')
            ->sortByDesc('puntaje')
            ->take(3)
            ->sum('puntaje');

        return round($diplomados + $cursos, 2);
    }
}
