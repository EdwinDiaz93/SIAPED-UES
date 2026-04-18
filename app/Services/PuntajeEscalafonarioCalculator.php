<?php

namespace App\Services;

use App\Models\CredencialCapacitacion;
use App\Models\CredencialProyeccionSocial;
use App\Models\CredencialEspecializacion;
use App\Models\CredencialInvestigacion;
use App\Models\CredencialSeguimiento;
use App\Models\Evaluacion;
use App\Models\PeriodoEvaluacion;
use App\Models\User;

class PuntajeEscalafonarioCalculator
{
    /**
     * Puntajes máximos por aspecto y categoría (Art. 45 Reglamento Escalafón).
     * PU-I accede por concurso, no por puntaje.
     */
    public const MAXIMOS = [
        'pu-ii' => [
            'labor_academica'   => 10,
            'tiempo_servicio'   => 4,
            'capacitacion'      => 3,
            'proyeccion_social' => 3,
            'especializacion'   => 2,
            'investigacion'     => 1,
            'seguimiento'       => 2,
            'total'             => 25,
        ],
        'pu-iii' => [
            'labor_academica'   => 20,
            'tiempo_servicio'   => 8,
            'capacitacion'      => 6,
            'proyeccion_social' => 6,
            'especializacion'   => 8,
            'investigacion'     => 5,
            'seguimiento'       => 4,
            'total'             => 57,
        ],
        'pu-iv' => [
            'labor_academica'   => 30,
            'tiempo_servicio'   => 12,
            'capacitacion'      => 8,
            'proyeccion_social' => 9,
            'especializacion'   => 12,
            'investigacion'     => 9,
            'seguimiento'       => 6,
            'total'             => 86,
        ],
    ];

