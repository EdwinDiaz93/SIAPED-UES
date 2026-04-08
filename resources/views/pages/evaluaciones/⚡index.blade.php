<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Evaluacion;
use App\Models\PeriodoEvaluacion;
use App\Models\User;
use Spatie\Permission\Models\Role;

new class extends Component {
    use WithPagination;

    public ?int $periodoSeleccionado = null;

    public function mount()
    {
        // Pre-seleccionar el periodo activo si existe
        $activo = PeriodoEvaluacion::where('estado', 'activo')->first();
        $this->periodoSeleccionado = $activo?->id;
    }

    #[Computed]
    public function periodos()
    {
        return PeriodoEvaluacion::orderBy('anio', 'desc')->orderBy('ciclo')->get();
    }

    #[Computed]
    public function evaluaciones()
    {
        if (!$this->periodoSeleccionado) return collect();

        return Evaluacion::with(['docente.institution.categoria', 'docente.institution.escuela'])
            ->where('periodo_id', $this->periodoSeleccionado)
            ->orderBy('id')
            ->paginate(15);
    }

    #[Computed]
    public function docentes()
    {
        $rolDocente = Role::where('name', 'docente')->first();
        if (!$rolDocente) return collect();
        return User::role($rolDocente->name)->orderBy('name')->get();
    }

    public function updatedPeriodoSeleccionado()
    {
        $this->resetPage();
    }

    /**
     * Crea evaluaciones para TODOS los docentes en el periodo seleccionado.
     */
    public function generarEvaluaciones()
    {
        if (!$this->periodoSeleccionado) return;

        $docentes = $this->docentes;
        $creadas  = 0;

        foreach ($docentes as $docente) {
            $existe = Evaluacion::where('docente_id', $docente->id)
                ->where('periodo_id', $this->periodoSeleccionado)
                ->exists();

            if (!$existe) {
                Evaluacion::create([
                    'docente_id' => $docente->id,
                    'periodo_id' => $this->periodoSeleccionado,
                    'estado'     => 'pendiente',
                ]);
                $creadas++;
            }
        }

        $this->dispatch('notify', type: 'success',
            message: "$creadas evaluaciones generadas correctamente.");
    }

    public function verCuestionario(int $id)
    {
        $this->redirectRoute('evaluaciones.cuestionario', ['id' => $id]);
    }

    public function verFormulario(int $docenteId)
    {
        $this->redirectRoute('formulario.show', [
            'docenteId' => $docenteId,
            'periodoId' => $this->periodoSeleccionado,
        ]);
    }
};
?>

<div class="p-4">

    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Evaluaciones — Labor Académica</h1>
    </div>

    {{-- Selector de periodo --}}
    <div class="flex flex-wrap items-end gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <label class="text-sm font-semibold">Periodo de evaluación</label>
            <select wire:model.live="periodoSeleccionado"
                class="border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark min-w-56">
                <option value="">— Seleccione un periodo —</option>
                @foreach ($this->periodos as $p)
                    <option value="{{ $p->id }}">
                        Ciclo {{ $p->ciclo }} - {{ $p->anio }}
                        ({{ ucfirst($p->estado) }})
                    </option>
                @endforeach
            </select>
        </div>

        @if ($periodoSeleccionado)
            <button wire:click="generarEvaluaciones"
                wire:confirm="¿Generar evaluaciones para todos los docentes activos en este periodo?"
                class="flex items-center gap-2 px-4 py-2 bg-ues text-white rounded-lg cursor-pointer font-medium hover:opacity-90">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Generar Evaluaciones
            </button>
        @endif
    </div>

    {{-- Tabla de evaluaciones --}}
    @if ($periodoSeleccionado)
        <div class="overflow-hidden rounded-radius border border-outline dark:border-outline-dark">
            <table class="w-full text-left text-sm text-on-surface dark:text-on-surface-dark">
                <thead class="border-b bg-ues text-white dark:border-outline-dark">
                    <tr>
                        <th class="p-3">Docente</th>
                        <th class="p-3">Escuela/Unidad</th>
                        <th class="p-3">Categoría</th>
                        <th class="p-3 text-center">Estudiantes</th>
                        <th class="p-3 text-center">Jefe</th>
                        <th class="p-3 text-center">Autoevaluación</th>
                        <th class="p-3 text-center">Promedio</th>
                        <th class="p-3 text-center">Puntaje</th>
                        <th class="p-3 text-center">Estado</th>
                        <th class="p-3 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline dark:divide-outline-dark">
                    @forelse ($this->evaluaciones as $eval)
                        @php
                            $notaMin = $eval->getNotaMinimaRequerida();
                            $cumple  = $eval->cumpleNotaMinima();
                            $statusColors = [
                                'pendiente'   => 'bg-yellow-100 text-yellow-800',
                                'en_progreso' => 'bg-blue-100 text-blue-800',
                                'completada'  => 'bg-green-100 text-green-800',
                            ];
                        @endphp
                        <tr class="hover:bg-surface-alt dark:hover:bg-surface-dark-alt">
                            <td class="p-3 font-medium">
                                {{ $eval->docente->name }} {{ $eval->docente->apellidos }}
                            </td>
                            <td class="p-3 text-xs">
                                {{ $eval->docente->institution?->escuela?->name ?? '-' }}
                            </td>
                            <td class="p-3">
                                <span class="font-semibold uppercase text-xs">
                                    {{ $eval->docente->institution?->categoria?->name ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                @if ($eval->nota_estudiante !== null)
                                    <span class="font-semibold">{{ number_format($eval->nota_estudiante, 2) }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                @if ($eval->nota_jefe !== null)
                                    <span class="font-semibold">{{ number_format($eval->nota_jefe, 2) }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                @if ($eval->nota_auto !== null)
                                    <span class="font-semibold">{{ number_format($eval->nota_auto, 2) }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                @if ($eval->nota_promedio !== null)
                                    <span class="font-bold {{ $cumple ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($eval->nota_promedio, 2) }}
                                    </span>
                                    <span class="text-xs text-gray-400">(min {{ $notaMin }})</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="p-3 text-center font-bold">
                                {{ $eval->puntaje !== null ? number_format($eval->puntaje, 2) . ' pts' : '—' }}
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusColors[$eval->estado] }}">
                                    {{ ucfirst(str_replace('_', ' ', $eval->estado)) }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <button wire:click="verFormulario({{ $eval->docente_id }})"
                                    class="p-1 rounded text-green-600 hover:bg-green-50 cursor-pointer mr-1" title="Formulario consolidado">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                    </svg>
                                </button>
                                <button wire:click="verCuestionario({{ $eval->id }})"
                                    class="p-1 rounded text-blue-600 hover:bg-blue-50 cursor-pointer" title="Llenar cuestionarios">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-8 text-center text-gray-400">
                                No hay evaluaciones para este periodo.
                                Haga clic en "Generar Evaluaciones" para crearlas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $this->evaluaciones->links() }}</div>
    @else
        <div class="flex items-center justify-center h-40 border border-dashed border-outline rounded-xl text-gray-400">
            Seleccione un periodo para ver las evaluaciones.
        </div>
    @endif

</div>
