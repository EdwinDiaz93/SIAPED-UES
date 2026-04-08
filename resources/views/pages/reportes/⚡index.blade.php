<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Evaluacion;
use App\Models\PeriodoEvaluacion;
use App\Models\User;
use App\Models\FormularioConsolidado;
use App\Services\PuntajeEscalafonarioCalculator;
use Spatie\Permission\Models\Role;

new class extends Component {
    use WithPagination;

    public ?int $periodoId = null;
    public string $filtroEscuela = '';
    public string $filtroCategoria = '';
    public string $tab = 'general';

    public function mount()
    {
        $activo = PeriodoEvaluacion::where('estado', 'activo')->first();
        $this->periodoId = $activo?->id;
    }

    #[Computed]
    public function periodos()
    {
        return PeriodoEvaluacion::orderBy('anio', 'desc')->orderBy('ciclo')->get();
    }

    #[Computed]
    public function docentes()
    {
        $rolDocente = Role::where('name', 'docente')->first();
        if (!$rolDocente) return collect();

        return User::role($rolDocente->name)
            ->with('institution.categoria', 'institution.escuela', 'institution.tipoNombramiento')
            ->when($this->filtroEscuela, fn($q) => $q->whereHas('institution', fn($qi) =>
                $qi->whereHas('escuela', fn($qv) => $qv->where('value', $this->filtroEscuela))
            ))
            ->when($this->filtroCategoria, fn($q) => $q->whereHas('institution', fn($qi) =>
                $qi->whereHas('categoria', fn($qv) => $qv->where('value', $this->filtroCategoria))
            ))
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function evaluacionesPorPeriodo()
    {
        if (!$this->periodoId) return collect();
        return Evaluacion::with('docente.institution.categoria', 'docente.institution.escuela')
            ->where('periodo_id', $this->periodoId)
            ->get();
    }

    // ── Estadísticas generales ────────────────────────────────────────────────

    #[Computed]
    public function totalDocentes()
    {
        return $this->docentes->count();
    }

    #[Computed]
    public function docentesPorCategoria()
    {
        return $this->docentes
            ->groupBy(fn($d) => strtoupper($d->institution?->categoria?->name ?? 'Sin categoría'))
            ->map->count()
            ->sortKeys();
    }

    #[Computed]
    public function docentesPorEscuela()
    {
        return $this->docentes
            ->groupBy(fn($d) => $d->institution?->escuela?->name ?? 'Sin escuela')
            ->map->count()
            ->sortKeys();
    }

    #[Computed]
    public function estadisticasEvaluaciones()
    {
        $evals = $this->evaluacionesPorPeriodo;
        if ($evals->isEmpty()) return null;

        $completadas = $evals->where('estado', 'completada');

        return [
            'total'       => $evals->count(),
            'completadas' => $completadas->count(),
            'pendientes'  => $evals->where('estado', 'pendiente')->count(),
            'en_progreso' => $evals->where('estado', 'en_progreso')->count(),
            'pct_completadas' => $evals->count() > 0
                ? round(($completadas->count() / $evals->count()) * 100, 1)
                : 0,
            'nota_promedio_global' => $completadas->avg('nota_promedio') ?? 0,
            'puntaje_promedio'     => $completadas->avg('puntaje') ?? 0,
            'cumple_minimo'        => $completadas->filter(fn($e) => $e->cumpleNotaMinima())->count(),
        ];
    }

    #[Computed]
    public function rankingDocentes()
    {
        return $this->evaluacionesPorPeriodo
            ->where('estado', 'completada')
            ->sortByDesc('nota_promedio')
            ->take(10)
            ->values();
    }

    #[Computed]
    public function escuelas()
    {
        return \App\Models\CatalogType::where('value', 'Escuelas')->first()
            ?->catalogValues ?? collect();
    }

    #[Computed]
    public function categorias()
    {
        return \App\Models\CatalogType::where('value', 'Categoria Escalafonaria')->first()
            ?->catalogValues ?? collect();
    }

    public function updatedFiltroEscuela()   { $this->resetPage(); }
    public function updatedFiltroCategoria() { $this->resetPage(); }
    public function updatedPeriodoId()       { $this->resetPage(); }

    public function verFormulario(int $docenteId)
    {
        $this->redirectRoute('formulario.show', [
            'docenteId' => $docenteId,
            'periodoId' => $this->periodoId,
        ]);
    }
};
?>

<div class="p-4">

    <h1 class="text-2xl font-bold mb-2">Reportes y Estadísticas</h1>

    {{-- Controles --}}
    <div class="flex flex-wrap gap-4 items-end mb-6 print:hidden">
        <div>
            <label class="text-sm font-semibold block mb-1">Periodo</label>
            <select wire:model.live="periodoId"
                class="border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark min-w-48">
                <option value="">— Todos —</option>
                @foreach ($this->periodos as $p)
                    <option value="{{ $p->id }}">Ciclo {{ $p->ciclo }} - {{ $p->anio }} ({{ ucfirst($p->estado) }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-semibold block mb-1">Escuela / Unidad</label>
            <select wire:model.live="filtroEscuela"
                class="border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark min-w-48">
                <option value="">Todas</option>
                @foreach ($this->escuelas as $e)
                    <option value="{{ $e->value }}">{{ $e->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-semibold block mb-1">Categoría</label>
            <select wire:model.live="filtroCategoria"
                class="border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark">
                <option value="">Todas</option>
                @foreach ($this->categorias as $c)
                    <option value="{{ $c->value }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <button onclick="window.print()"
            class="flex items-center gap-2 px-4 py-2 border border-gray-400 text-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
            </svg>
            Imprimir
        </button>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 border-b border-outline dark:border-outline-dark mb-6 print:hidden">
        @foreach ([
            ['general',    'Resumen General'],
            ['evaluaciones','Evaluaciones por Periodo'],
            ['docentes',    'Detalle Docentes'],
        ] as [$key, $label])
            <button wire:click="$set('tab', '{{ $key }}')"
                @class([
                    'px-4 py-2 text-sm font-bold bg-ues text-white border-b-2 border-primary' => $tab === $key,
                    'px-4 py-2 text-sm font-medium text-on-surface hover:border-b-2 hover:border-b-outline-strong' => $tab !== $key,
                ])>
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ── TAB: RESUMEN GENERAL ── --}}
    @if ($tab === 'general')

        {{-- KPIs --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            @php
                $stats = $this->estadisticasEvaluaciones;
            @endphp
            <div class="p-4 rounded-xl border border-outline dark:border-outline-dark text-center">
                <p class="text-xs text-gray-500 mb-1">Total Docentes</p>
                <p class="text-3xl font-bold text-ues">{{ $this->totalDocentes }}</p>
            </div>
            <div class="p-4 rounded-xl border border-outline dark:border-outline-dark text-center">
                <p class="text-xs text-gray-500 mb-1">Evaluaciones Completadas</p>
                <p class="text-3xl font-bold {{ $stats ? 'text-green-600' : 'text-gray-400' }}">
                    {{ $stats ? $stats['completadas'] . ' / ' . $stats['total'] : '—' }}
                </p>
                @if ($stats)
                    <p class="text-xs text-gray-400">{{ $stats['pct_completadas'] }}% completado</p>
                @endif
            </div>
            <div class="p-4 rounded-xl border border-outline dark:border-outline-dark text-center">
                <p class="text-xs text-gray-500 mb-1">Nota Promedio Global</p>
                <p class="text-3xl font-bold">
                    {{ $stats ? number_format($stats['nota_promedio_global'], 2) : '—' }}
                </p>
                @if ($stats) <p class="text-xs text-gray-400">sobre 10.0</p> @endif
            </div>
            <div class="p-4 rounded-xl border border-outline dark:border-outline-dark text-center">
                <p class="text-xs text-gray-500 mb-1">Cumplen Nota Mínima</p>
                <p class="text-3xl font-bold text-green-600">
                    {{ $stats ? $stats['cumple_minimo'] : '—' }}
                </p>
                @if ($stats)
                    <p class="text-xs text-gray-400">de {{ $stats['completadas'] }} completadas</p>
                @endif
            </div>
        </div>

        {{-- Distribución por categoría y escuela --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

            <div class="p-4 border border-outline dark:border-outline-dark rounded-xl">
                <h3 class="font-bold mb-3">Distribución por Categoría Escalafonaria</h3>
                @forelse ($this->docentesPorCategoria as $cat => $count)
                    @php
                        $pct = $this->totalDocentes > 0 ? round(($count / $this->totalDocentes) * 100) : 0;
                        $barColors = ['PU-I' => 'bg-gray-400', 'PU-II' => 'bg-yellow-400', 'PU-III' => 'bg-blue-500', 'PU-IV' => 'bg-green-500'];
                        $color = $barColors[$cat] ?? 'bg-ues';
                    @endphp
                    <div class="mb-3">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-semibold">{{ $cat }}</span>
                            <span>{{ $count }} docentes ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="{{ $color }} h-4 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm">Sin datos.</p>
                @endforelse
            </div>

            <div class="p-4 border border-outline dark:border-outline-dark rounded-xl">
                <h3 class="font-bold mb-3">Distribución por Escuela / Unidad</h3>
                @forelse ($this->docentesPorEscuela as $escuela => $count)
                    @php $pct = $this->totalDocentes > 0 ? round(($count / $this->totalDocentes) * 100) : 0; @endphp
                    <div class="mb-3">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-semibold truncate max-w-xs">{{ $escuela }}</span>
                            <span class="ml-2 flex-shrink-0">{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-ues h-4 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm">Sin datos.</p>
                @endforelse
            </div>

        </div>

        {{-- Estado evaluaciones si hay periodo --}}
        @if ($stats)
            <div class="p-4 border border-outline dark:border-outline-dark rounded-xl">
                <h3 class="font-bold mb-3">Estado de Evaluaciones — Periodo Seleccionado</h3>
                <div class="grid grid-cols-3 gap-4 text-center">
                    @foreach ([
                        ['Pendientes',   $stats['pendientes'],  'bg-yellow-100 text-yellow-800'],
                        ['En Progreso',  $stats['en_progreso'], 'bg-blue-100 text-blue-800'],
                        ['Completadas',  $stats['completadas'], 'bg-green-100 text-green-800'],
                    ] as [$label, $val, $cls])
                        <div class="p-4 rounded-xl {{ $cls }}">
                            <p class="text-2xl font-bold">{{ $val }}</p>
                            <p class="text-sm font-medium">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    {{-- ── TAB: EVALUACIONES POR PERIODO ── --}}
    @elseif ($tab === 'evaluaciones')

        @if (!$this->periodoId)
            <div class="p-6 text-center text-gray-400 border border-dashed border-outline rounded-xl">
                Seleccione un periodo para ver el reporte de evaluaciones.
            </div>
        @else
            {{-- Ranking top 10 --}}
            <div class="mb-6">
                <h3 class="font-bold mb-3">Top 10 — Mayor Nota Promedio</h3>
                <div class="overflow-hidden rounded-xl border border-outline dark:border-outline-dark">
                    <table class="w-full text-sm">
                        <thead class="bg-ues text-white">
                            <tr>
                                <th class="p-3 text-center w-10">#</th>
                                <th class="p-3 text-left">Docente</th>
                                <th class="p-3 text-center">Categoría</th>
                                <th class="p-3 text-center">Est.</th>
                                <th class="p-3 text-center">Jefe</th>
                                <th class="p-3 text-center">Auto</th>
                                <th class="p-3 text-center">Promedio</th>
                                <th class="p-3 text-center">Puntaje</th>
                                <th class="p-3 text-center">¿Cumple?</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline dark:divide-outline-dark">
                            @forelse ($this->rankingDocentes as $i => $eval)
                                <tr class="hover:bg-surface-alt/40">
                                    <td class="p-3 text-center font-bold text-gray-400">{{ $i + 1 }}</td>
                                    <td class="p-3 font-medium">{{ $eval->docente->name }} {{ $eval->docente->apellidos }}</td>
                                    <td class="p-3 text-center text-xs font-semibold uppercase">{{ $eval->docente->institution?->categoria?->name ?? '—' }}</td>
                                    <td class="p-3 text-center">{{ $eval->nota_estudiante !== null ? number_format($eval->nota_estudiante, 2) : '—' }}</td>
                                    <td class="p-3 text-center">{{ $eval->nota_jefe !== null ? number_format($eval->nota_jefe, 2) : '—' }}</td>
                                    <td class="p-3 text-center">{{ $eval->nota_auto !== null ? number_format($eval->nota_auto, 2) : '—' }}</td>
                                    <td class="p-3 text-center font-bold text-lg">{{ number_format($eval->nota_promedio, 2) }}</td>
                                    <td class="p-3 text-center font-bold text-ues">{{ number_format($eval->puntaje, 2) }} pts</td>
                                    <td class="p-3 text-center">
                                        @if ($eval->cumpleNotaMinima())
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">Sí</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">No</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="p-6 text-center text-gray-400">Sin evaluaciones completadas en este periodo.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tabla completa --}}
            <h3 class="font-bold mb-3">Todas las Evaluaciones del Periodo</h3>
            <div class="overflow-hidden rounded-xl border border-outline dark:border-outline-dark">
                <table class="w-full text-sm">
                    <thead class="bg-ues text-white">
                        <tr>
                            <th class="p-3 text-left">Docente</th>
                            <th class="p-3 text-center">Escuela</th>
                            <th class="p-3 text-center">Cat.</th>
                            <th class="p-3 text-center">Promedio</th>
                            <th class="p-3 text-center">Puntaje</th>
                            <th class="p-3 text-center">Estado</th>
                            <th class="p-3 text-center print:hidden">Formulario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline dark:divide-outline-dark">
                        @php
                            $statusColors = [
                                'pendiente'   => 'bg-yellow-100 text-yellow-800',
                                'en_progreso' => 'bg-blue-100 text-blue-800',
                                'completada'  => 'bg-green-100 text-green-800',
                            ];
                        @endphp
                        @forelse ($this->evaluacionesPorPeriodo->sortBy('docente.name') as $eval)
                            <tr class="hover:bg-surface-alt/40">
                                <td class="p-3 font-medium">{{ $eval->docente->name }} {{ $eval->docente->apellidos }}</td>
                                <td class="p-3 text-center text-xs">{{ $eval->docente->institution?->escuela?->name ?? '—' }}</td>
                                <td class="p-3 text-center text-xs font-semibold uppercase">{{ $eval->docente->institution?->categoria?->name ?? '—' }}</td>
                                <td class="p-3 text-center font-bold">
                                    {{ $eval->nota_promedio !== null ? number_format($eval->nota_promedio, 2) : '—' }}
                                </td>
                                <td class="p-3 text-center font-bold text-ues">
                                    {{ $eval->puntaje !== null ? number_format($eval->puntaje, 2) . ' pts' : '—' }}
                                </td>
                                <td class="p-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$eval->estado] }}">
                                        {{ ucfirst(str_replace('_', ' ', $eval->estado)) }}
                                    </span>
                                </td>
                                <td class="p-3 text-center print:hidden">
                                    <button wire:click="verFormulario({{ $eval->docente_id }})"
                                        class="text-xs px-2 py-1 bg-ues text-white rounded cursor-pointer hover:opacity-80">
                                        Ver
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-6 text-center text-gray-400">Sin evaluaciones en este periodo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

    {{-- ── TAB: DETALLE DOCENTES ── --}}
    @elseif ($tab === 'docentes')

        <div class="overflow-hidden rounded-xl border border-outline dark:border-outline-dark">
            <table class="w-full text-sm">
                <thead class="bg-ues text-white">
                    <tr>
                        <th class="p-3 text-left">Docente</th>
                        <th class="p-3 text-center">Categoría</th>
                        <th class="p-3 text-center">Escuela / Unidad</th>
                        <th class="p-3 text-center">Nombramiento</th>
                        <th class="p-3 text-center">Fecha Ingreso</th>
                        <th class="p-3 text-center">Pts T. Servicio</th>
                        <th class="p-3 text-center print:hidden">Formulario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline dark:divide-outline-dark">
                    @forelse ($this->docentes as $doc)
                        <tr class="hover:bg-surface-alt/40">
                            <td class="p-3">
                                <p class="font-medium">{{ $doc->name }} {{ $doc->apellidos }}</p>
                                <p class="text-xs text-gray-400">{{ $doc->email }}</p>
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-ues/10 text-ues uppercase">
                                    {{ $doc->institution?->categoria?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="p-3 text-center text-xs">{{ $doc->institution?->escuela?->name ?? '—' }}</td>
                            <td class="p-3 text-center text-xs">{{ $doc->institution?->tipoNombramiento?->name ?? '—' }}</td>
                            <td class="p-3 text-center text-xs">
                                {{ $doc->institution?->fecha_ingreso?->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="p-3 text-center font-bold">
                                {{ $doc->institution ? number_format($doc->institution->puntaje_tiempo_servicio, 2) : '—' }} pts
                            </td>
                            <td class="p-3 text-center print:hidden">
                                <button wire:click="verFormulario({{ $doc->id }})"
                                    class="text-xs px-2 py-1 bg-ues text-white rounded cursor-pointer hover:opacity-80">
                                    Formulario
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-6 text-center text-gray-400">Sin docentes registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="text-xs text-gray-400 mt-2">Total: {{ $this->totalDocentes }} docentes</p>

    @endif

</div>

<style>
    @media print {
        .print\:hidden { display: none !important; }
    }
</style>