    /**
     * Calcula el puntaje completo de los 7 aspectos escalafonarios para un docente.
     *
     * @return array{
     *   docente: User,
     *   categoria_actual: string,
     *   aspectos: array,
     *   total_ganado: float,
     *   maximos_categoria: array,
     *   siguiente_categoria: string|null,
     *   puntaje_para_ascenso: float|null,
     *   cumple_ascenso: bool,
     *   periodo_id: int|null,
     * }
     */
    public static function calcular(int $docenteId, ?int $periodoId = null): array
    {
        $docente = User::with('institution.categoria', 'institution.tipoNombramiento')->findOrFail($docenteId);

        $categoriaValue = strtolower($docente->institution?->categoria?->value ?? 'pu-i');

        // ── 1. Labor Académica ────────────────────────────────────────────────
        // El manual establece que se evalúa 2 veces al año (Ciclo I y Ciclo II)
        // y ambas calificaciones se promedian para obtener la calificación anual.

        if ($periodoId) {
            // Modo ciclo específico: mostrar solo ese período (útil para vista detalle)
            $eval = Evaluacion::where('docente_id', $docenteId)
                ->where('periodo_id', $periodoId)
                ->where('estado', 'completada')
                ->first();

            $puntajeLaborAca   = $eval?->puntaje ?? 0;
            $notaPromedioLabor = $eval?->nota_promedio;
            $detalleLabor      = $eval
                ? "Nota promedio ciclo: " . number_format($eval->nota_promedio, 2)
                : 'Sin evaluación completada en este período';
        } else {
            // Modo anual: buscar el año más reciente con evaluaciones completadas
            // y promediar los ciclos de ese año
            $anioReciente = Evaluacion::where('evaluaciones.docente_id', $docenteId)
                ->where('evaluaciones.estado', 'completada')
                ->join('periodos_evaluacion', 'evaluaciones.periodo_id', '=', 'periodos_evaluacion.id')
                ->max('periodos_evaluacion.anio');

            if ($anioReciente) {
                $evalsAnio = Evaluacion::where('docente_id', $docenteId)
                    ->where('estado', 'completada')
                    ->whereHas('periodo', fn($q) => $q->where('anio', $anioReciente))
                    ->with('periodo')
                    ->get();

                $totalNotas  = $evalsAnio->sum('nota_promedio');
                $cantCiclos  = $evalsAnio->count();
                $notaAnual   = $cantCiclos > 0 ? round($totalNotas / $cantCiclos, 2) : 0;
                $puntajeLaborAca   = round($notaAnual * 0.50, 2);
                $notaPromedioLabor = $notaAnual;

                $ciclosTexto = $evalsAnio->map(fn($e) => "{$e->periodo->ciclo}: " . number_format($e->nota_promedio, 2))->implode(' | ');
                $detalleLabor = "Año {$anioReciente} ({$cantCiclos} ciclo" . ($cantCiclos > 1 ? 's' : '') . "): {$ciclosTexto}";
            } else {
                $puntajeLaborAca   = 0;
                $notaPromedioLabor = null;
                $detalleLabor      = 'Sin evaluaciones completadas';
            }
        }

        // ── 2. Tiempo de Servicio ─────────────────────────────────────────────
        $puntajeTiempo = $docente->institution?->puntaje_tiempo_servicio ?? 0;

        // ── 3–7. Credenciales ─────────────────────────────────────────────────
        $puntajeCapacitacion = CredencialCapacitacion::puntajeTotalDocente($docenteId);
        $puntajeProyeccion   = CredencialProyeccionSocial::puntajeTotalDocente($docenteId);
        $puntajeEspec        = CredencialEspecializacion::puntajeTotalDocente($docenteId);
        $puntajeInv          = CredencialInvestigacion::puntajeTotalDocente($docenteId);
        $puntajeSeguimiento  = CredencialSeguimiento::puntajeTotalDocente($docenteId);

        // ── Aplicar topes de la categoría siguiente (la que quiere alcanzar) ──
        $siguienteCategoria = self::siguienteCategoria($categoriaValue);
        $topes              = $siguienteCategoria
            ? self::MAXIMOS[$siguienteCategoria]
            : self::MAXIMOS['pu-iv']; // si ya es PU-IV, comparar contra PU-IV

        $aspectos = [
            'labor_academica'   => [
                'label'      => 'Labor Académica',
                'ganado'     => min((float) $puntajeLaborAca, $topes['labor_academica']),
                'maximo'     => $topes['labor_academica'],
                'bruto'      => (float) $puntajeLaborAca,
                'detalle'    => $detalleLabor,
            ],
            'tiempo_servicio'   => [
                'label'   => 'Tiempo de Servicio',
                'ganado'  => min((float) $puntajeTiempo, $topes['tiempo_servicio']),
                'maximo'  => $topes['tiempo_servicio'],
                'bruto'   => (float) $puntajeTiempo,
                'detalle' => $docente->institution?->fecha_ingreso
                    ? 'Desde ' . $docente->institution->fecha_ingreso->format('d/m/Y')
                    : 'Sin fecha de ingreso registrada',
            ],
            'capacitacion'      => [
                'label'   => 'Capacitación Didáctica-Pedagógica',
                'ganado'  => min($puntajeCapacitacion, $topes['capacitacion']),
                'maximo'  => $topes['capacitacion'],
                'bruto'   => $puntajeCapacitacion,
                'detalle' => 'Últimos 5 años (máx 3 cursos)',
            ],
            'proyeccion_social' => [
                'label'   => 'Proyección Social',
                'ganado'  => min($puntajeProyeccion, $topes['proyeccion_social']),
                'maximo'  => $topes['proyeccion_social'],
                'bruto'   => $puntajeProyeccion,
                'detalle' => 'Últimos 5 años (máx 3 proyectos)',
            ],
            'especializacion'   => [
                'label'   => 'Especialización',
                'ganado'  => min($puntajeEspec, $topes['especializacion']),
                'maximo'  => $topes['especializacion'],
                'bruto'   => $puntajeEspec,
                'detalle' => 'Grados siempre válidos; cursos últimos 5 años',
            ],
            'investigacion'     => [
                'label'   => 'Investigación y Publicaciones',
                'ganado'  => min($puntajeInv, $topes['investigacion']),
                'maximo'  => $topes['investigacion'],
                'bruto'   => $puntajeInv,
                'detalle' => 'Últimos 5 años',
            ],
            'seguimiento'       => [
                'label'   => 'Seguimiento Curricular',
                'ganado'  => min($puntajeSeguimiento, $topes['seguimiento']),
                'maximo'  => $topes['seguimiento'],
                'bruto'   => $puntajeSeguimiento,
                'detalle' => 'Últimos 5 años (cursos acumulan máx 2 pts)',
            ],
        ];

        $totalGanado = round(array_sum(array_column($aspectos, 'ganado')), 2);
        $totalMaximo = $topes['total'];
        $cumpleAscenso = $siguienteCategoria !== null && $totalGanado >= $totalMaximo;

        return [
            'docente'             => $docente,
            'categoria_actual'    => strtoupper($categoriaValue),
            'siguiente_categoria' => $siguienteCategoria ? strtoupper($siguienteCategoria) : null,
            'aspectos'            => $aspectos,
            'total_ganado'        => $totalGanado,
            'total_maximo'        => $totalMaximo,
            'porcentaje'          => $totalMaximo > 0 ? round(($totalGanado / $totalMaximo) * 100, 1) : 0,
            'cumple_ascenso'      => $cumpleAscenso,
            'puntaje_faltante'    => $cumpleAscenso ? 0 : max(0, $totalMaximo - $totalGanado),
            'periodo_id'          => $periodoId,
            'maximos_referencia'  => self::MAXIMOS,
        ];
    }

