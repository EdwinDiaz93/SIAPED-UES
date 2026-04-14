<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CredencialInvestigacion extends Model
{
    protected $table = 'credenciales_investigacion';

    protected $fillable = [
        'docente_id', 'tipo', 'titulo', 'fecha',
        'financiamiento', 'participacion', 'duracion_proyecto',
        'tipo_publicacion', 'puntaje', 'estado',
        'archivo_path', 'archivo_descripcion',
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
     * Proyectos: puntaje = financiamiento + participación + duración
     *   financiamiento: propio=0.3 | institucional=0.5 | externo=1.0
     *   participación:  colaborador=0.5 | investigador=1.0 | coordinador=1.5
     *   duración:       <1 año=0.5 | 1-2 años=1.0 | >2 años=1.5
     *
     * Publicaciones:
     *   libro=3.0 | capítulo=2.0 | artículo indexado=2.5 | artículo no indexado=1.0
     *
     * Red de investigación: 1.0 por red
     * Patente:              2.0 por patente
     */
    public static function calcularPuntaje(array $datos): float
    {
        $tipo = $datos['tipo'];

        if ($tipo === 'proyecto') {
            $fin = match ($datos['financiamiento'] ?? '') {
                'propio'        => 0.3,
                'institucional' => 0.5,
                'externo'       => 1.0,
                default         => 0,
            };
            $par = match ($datos['participacion'] ?? '') {
                'colaborador'  => 0.5,
                'investigador' => 1.0,
                'coordinador'  => 1.5,
                default        => 0,
            };
            $dur = match ($datos['duracion_proyecto'] ?? '') {
                'lt1anio'   => 0.5,
                '1a2anios'  => 1.0,
                'gt2anios'  => 1.5,
                default     => 0,
            };
            return round($fin + $par + $dur, 2);
        }

        if ($tipo === 'publicacion') {
            return match ($datos['tipo_publicacion'] ?? '') {
                'libro'               => 3.0,
                'capitulo'            => 2.0,
                'articulo_indexado'   => 2.5,
                'articulo_no_indexado'=> 1.0,
                default               => 0,
            };
        }

        return match ($tipo) {
            'red'     => 1.0,
            'patente' => 2.0,
            default   => 0,
        };
    }

    /** Solo últimos 5 años. */
    public static function puntajeTotalDocente(int $docenteId): float
    {
        $corte = Carbon::now()->subYears(5);

        return round(
            self::where('docente_id', $docenteId)
                ->where('fecha', '>=', $corte)
                ->where('estado', 'aprobado')
                ->sum('puntaje'),
            2
        );
    }

    public static function etiquetasTipo(): array
    {
        return [
            'proyecto'    => 'Proyecto de investigación',
            'publicacion' => 'Publicación',
            'red'         => 'Red de investigación',
            'patente'     => 'Patente',
        ];
    }
}
