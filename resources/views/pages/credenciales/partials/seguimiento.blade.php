<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Formulario (solo docente) --}}
    @unless ($esAdmin)
    <div class="lg:col-span-1 p-4 border border-outline dark:border-outline-dark rounded-xl">
        <h3 class="font-bold mb-4 text-base">{{ $seg_editando ? 'Editar' : 'Agregar' }}</h3>
        <form wire:submit.prevent="guardarSeguimiento" class="flex flex-col gap-3">

            <div>
                <label class="text-sm font-semibold">Tipo</label>
                <select wire:model.live="seg_tipo" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                    <option value="grado_adicional">Grado académico adicional (3 pts)</option>
                    <option value="curso">Curso 20–60 hrs (0.5 pts)</option>
                    <option value="coordinacion_comision">Coordinación / Comisión (1 pt)</option>
                    <option value="idioma">Idioma (1 pt)</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold">Descripción</label>
                <input type="text" wire:model="seg_descripcion" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                @error('seg_descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            @if ($seg_tipo === 'curso')
            <div>
                <label class="text-sm font-semibold">Horas (20–60)</label>
                <input type="number" wire:model="seg_horas" min="20" max="60" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                @error('seg_horas') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            @endif

            <div>
                <label class="text-sm font-semibold">Fecha</label>
                <input type="date" wire:model="seg_fecha" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                @error('seg_fecha') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-2 mt-1">
                <button type="submit" class="flex-1 py-2 bg-ues text-white rounded-lg text-sm font-medium cursor-pointer hover:opacity-90">
                    {{ $seg_editando ? 'Actualizar' : 'Guardar' }}
                </button>
                @if ($seg_editando)
                    <button type="button" wire:click="resetSeg" class="px-3 py-2 border rounded-lg text-sm cursor-pointer hover:bg-gray-50">Cancelar</button>
                @endif
            </div>
        </form>
    </div>
    @endunless

    {{-- Listado --}}
    <div class="{{ $esAdmin ? 'lg:col-span-3' : 'lg:col-span-2' }}">
        <div class="overflow-hidden rounded-xl border border-outline dark:border-outline-dark">
            <table class="w-full text-sm">
                <thead class="bg-ues text-white">
                    <tr>
                        <th class="p-3 text-left">Descripción</th>
                        <th class="p-3 text-center">Tipo</th>
                        <th class="p-3 text-center">Horas</th>
                        <th class="p-3 text-center">Fecha</th>
                        <th class="p-3 text-center">Pts</th>
                        <th class="p-3 text-center">Estado</th>
                        <th class="p-3 text-center">Acc.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline dark:divide-outline-dark">
                    @forelse ($this->seguimientos as $r)
                        @php
                            $tipos = ['grado_adicional' => 'Grado', 'curso' => 'Curso', 'coordinacion_comision' => 'Coord./Comisión', 'idioma' => 'Idioma'];
                        @endphp
                        <tr class="hover:bg-surface-alt/50">
                            <td class="p-3">{{ $r->descripcion }}</td>
                            <td class="p-3 text-center text-xs">{{ $tipos[$r->tipo] ?? $r->tipo }}</td>
                            <td class="p-3 text-center">{{ $r->horas ?? '—' }}</td>
                            <td class="p-3 text-center">{{ $r->fecha->format('m/Y') }}</td>
                            <td class="p-3 text-center font-bold text-ues">{{ number_format($r->puntaje, 2) }}</td>
                            <td class="p-3 text-center">
                                @if ($r->estado === 'aprobado')
                                    <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700 font-medium">Aprobado</span>
                                @elseif ($r->estado === 'rechazado')
                                    <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700 font-medium">Rechazado</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700 font-medium">Pendiente</span>
                                @endif
                            </td>
                            <td class="p-3 text-center whitespace-nowrap">
                                @if ($esAdmin)
                                    @if ($r->estado !== 'aprobado')
                                        <button wire:click="aprobarCredencial('seguimiento', {{ $r->id }})" class="text-green-600 hover:text-green-800 text-xs font-medium mr-1 cursor-pointer">Aprobar</button>
                                    @endif
                                    @if ($r->estado !== 'rechazado')
                                        <button wire:click="rechazarCredencial('seguimiento', {{ $r->id }})" class="text-red-500 hover:text-red-700 text-xs font-medium cursor-pointer">Rechazar</button>
                                    @endif
                                @else
                                    @if ($r->estado !== 'aprobado')
                                        <button wire:click="editarSeguimiento({{ $r->id }})" class="text-blue-500 hover:text-blue-700 mr-2 cursor-pointer">✏</button>
                                        <button wire:click="eliminarSeguimiento({{ $r->id }})" wire:confirm="¿Eliminar?" class="text-red-500 hover:text-red-700 cursor-pointer">✕</button>
                                    @else
                                        <span class="text-xs text-gray-400">Bloqueado</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-6 text-center text-gray-400">Sin registros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="text-xs text-gray-400 mt-2">* Total (cursos acumulan máx 2 pts): <strong>{{ number_format($this->totalSeguimiento, 2) }} pts</strong></p>
    </div>

</div>
