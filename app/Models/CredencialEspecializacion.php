<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CredencialEspecializacion extends Model
{
    protected $table = 'credenciales_especializacion';

    protected $fillable = [
        'docente_id', 'tipo', 'titulo', 'institucion', 'horas', 'fecha', 'puntaje',
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
     * PhD = 12 | Maestría = 10
     * Curso 60-240 hrs:
     *   60–99 h  → 0.5 | 100–149 h → 1.0 | 150–199 h → 1.5
     *   200–240 h → 2.0 | >240 h → 2.5
     */
    public static function calcularPuntaje(string $tipo, ?int $horas): float
    {
        return match (true) {
            $tipo === 'phd'      => 12.0,
            $tipo === 'maestria' => 10.0,
            $tipo === 'curso' && $horas !== null => match (true) {
                $horas < 60   => 0.0,
                $horas <= 99  => 0.5,
                $horas <= 149 => 1.0,
                $horas <= 199 => 1.5,
                $horas <= 240 => 2.0,
                default       => 2.5,
            },
            default => 0.0,
        };
    }

    /**
     * Grados (PhD/Maestría) siempre válidos. Cursos: solo últimos 5 años, máx 3.
     */
    public static function puntajeTotalDocente(int $docenteId): float
    {
        $corte = Carbon::now()->subYears(5);

        // Grados: siempre cuentan (no tienen límite de antigüedad)
        $grados = self::where('docente_id', $docenteId)
            ->whereIn('tipo', ['phd', 'maestria'])
            ->sum('puntaje');

        // Cursos: solo últimos 5 años, máx 3 de mayor puntaje
        $cursos = self::where('docente_id', $docenteId)
            ->where('tipo', 'curso')
            ->where('fecha', '>=', $corte)
            ->orderByDesc('puntaje')
            ->take(3)
            ->sum('puntaje');

        return round($grados + $cursos, 2);
    }
}
