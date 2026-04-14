<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CredencialProyeccionSocial extends Model
{
    protected $table = 'credenciales_proyeccion_social';

    protected $fillable = [
        'docente_id', 'nombre', 'responsabilidad', 'cobertura',
        'duracion', 'fecha_inicio', 'fecha_fin', 'puntaje', 'estado',
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
     * puntaje = responsabilidad + cobertura + duración  (máx 3.0 por proyecto)
     */
    public static function calcularPuntaje(
        string $responsabilidad,
        string $cobertura,
        string $duracion
    ): float {
        $pts = 0.0;

        $pts += match ($responsabilidad) {
            'formulador'  => 0.5,
            'ejecutor'    => 0.75,
            'coordinador' => 1.0,
            default       => 0,
        };

        $pts += match ($cobertura) {
            'local'    => 0.3,
            'regional' => 0.7,
            'nacional' => 1.0,
            default    => 0,
        };

        $pts += match ($duracion) {
            'lte3meses' => 0.3,
            '3a6meses'  => 0.7,
            'gt6meses'  => 1.0,
            default     => 0,
        };

        return round($pts, 2);
    }

    /**
     * Puntaje total: máx 3 proyectos (mayor puntaje). Solo últimos 5 años.
     */
    public static function puntajeTotalDocente(int $docenteId): float
    {
        $corte = Carbon::now()->subYears(5);

        return self::where('docente_id', $docenteId)
            ->where('fecha_fin', '>=', $corte)
            ->where('estado', 'aprobado')
            ->orderByDesc('puntaje')
            ->take(3)
            ->sum('puntaje');
    }

    // Etiquetas para la UI
    public static function etiquetasResponsabilidad(): array
    {
        return ['formulador' => 'Formulador', 'ejecutor' => 'Ejecutor', 'coordinador' => 'Coordinador'];
    }

    public static function etiquetasCobertura(): array
    {
        return ['local' => 'Local', 'regional' => 'Regional', 'nacional' => 'Nacional'];
    }

    public static function etiquetasDuracion(): array
    {
        return ['lte3meses' => '≤ 3 meses', '3a6meses' => '3 a 6 meses', 'gt6meses' => '> 6 meses'];
    }
}
