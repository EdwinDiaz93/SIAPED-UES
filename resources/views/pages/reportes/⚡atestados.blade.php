<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\CredencialCapacitacion;
use App\Models\CredencialProyeccionSocial;
use App\Models\CredencialEspecializacion;
use App\Models\CredencialInvestigacion;
use App\Models\CredencialSeguimiento;
use App\Models\PeriodoEvaluacion;
use App\Models\CatalogType;

new class extends Component {

    public string $filtroCategoria = '';
    public string $filtroEstado    = '';
    public string $filtroEscuela   = '';
    public string $filtroCiclo     = '';

    #[Computed]
    public function escuelas()
    {
        return CatalogType::where('value', 'Escuelas')->first()
            ?->catalogValues ?? collect();
    }

    #[Computed]
    public function periodos()
    {
        return PeriodoEvaluacion::orderBy('anio', 'desc')->orderBy('ciclo')->get();
    }

    #[Computed]
    public function categorias()
    {
        return [
            'Capacitación Didáctica-Pedagógica',
            'Proyección Social',
            'Especialización',
            'Investigación y Publicaciones',
            'Seguimiento Curricular',
        ];
    }

    private function periodoDeFecha($fecha, $periodos): ?PeriodoEvaluacion
    {
        if (!$fecha) return null;

        return $periodos->first(fn ($p) => $p->fecha_inicio && $p->fecha_fin
            && $fecha->gte($p->fecha_inicio) && $fecha->lte($p->fecha_fin));
    }

    #[Computed]
    public function atestados()
    {
        $periodos = $this->periodos;

        $mapear = function ($registros, string $categoria, \Closure $descripcion, string $campoFecha) use ($periodos) {
            return $registros->map(function ($r) use ($categoria, $descripcion, $campoFecha, $periodos) {
                $fecha   = $r->{$campoFecha};
                $periodo = $this->periodoDeFecha($fecha, $periodos);

                return [
                    'docente'     => $r->docente,
                    'categoria'   => $categoria,
                    'descripcion' => $descripcion($r),
                    'fecha'       => $fecha,
                    'puntaje'     => $r->puntaje,
                    'estado'      => $r->estado,
                    'ciclo_id'    => $periodo?->id,
                    'ciclo_label' => $periodo?->label ?? 'Sin ciclo asociado',
                ];
            });
        };

        $todos = $mapear(
            CredencialCapacitacion::with('docente.institution.escuela')->get(),
            'Capacitación Didáctica-Pedagógica',
            fn ($r) => $r->nombre . ($r->institucion ? " — {$r->institucion}" : ''),
            'fecha_fin'
        )->concat($mapear(
            CredencialProyeccionSocial::with('docente.institution.escuela')->get(),
            'Proyección Social',
            fn ($r) => $r->nombre,
            'fecha_fin'
        ))->concat($mapear(
            CredencialEspecializacion::with('docente.institution.escuela')->get(),
            'Especialización',
            fn ($r) => $r->titulo . ($r->institucion ? " — {$r->institucion}" : ''),
            'fecha'
        ))->concat($mapear(
            CredencialInvestigacion::with('docente.institution.escuela')->get(),
            'Investigación y Publicaciones',
            fn ($r) => $r->titulo,
            'fecha'
        ))->concat($mapear(
            CredencialSeguimiento::with('docente.institution.escuela')->get(),
            'Seguimiento Curricular',
            fn ($r) => $r->descripcion,
            'fecha'
        ));

        return $todos
            ->when($this->filtroCategoria, fn ($c) => $c->where('categoria', $this->filtroCategoria))
            ->when($this->filtroEstado, fn ($c) => $c->where('estado', $this->filtroEstado))
            ->when($this->filtroEscuela, fn ($c) => $c->where('docente.institution.escuela.value', $this->filtroEscuela))
            ->when($this->filtroCiclo, fn ($c) => $c->where('ciclo_id', (int) $this->filtroCiclo))
            ->sortByDesc('fecha')
            ->values();
    }

    public function updatedFiltroCategoria() {}
    public function updatedFiltroEstado() {}
    public function updatedFiltroEscuela() {}
    public function updatedFiltroCiclo() {}
};
?>

