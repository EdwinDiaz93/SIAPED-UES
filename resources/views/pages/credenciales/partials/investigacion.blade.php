<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Formulario (solo docente) --}}
    @unless ($esAdmin)
    <div class="lg:col-span-1 p-4 border border-outline dark:border-outline-dark rounded-xl">
        <h3 class="font-bold mb-4 text-base">{{ $inv_editando ? 'Editar' : 'Agregar' }}</h3>
        <form wire:submit.prevent="guardarInvestigacion" class="flex flex-col gap-3">

            <div>
                <label class="text-sm font-semibold">Tipo</label>
                <select wire:model.live="inv_tipo" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                    @foreach (\App\Models\CredencialInvestigacion::etiquetasTipo() as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold">Título</label>
                <input type="text" wire:model="inv_titulo" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                @error('inv_titulo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            @if ($inv_tipo === 'proyecto')
            <div>
                <label class="text-sm font-semibold">Financiamiento</label>
                <select wire:model="inv_financiamiento" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                    <option value="propio">Propio (0.3)</option>
                    <option value="institucional">Institucional (0.5)</option>
                    <option value="externo">Externo (1.0)</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold">Participación</label>
                <select wire:model="inv_participacion" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                    <option value="colaborador">Colaborador (0.5)</option>
                    <option value="investigador">Investigador (1.0)</option>
                    <option value="coordinador">Coordinador (1.5)</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold">Duración</label>
                <select wire:model="inv_duracion_proyecto" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                    <option value="lt1anio">Menos de 1 año (0.5)</option>
                    <option value="1a2anios">1 a 2 años (1.0)</option>
                    <option value="gt2anios">Más de 2 años (1.5)</option>
                </select>
            </div>
            @endif

            @if ($inv_tipo === 'publicacion')
            <div>
                <label class="text-sm font-semibold">Tipo de publicación</label>
                <select wire:model="inv_tipo_publicacion" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                    <option value="libro">Libro (3.0)</option>
                    <option value="capitulo">Capítulo de libro (2.0)</option>
                    <option value="articulo_indexado">Artículo indexado (2.5)</option>
                    <option value="articulo_no_indexado">Artículo no indexado (1.0)</option>
                </select>
            </div>
            @endif

            <div>
                <label class="text-sm font-semibold">Fecha</label>
                <input type="date" wire:model="inv_fecha" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                @error('inv_fecha') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-2 mt-1">
                <button type="submit" class="flex-1 py-2 bg-ues text-white rounded-lg text-sm font-medium cursor-pointer hover:opacity-90">
                    {{ $inv_editando ? 'Actualizar' : 'Guardar' }}
                </button>
                @if ($inv_editando)
                    <button type="button" wire:click="resetInv" class="px-3 py-2 border rounded-lg text-sm cursor-pointer hover:bg-gray-50">Cancelar</button>
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
                        <th class="p-3 text-left">Título</th>
                        <th class="p-3 text-center">Tipo</th>
                        <th class="p-3 text-center">Fecha</th>
                        <th class="p-3 text-center">Pts</th>
                        <th class="p-3 text-center">Estado</th>
                        <th class="p-3 text-center">Acc.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline dark:divide-outline-dark">
                    @forelse ($this->investigaciones as $r)
                        <tr class="hover:bg-surface-alt/50">
                            <td class="p-3">{{ $r->titulo }}</td>
                            <td class="p-3 text-center text-xs capitalize">{{ \App\Models\CredencialInvestigacion::etiquetasTipo()[$r->tipo] }}</td>
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
                                        <button wire:click="aprobarCredencial('investigacion', {{ $r->id }})" class="text-green-600 hover:text-green-800 text-xs font-medium mr-1 cursor-pointer">Aprobar</button>
                                    @endif
                                    @if ($r->estado !== 'rechazado')
                                        <button wire:click="rechazarCredencial('investigacion', {{ $r->id }})" class="text-red-500 hover:text-red-700 text-xs font-medium cursor-pointer">Rechazar</button>
                                    @endif
                                @else
                                    @if ($r->estado !== 'aprobado')
                                        <button wire:click="editarInvestigacion({{ $r->id }})" class="text-blue-500 hover:text-blue-700 mr-2 cursor-pointer">✏</button>
                                        <button wire:click="eliminarInvestigacion({{ $r->id }})" wire:confirm="¿Eliminar?" class="text-red-500 hover:text-red-700 cursor-pointer">✕</button>
                                    @else
                                        <span class="text-xs text-gray-400">Bloqueado</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-6 text-center text-gray-400">Sin registros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="text-xs text-gray-400 mt-2">* Total últimos 5 años: <strong>{{ number_format($this->totalInvestigacion, 2) }} pts</strong></p>
    </div>

</div>
