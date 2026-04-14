<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Formulario (solo docente) --}}
    @unless ($esAdmin)
    <div class="lg:col-span-1 p-4 border border-outline dark:border-outline-dark rounded-xl">
        <h3 class="font-bold mb-4 text-base">
            {{ $proy_editando ? 'Editar proyecto' : 'Agregar proyecto' }}
        </h3>
        <form wire:submit.prevent="guardarProyeccion" class="flex flex-col gap-3">

            <div>
                <label class="text-sm font-semibold">Nombre del proyecto</label>
                <input type="text" wire:model="proy_nombre" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                @error('proy_nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-sm font-semibold">Responsabilidad</label>
                <select wire:model="proy_responsabilidad" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                    @foreach (\App\Models\CredencialProyeccionSocial::etiquetasResponsabilidad() as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold">Cobertura</label>
                <select wire:model="proy_cobertura" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                    @foreach (\App\Models\CredencialProyeccionSocial::etiquetasCobertura() as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold">Duración</label>
                <select wire:model="proy_duracion" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                    @foreach (\App\Models\CredencialProyeccionSocial::etiquetasDuracion() as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-sm font-semibold">Fecha inicio</label>
                    <input type="date" wire:model="proy_fecha_inicio" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                    @error('proy_fecha_inicio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="text-sm font-semibold">Fecha fin</label>
                    <input type="date" wire:model="proy_fecha_fin" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                    @error('proy_fecha_fin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            @include('pages.credenciales.partials.archivo-form', [
                'prefix'   => 'proy',
                'registro' => $proy_editando ? \App\Models\CredencialProyeccionSocial::find($proy_editando) : null,
            ])

            <div class="flex gap-2 mt-3">
                <button type="submit" class="flex-1 py-2 bg-ues text-white rounded-lg text-sm font-medium cursor-pointer hover:opacity-90">
                    {{ $proy_editando ? 'Actualizar' : 'Guardar' }}
                </button>
                @if ($proy_editando)
                    <button type="button" wire:click="resetProy" class="px-3 py-2 border rounded-lg text-sm cursor-pointer hover:bg-gray-50">Cancelar</button>
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
                        <th class="p-3 text-left">Proyecto</th>
                        <th class="p-3 text-center">Resp.</th>
                        <th class="p-3 text-center">Cobert.</th>
                        <th class="p-3 text-center">Duración</th>
                        <th class="p-3 text-center">Pts</th>
                        <th class="p-3 text-center">Estado</th>
                        <th class="p-3 text-center">Archivo</th>
                        <th class="p-3 text-center">Acc.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline dark:divide-outline-dark">
                    @forelse ($this->proyecciones as $r)
                        <tr class="hover:bg-surface-alt/50">
                            <td class="p-3">{{ $r->nombre }}</td>
                            <td class="p-3 text-center text-xs capitalize">{{ $r->responsabilidad }}</td>
                            <td class="p-3 text-center text-xs capitalize">{{ $r->cobertura }}</td>
                            <td class="p-3 text-center text-xs">{{ \App\Models\CredencialProyeccionSocial::etiquetasDuracion()[$r->duracion] }}</td>
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
                            <td class="p-3 text-center">
                                @include('pages.credenciales.partials.archivo-cell', ['r' => $r])
                            </td>
                            <td class="p-3 text-center whitespace-nowrap">
                                @if ($esAdmin)
                                    @if ($r->estado !== 'aprobado')
                                        <button wire:click="aprobarCredencial('proyeccion', {{ $r->id }})" class="text-green-600 hover:text-green-800 text-xs font-medium mr-1 cursor-pointer">Aprobar</button>
                                    @endif
                                    @if ($r->estado !== 'rechazado')
                                        <button wire:click="rechazarCredencial('proyeccion', {{ $r->id }})" class="text-red-500 hover:text-red-700 text-xs font-medium cursor-pointer">Rechazar</button>
                                    @endif
                                @else
                                    @if ($r->estado !== 'aprobado')
                                        <button wire:click="editarProyeccion({{ $r->id }})" class="text-blue-500 hover:text-blue-700 mr-2 cursor-pointer">✏</button>
                                        <button wire:click="eliminarProyeccion({{ $r->id }})" wire:confirm="¿Eliminar?" class="text-red-500 hover:text-red-700 cursor-pointer">✕</button>
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
        <p class="text-xs text-gray-400 mt-2">* Puntaje total (máx 3 proyectos): <strong>{{ number_format($this->totalProyeccion, 2) }} pts</strong></p>
    </div>

</div>
