<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CredencialSeguimiento extends Model
{
    protected $table = 'credenciales_seguimiento';

    protected $fillable = [
        'docente_id', 'tipo', 'descripcion', 'horas', 'fecha', 'puntaje',
    ];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    /**
     * grado_adicional: 3.0 pts
     * curso 20-60 hrs: 0.5 pts (acumulado máx 2.0)
     * coordinacion/comision: 1.0 pts c/u
     * idioma: 1.0 pts c/idioma
     */
    public static function calcularPuntaje(string $tipo, ?int $horas): float
    {
        return match ($tipo) {
            'grado_adicional'       => 3.0,
            'coordinacion_comision' => 1.0,
            'idioma'                => 1.0,
            'curso' => ($horas !== null && $horas >= 20 && $horas <= 60) ? 0.5 : 0.0,
            default => 0.0,
        };
    }

    /**
     * Puntaje total. Solo últimos 5 años.
     * Cursos: puntaje acumulado máx 2.0.
     */
    public static function puntajeTotalDocente(int $docenteId): float
    {
        $corte = Carbon::now()->subYears(5);

        $registros = self::where('docente_id', $docenteId)
            ->where('fecha', '>=', $corte)
            ->get();

        $grados      = $registros->where('tipo', 'grado_adicional')->sum('puntaje');
        $cursos      = min(2.0, $registros->where('tipo', 'curso')->sum('puntaje'));
        $comisiones  = $registros->where('tipo', 'coordinacion_comision')->sum('puntaje');
        $idiomas     = $registros->where('tipo', 'idioma')->sum('puntaje');

        return round($grados + $cursos + $comisiones + $idiomas, 2);
    }
}
