<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\PeriodoEvaluacion;

new class extends Component {
    use WithPagination;

    // --- Estado del modal ---
    public bool $showModal = false;
    public ?int $editingId = null;

    // --- Campos del formulario ---
    public $anio        = '';
    public $ciclo       = 'I';
    public $fecha_inicio = '';
    public $fecha_fin    = '';
    public $estado       = 'pendiente';
    public $descripcion  = '';

    // --- Filtros de listado ---
    public $filtroEstado = '';

    protected function rules(): array
    {
        $uniqueRule = 'unique:periodos_evaluacion,anio,' . ($this->editingId ?? 'NULL') . ',id,ciclo,' . $this->ciclo;

        return [
            'anio'        => ['required', 'integer', 'min:2000', 'max:2100', $uniqueRule],
            'ciclo'       => ['required', 'in:I,II'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'   => ['required', 'date', 'after:fecha_inicio'],
            'estado'      => ['required', 'in:pendiente,activo,cerrado'],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected $messages = [
        'anio.required'        => 'El año es requerido.',
        'anio.unique'          => 'Ya existe un periodo para ese año y ciclo.',
        'ciclo.required'       => 'El ciclo es requerido.',
        'fecha_inicio.required' => 'La fecha de inicio es requerida.',
        'fecha_fin.required'   => 'La fecha de fin es requerida.',
        'fecha_fin.after'      => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        'estado.required'      => 'El estado es requerido.',
    ];

    #[Computed]
    public function periodos()
    {
        return PeriodoEvaluacion::when($this->filtroEstado, fn($q) => $q->where('estado', $this->filtroEstado))
            ->orderBy('anio', 'desc')
            ->orderBy('ciclo', 'asc')
            ->paginate(10);
    }

    public function updatedFiltroEstado()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->resetForm();
        $this->anio = now()->year;
        $this->showModal = true;
    }

    public function openEdit(int $id)
    {
        $periodo = PeriodoEvaluacion::findOrFail($id);
        $this->editingId   = $periodo->id;
        $this->anio        = $periodo->anio;
        $this->ciclo       = $periodo->ciclo;
        $this->fecha_inicio = $periodo->fecha_inicio->format('Y-m-d');
        $this->fecha_fin   = $periodo->fecha_fin->format('Y-m-d');
        $this->estado      = $periodo->estado;
        $this->descripcion = $periodo->descripcion;
        $this->showModal   = true;
    }

    public function save()
    {
        $this->validate();

        // Solo puede haber un periodo activo a la vez
        if ($this->estado === 'activo') {
            $activoQuery = PeriodoEvaluacion::where('estado', 'activo');
            if ($this->editingId) {
                $activoQuery->where('id', '!=', $this->editingId);
            }
            if ($activoQuery->exists()) {
                $this->addError('estado', 'Ya existe un periodo activo. Ciérrelo antes de activar otro.');
                return;
            }
        }

        $data = [
            'anio'        => $this->anio,
            'ciclo'       => $this->ciclo,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin'   => $this->fecha_fin,
            'estado'      => $this->estado,
            'descripcion' => $this->descripcion,
        ];

        if ($this->editingId) {
            PeriodoEvaluacion::findOrFail($this->editingId)->update($data);
            $this->dispatch('notify', type: 'success', message: 'Periodo actualizado correctamente.');
        } else {
            PeriodoEvaluacion::create($data);
            $this->dispatch('notify', type: 'success', message: 'Periodo creado correctamente.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function cambiarEstado(int $id, string $nuevoEstado)
    {
        if ($nuevoEstado === 'activo') {
            if (PeriodoEvaluacion::where('estado', 'activo')->where('id', '!=', $id)->exists()) {
                $this->dispatch('notify', type: 'error', message: 'Ya existe un periodo activo.');
                return;
            }
        }
        PeriodoEvaluacion::findOrFail($id)->update(['estado' => $nuevoEstado]);
        $this->dispatch('notify', type: 'success', message: 'Estado actualizado.');
    }

    public function delete(int $id)
    {
        $periodo = PeriodoEvaluacion::findOrFail($id);
        if ($periodo->estado === 'activo') {
            $this->dispatch('notify', type: 'error', message: 'No se puede eliminar un periodo activo.');
            return;
        }
        $periodo->delete();
        $this->dispatch('notify', type: 'success', message: 'Periodo eliminado.');
    }

    private function resetForm()
    {
        $this->editingId   = null;
        $this->anio        = '';
        $this->ciclo       = 'I';
        $this->fecha_inicio = '';
        $this->fecha_fin   = '';
        $this->estado      = 'pendiente';
        $this->descripcion = '';
        $this->resetValidation();
    }
};
?>

<div class="p-4">

    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Periodos de Evaluación</h1>
        <button wire:click="openCreate"
            class="flex items-center gap-2 px-4 py-2 bg-ues text-white rounded-lg cursor-pointer font-medium hover:opacity-90">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Nuevo Periodo
        </button>
    </div>

    {{-- Filtro --}}
    <div class="mb-4 flex gap-2">
        <select wire:model.live="filtroEstado"
            class="border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark">
            <option value="">Todos los estados</option>
            <option value="pendiente">Pendiente</option>
            <option value="activo">Activo</option>
            <option value="cerrado">Cerrado</option>
        </select>
    </div>

    {{-- Tabla --}}
    <div class="overflow-hidden rounded-radius border border-outline dark:border-outline-dark">
        <table class="w-full text-left text-sm text-on-surface dark:text-on-surface-dark">
            <thead class="border-b border-outline bg-ues text-white dark:border-outline-dark">
                <tr>
                    <th class="p-4">Periodo</th>
                    <th class="p-4">Fecha Inicio</th>
                    <th class="p-4">Fecha Fin</th>
                    <th class="p-4">Estado</th>
                    <th class="p-4">Descripción</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline dark:divide-outline-dark">
                @forelse ($this->periodos as $periodo)
                    <tr class="hover:bg-surface-alt dark:hover:bg-surface-dark-alt">
                        <td class="p-4 font-medium">Ciclo {{ $periodo->ciclo }} - {{ $periodo->anio }}</td>
                        <td class="p-4">{{ $periodo->fecha_inicio->format('d/m/Y') }}</td>
                        <td class="p-4">{{ $periodo->fecha_fin->format('d/m/Y') }}</td>
                        <td class="p-4">
                            @php
                                $colors = [
                                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                                    'activo'    => 'bg-green-100 text-green-800',
                                    'cerrado'   => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $colors[$periodo->estado] }}">
                                {{ ucfirst($periodo->estado) }}
                            </span>
                        </td>
                        <td class="p-4 text-sm text-gray-500">{{ $periodo->descripcion ?? '-' }}</td>
                        <td class="p-4">
                            <div class="flex items-center justify-center gap-2">

                                {{-- Activar --}}
                                @if ($periodo->estado === 'pendiente')
                                    <button wire:click="cambiarEstado({{ $periodo->id }}, 'activo')"
                                        title="Activar"
                                        class="p-1 rounded text-green-600 hover:bg-green-50 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                                        </svg>
                                    </button>
                                @endif

                                {{-- Cerrar --}}
                                @if ($periodo->estado === 'activo')
                                    <button wire:click="cambiarEstado({{ $periodo->id }}, 'cerrado')"
                                        title="Cerrar periodo"
                                        class="p-1 rounded text-red-600 hover:bg-red-50 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" />
                                        </svg>
                                    </button>
                                @endif

                                {{-- Editar --}}
                                <button wire:click="openEdit({{ $periodo->id }})" title="Editar"
                                    class="p-1 rounded text-blue-600 hover:bg-blue-50 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                    </svg>
                                </button>

                                {{-- Eliminar (solo pendiente o cerrado) --}}
                                @if ($periodo->estado !== 'activo')
                                    <button wire:click="delete({{ $periodo->id }})"
                                        wire:confirm="¿Confirma que desea eliminar este periodo?"
                                        title="Eliminar"
                                        class="p-1 rounded text-gray-500 hover:bg-gray-100 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">No hay periodos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $this->periodos->links() }}
    </div>

    {{-- Modal crear / editar --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-lg p-6">

                <h2 class="text-xl font-bold mb-4">
                    {{ $editingId ? 'Editar Periodo' : 'Nuevo Periodo de Evaluación' }}
                </h2>

                <form wire:submit.prevent="save" class="flex flex-col gap-4">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="font-semibold text-sm">Año</label>
                            <input type="number" wire:model="anio" min="2000" max="2100"
                                class="w-full mt-1 p-2 border rounded-lg border-ues">
                            @error('anio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="font-semibold text-sm">Ciclo</label>
                            <select wire:model="ciclo"
                                class="w-full mt-1 p-2 border rounded-lg border-ues">
                                <option value="I">Ciclo I</option>
                                <option value="II">Ciclo II</option>
                            </select>
                            @error('ciclo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="font-semibold text-sm">Fecha Inicio</label>
                            <input type="date" wire:model="fecha_inicio"
                                class="w-full mt-1 p-2 border rounded-lg border-ues">
                            @error('fecha_inicio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="font-semibold text-sm">Fecha Fin</label>
                            <input type="date" wire:model="fecha_fin"
                                class="w-full mt-1 p-2 border rounded-lg border-ues">
                            @error('fecha_fin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="font-semibold text-sm">Estado</label>
                        <select wire:model="estado"
                            class="w-full mt-1 p-2 border rounded-lg border-ues">
                            <option value="pendiente">Pendiente</option>
                            <option value="activo">Activo</option>
                            <option value="cerrado">Cerrado</option>
                        </select>
                        @error('estado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="font-semibold text-sm">Descripción (opcional)</label>
                        <textarea wire:model="descripcion" rows="2"
                            class="w-full mt-1 p-2 border rounded-lg border-ues resize-none"></textarea>
                        @error('descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 mt-2">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="px-4 py-2 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-ues text-white rounded-lg cursor-pointer font-medium hover:opacity-90">
                            {{ $editingId ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

</div>
