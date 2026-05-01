<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Formulario (solo docente) --}}
    @unless ($esAdmin)
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

            @include('pages.credenciales.partials.archivo-form', [
                'prefix'   => 'cap',
                'registro' => $cap_editando ? \App\Models\CredencialCapacitacion::find($cap_editando) : null,
            ])

            <div class="flex gap-2 mt-3">
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
    @endunless

    {{-- Listado --}}
    <div class="{{ $esAdmin ? 'lg:col-span-3' : 'lg:col-span-2' }}">
        <div class="overflow-hidden rounded-xl border border-outline dark:border-outline-dark">
            <table class="w-full text-sm">
                <thead class="bg-ues text-white">
                    <tr>
                        <th class="p-3 text-left">Nombre</th>
                        <th class="p-3 text-center">Tipo</th>
                        <th class="p-3 text-center">Horas</th>
                        <th class="p-3 text-center">Fecha fin</th>
                        <th class="p-3 text-center">Pts</th>
                        <th class="p-3 text-center">Estado</th>
                        <th class="p-3 text-center">Archivo</th>
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
                                @include('pages.credenciales.partials.archivo-cell', ['r' => $r])
                            </td>
                            <td class="p-3 text-center">
                                @if ($r->estado === 'aprobado')
                                    <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700 font-medium">Aprobado</span>
                                @elseif ($r->estado === 'rechazado')
                                    <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700 font-medium">Rechazado</span>
                                    @if ($r->comentario_rechazo)
                                        <p class="text-xs text-red-600 mt-1 text-left max-w-[10rem]" title="{{ $r->comentario_rechazo }}">{{ Str::limit($r->comentario_rechazo, 60) }}</p>
                                    @endif
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700 font-medium">Pendiente</span>
                                    @if ($r->comentario)
                                        <p class="text-xs text-gray-400 mt-1 text-left max-w-[10rem]" title="{{ $r->comentario }}">{{ Str::limit($r->comentario, 60) }}</p>
                                    @endif
                                @endif
                            </td>
                            <td class="p-3 text-center whitespace-nowrap">
                                @if ($esAdmin)
                                    @if ($r->estado !== 'aprobado')
                                        <button wire:click="aprobarCredencial('capacitacion', {{ $r->id }})" class="text-green-600 hover:text-green-800 text-xs font-medium mr-1 cursor-pointer">Aprobar</button>
                                    @endif
                                    @if ($r->estado !== 'rechazado')
                                        <button wire:click="rechazarCredencial('capacitacion', {{ $r->id }})" class="text-red-500 hover:text-red-700 text-xs font-medium cursor-pointer">Rechazar</button>
                                    @endif
                                @else
                                    @if ($r->estado !== 'aprobado')
                                        <button wire:click="editarCapacitacion({{ $r->id }})" class="text-blue-500 hover:text-blue-700 mr-2 cursor-pointer">✏</button>
                                        <button wire:click="eliminarCapacitacion({{ $r->id }})" wire:confirm="¿Eliminar este registro?" class="text-red-500 hover:text-red-700 cursor-pointer">✕</button>
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
        <p class="text-xs text-gray-400 mt-2">* Puntaje total (máx 3 cursos): <strong>{{ number_format($this->totalCapacitacion, 2) }} pts</strong></p>
    </div>

</div>
