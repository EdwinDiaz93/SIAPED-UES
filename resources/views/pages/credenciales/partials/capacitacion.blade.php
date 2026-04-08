<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Formulario --}}
    <div class="lg:col-span-1 p-4 border border-outline dark:border-outline-dark rounded-xl">
        <h3 class="font-bold mb-4 text-base">
            {{ $cap_editando ? 'Editar registro' : 'Agregar capacitación' }}
        </h3>
        <form wire:submit.prevent="guardarCapacitacion" class="flex flex-col gap-3">

            <div>
                <label class="text-sm font-semibold">Tipo</label>
                <select wire:model.live="cap_tipo" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                    <option value="curso">Curso (>40 hrs)</option>
                    <option value="diplomado_maestria">Diplomado / Maestría en Docencia</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold">Nombre</label>
                <input type="text" wire:model="cap_nombre" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                @error('cap_nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-sm font-semibold">Institución</label>
                <input type="text" wire:model="cap_institucion" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
            </div>

            @if ($cap_tipo === 'curso')
            <div>
                <label class="text-sm font-semibold">Horas</label>
                <input type="number" wire:model="cap_horas" min="41" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                @error('cap_horas') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            @endif

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-sm font-semibold">Fecha inicio</label>
                    <input type="date" wire:model="cap_fecha_inicio" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                    @error('cap_fecha_inicio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="text-sm font-semibold">Fecha fin</label>
                    <input type="date" wire:model="cap_fecha_fin" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                    @error('cap_fecha_fin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex gap-2 mt-1">
                <button type="submit" class="flex-1 py-2 bg-ues text-white rounded-lg text-sm font-medium cursor-pointer hover:opacity-90">
                    {{ $cap_editando ? 'Actualizar' : 'Guardar' }}
                </button>
                @if ($cap_editando)
                    <button type="button" wire:click="resetCap" class="px-3 py-2 border rounded-lg text-sm cursor-pointer hover:bg-gray-50">
                        Cancelar
                    </button>
                @endif
            </div>
        </form>
    </div>

    {{-- Listado --}}
    <div class="lg:col-span-2">
        <div class="overflow-hidden rounded-xl border border-outline dark:border-outline-dark">
            <table class="w-full text-sm">
                <thead class="bg-ues text-white">
                    <tr>
                        <th class="p-3 text-left">Nombre</th>
                        <th class="p-3 text-center">Tipo</th>
                        <th class="p-3 text-center">Horas</th>
                        <th class="p-3 text-center">Fecha fin</th>
                        <th class="p-3 text-center">Pts</th>
                        <th class="p-3 text-center">Acc.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline dark:divide-outline-dark">
                    @forelse ($this->capacitaciones as $r)
                        <tr class="hover:bg-surface-alt/50">
                            <td class="p-3">{{ $r->nombre }}<br><span class="text-xs text-gray-400">{{ $r->institucion }}</span></td>
                            <td class="p-3 text-center text-xs">{{ $r->tipo === 'curso' ? 'Curso' : 'Diplomado/Mtr.' }}</td>
                            <td class="p-3 text-center">{{ $r->horas ?? '—' }}</td>
                            <td class="p-3 text-center">{{ $r->fecha_fin->format('m/Y') }}</td>
                            <td class="p-3 text-center font-bold text-ues">{{ number_format($r->puntaje, 2) }}</td>
                            <td class="p-3 text-center">
                                <button wire:click="editarCapacitacion({{ $r->id }})" class="text-blue-500 hover:text-blue-700 mr-2 cursor-pointer">✏</button>
                                <button wire:click="eliminarCapacitacion({{ $r->id }})" wire:confirm="¿Eliminar este registro?" class="text-red-500 hover:text-red-700 cursor-pointer">✕</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-6 text-center text-gray-400">Sin registros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="text-xs text-gray-400 mt-2">* Puntaje total (máx 3 cursos): <strong>{{ number_format($this->totalCapacitacion, 2) }} pts</strong></p>
    </div>

</div>