<div class="p-4">

    <div class="flex items-center justify-between mb-2 print:hidden">
        <h1 class="text-2xl font-bold">Listado de Atestados</h1>
        <button onclick="window.print()"
            class="flex items-center gap-2 px-4 py-2 border border-gray-400 text-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
            </svg>
            Imprimir
        </button>
    </div>
    <p class="text-sm text-gray-500 mb-6">
        Todos los atestados (capacitación, proyección social, especialización, investigación y seguimiento curricular) registrados por los docentes.
    </p>

    {{-- Filtros --}}
    <div class="flex flex-wrap gap-4 items-end mb-6 print:hidden">
        <div>
            <label class="text-sm font-semibold block mb-1">Categoría</label>
            <select wire:model.live="filtroCategoria"
                class="border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark min-w-56">
                <option value="">Todas</option>
                @foreach ($this->categorias as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-semibold block mb-1">Estado</label>
            <select wire:model.live="filtroEstado"
                class="border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark min-w-40">
                <option value="">Todos</option>
                <option value="pendiente">Pendiente</option>
                <option value="aprobado">Aprobado</option>
                <option value="rechazado">Rechazado</option>
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
            <label class="text-sm font-semibold block mb-1">Ciclo Académico</label>
            <select wire:model.live="filtroCiclo"
                class="border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark min-w-48">
                <option value="">Todos</option>
                @foreach ($this->periodos as $p)
                    <option value="{{ $p->id }}">{{ $p->label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-outline dark:border-outline-dark">
        <table class="w-full text-sm">
            <thead class="bg-ues text-white">
                <tr>
                    <th class="p-3 text-left">Docente</th>
                    <th class="p-3 text-center">Escuela / Unidad</th>
                    <th class="p-3 text-left">Categoría</th>
                    <th class="p-3 text-left">Descripción</th>
                    <th class="p-3 text-center">Ciclo Académico</th>
                    <th class="p-3 text-center">Fecha</th>
                    <th class="p-3 text-center">Puntos Posibles</th>
                    <th class="p-3 text-center">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline dark:divide-outline-dark">
                @php
                    $estadoColores = [
                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                        'aprobado'  => 'bg-green-100 text-green-800',
                        'rechazado' => 'bg-red-100 text-red-800',
                    ];
                @endphp
                @forelse ($this->atestados as $a)
                    <tr class="hover:bg-surface-alt/40">
                        <td class="p-3">
                            <p class="font-medium">{{ $a['docente']?->name }} {{ $a['docente']?->apellidos }}</p>
                            <p class="text-xs text-gray-400">{{ $a['docente']?->email }}</p>
                        </td>
                        <td class="p-3 text-center text-xs">{{ $a['docente']?->institution?->escuela?->name ?? '—' }}</td>
                        <td class="p-3 text-xs font-semibold">{{ $a['categoria'] }}</td>
                        <td class="p-3 text-xs">{{ $a['descripcion'] }}</td>
                        <td class="p-3 text-center text-xs">{{ $a['ciclo_label'] }}</td>
                        <td class="p-3 text-center text-xs">{{ $a['fecha']?->format('d/m/Y') ?? '—' }}</td>
                        <td class="p-3 text-center font-bold text-ues">{{ number_format($a['puntaje'], 2) }} pts</td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $estadoColores[$a['estado']] ?? '' }}">
                                {{ ucfirst($a['estado']) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="p-6 text-center text-gray-400">No hay atestados que coincidan con los filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="text-xs text-gray-400 mt-2">Total: {{ $this->atestados->count() }} atestados</p>

</div>

<style>
    @media print {
        .print\:hidden { display: none !important; }
    }
</style>