    /**
     * Calcula el puntaje acumulado histórico considerando TODOS los años
     * donde Ciclo I y Ciclo II estén cerrados.
     *
     * Labor Académica = suma de (promedio_anual * 0.50) por cada año válido.
     * Credenciales = mismo cálculo que calcular() (ventanas reglamentarias).
     */
    public static function calcularTotal(int $docenteId): array
    {
        $docente = User::with('institution.categoria', 'institution.tipoNombramiento')->findOrFail($docenteId);

        $categoriaValue = strtolower($docente->institution?->categoria?->value ?? 'pu-i');

        // ── Labor Académica acumulada ─────────────────────────────────────────
        // Años donde Ciclo I Y Ciclo II están cerrados
        $aniosCiclo1 = PeriodoEvaluacion::where('ciclo', 'I')->where('estado', 'cerrado')->pluck('anio');
        $aniosCiclo2 = PeriodoEvaluacion::where('ciclo', 'II')->where('estado', 'cerrado')->pluck('anio');
        $aniosValidos = $aniosCiclo1->intersect($aniosCiclo2)->sort()->values();

        $puntajeLaborAcaBruto = 0;
        $detallesAnios        = [];

        foreach ($aniosValidos as $anio) {
            $evalsAnio = Evaluacion::where('docente_id', $docenteId)
                ->where('estado', 'completada')
                ->whereHas('periodo', fn($q) => $q->where('anio', $anio))
                ->with('periodo')
                ->get();

            if ($evalsAnio->count() > 0) {
                $notaAnual    = round($evalsAnio->sum('nota_promedio') / $evalsAnio->count(), 2);
                $puntajeAnio  = round($notaAnual * 0.50, 2);
                $puntajeLaborAcaBruto += $puntajeAnio;

                $ciclosTexto    = $evalsAnio
                    ->map(fn($e) => "{$e->periodo->ciclo}: " . number_format($e->nota_promedio, 2))
                    ->implode(' | ');
                $detallesAnios[] = "Año {$anio} ({$ciclosTexto}) = {$puntajeAnio} pts";
            }
        }

        $puntajeLaborAcaBruto = round($puntajeLaborAcaBruto, 2);

        if (count($detallesAnios) > 0) {
            $detalleLabor = 'Acumulado ' . count($detallesAnios) . ' año(s): ' . implode('; ', $detallesAnios);
        } else {
            $detalleLabor = 'Sin años con ambos ciclos cerrados';
        }

        // ── Tiempo de Servicio ────────────────────────────────────────────────
        $puntajeTiempo = $docente->institution?->puntaje_tiempo_servicio ?? 0;

        // ── Credenciales ──────────────────────────────────────────────────────
        $puntajeCapacitacion = CredencialCapacitacion::puntajeTotalDocente($docenteId);
        $puntajeProyeccion   = CredencialProyeccionSocial::puntajeTotalDocente($docenteId);
        $puntajeEspec        = CredencialEspecializacion::puntajeTotalDocente($docenteId);
        $puntajeInv          = CredencialInvestigacion::puntajeTotalDocente($docenteId);
        $puntajeSeguimiento  = CredencialSeguimiento::puntajeTotalDocente($docenteId);

        // ── Topes de la categoría siguiente ───────────────────────────────────
        $siguienteCategoria = self::siguienteCategoria($categoriaValue);
        $topes              = $siguienteCategoria
            ? self::MAXIMOS[$siguienteCategoria]
            : self::MAXIMOS['pu-iv'];

        $aspectos = [
            'labor_academica'   => [
                'label'   => 'Labor Académica',
                'ganado'  => min($puntajeLaborAcaBruto, $topes['labor_academica']),
                'maximo'  => $topes['labor_academica'],
                'bruto'   => $puntajeLaborAcaBruto,
                'detalle' => $detalleLabor,
            ],
            'tiempo_servicio'   => [
                'label'   => 'Tiempo de Servicio',
                'ganado'  => min((float) $puntajeTiempo, $topes['tiempo_servicio']),
                'maximo'  => $topes['tiempo_servicio'],
                'bruto'   => (float) $puntajeTiempo,
                'detalle' => $docente->institution?->fecha_ingreso
                    ? 'Desde ' . $docente->institution->fecha_ingreso->format('d/m/Y')
                    : 'Sin fecha de ingreso registrada',
            ],
            'capacitacion'      => [
                'label'   => 'Capacitación Didáctica-Pedagógica',
                'ganado'  => min($puntajeCapacitacion, $topes['capacitacion']),
                'maximo'  => $topes['capacitacion'],
                'bruto'   => $puntajeCapacitacion,
                'detalle' => 'Últimos 5 años (máx 3 cursos)',
            ],
            'proyeccion_social' => [
                'label'   => 'Proyección Social',
                'ganado'  => min($puntajeProyeccion, $topes['proyeccion_social']),
                'maximo'  => $topes['proyeccion_social'],
                'bruto'   => $puntajeProyeccion,
                'detalle' => 'Últimos 5 años (máx 3 proyectos)',
            ],
            'especializacion'   => [
                'label'   => 'Especialización',
                'ganado'  => min($puntajeEspec, $topes['especializacion']),
                'maximo'  => $topes['especializacion'],
                'bruto'   => $puntajeEspec,
                'detalle' => 'Grados siempre válidos; cursos últimos 5 años',
            ],
            'investigacion'     => [
                'label'   => 'Investigación y Publicaciones',
                'ganado'  => min($puntajeInv, $topes['investigacion']),
                'maximo'  => $topes['investigacion'],
                'bruto'   => $puntajeInv,
                'detalle' => 'Últimos 5 años',
            ],
            'seguimiento'       => [
                'label'   => 'Seguimiento Curricular',
                'ganado'  => min($puntajeSeguimiento, $topes['seguimiento']),
                'maximo'  => $topes['seguimiento'],
                'bruto'   => $puntajeSeguimiento,
                'detalle' => 'Últimos 5 años (cursos acumulan máx 2 pts)',
            ],
        ];

        $totalGanado   = round(array_sum(array_column($aspectos, 'ganado')), 2);
        $totalMaximo   = $topes['total'];
        $cumpleAscenso = $siguienteCategoria !== null && $totalGanado >= $totalMaximo;

        return [
            'docente'             => $docente,
            'categoria_actual'    => strtoupper($categoriaValue),
            'siguiente_categoria' => $siguienteCategoria ? strtoupper($siguienteCategoria) : null,
            'aspectos'            => $aspectos,
            'total_ganado'        => $totalGanado,
            'total_maximo'        => $totalMaximo,
            'porcentaje'          => $totalMaximo > 0 ? round(($totalGanado / $totalMaximo) * 100, 1) : 0,
            'cumple_ascenso'      => $cumpleAscenso,
            'puntaje_faltante'    => $cumpleAscenso ? 0 : max(0, $totalMaximo - $totalGanado),
            'periodo_id'          => null,
            'maximos_referencia'  => self::MAXIMOS,
            'anios_considerados'  => $aniosValidos->toArray(),
        ];
    }

    private static function siguienteCategoria(string $actual): ?string
    {
        return match (strtolower($actual)) {
            'pu-i'  => 'pu-ii',
            'pu-ii' => 'pu-iii',
            'pu-iii'=> 'pu-iv',
            default => null, // PU-IV ya es el máximo
        };
    }
}
