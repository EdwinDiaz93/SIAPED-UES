<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-1 p-4 border border-outline dark:border-outline-dark rounded-xl">
        <h3 class="font-bold mb-4 text-base">{{ $esp_editando ? 'Editar' : 'Agregar' }}</h3>
        <form wire:submit.prevent="guardarEspecializacion" class="flex flex-col gap-3">

            <div>
                <label class="text-sm font-semibold">Tipo</label>
                <select wire:model.live="esp_tipo" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                    <option value="phd">Doctorado (PhD) — 12 pts</option>
                    <option value="maestria">Maestría — 10 pts</option>
                    <option value="curso">Curso (60–240+ hrs)</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold">Título / Nombre</label>
                <input type="text" wire:model="esp_titulo" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                @error('esp_titulo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-sm font-semibold">Institución</label>
                <input type="text" wire:model="esp_institucion" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
            </div>

            @if ($esp_tipo === 'curso')
            <div>
                <label class="text-sm font-semibold">Horas (mín. 60)</label>
                <input type="number" wire:model="esp_horas" min="60" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                @error('esp_horas') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            @endif

            <div>
                <label class="text-sm font-semibold">Fecha de obtención</label>
                <input type="date" wire:model="esp_fecha" class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
                @error('esp_fecha') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-2 mt-1">
                <button type="submit" class="flex-1 py-2 bg-ues text-white rounded-lg text-sm font-medium cursor-pointer hover:opacity-90">
                    {{ $esp_editando ? 'Actualizar' : 'Guardar' }}
                </button>
                @if ($esp_editando)
                    <button type="button" wire:click="resetEsp" class="px-3 py-2 border rounded-lg text-sm cursor-pointer hover:bg-gray-50">Cancelar</button>
                @endif
            </div>
        </form>
    </div>

    <div class="lg:col-span-2">
        <div class="overflow-hidden rounded-xl border border-outline dark:border-outline-dark">
            <table class="w-full text-sm">
                <thead class="bg-ues text-white">
                    <tr>
                        <th class="p-3 text-left">Título</th>
                        <th class="p-3 text-center">Tipo</th>
                        <th class="p-3 text-center">Horas</th>
                        <th class="p-3 text-center">Fecha</th>
                        <th class="p-3 text-center">Pts</th>
                        <th class="p-3 text-center">Acc.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline dark:divide-outline-dark">
                    @forelse ($this->especializaciones as $r)
                        <tr class="hover:bg-surface-alt/50">
                            <td class="p-3">{{ $r->titulo }}<br><span class="text-xs text-gray-400">{{ $r->institucion }}</span></td>
                            <td class="p-3 text-center text-xs uppercase">{{ $r->tipo }}</td>
                            <td class="p-3 text-center">{{ $r->horas ?? '—' }}</td>
                            <td class="p-3 text-center">{{ $r->fecha->format('m/Y') }}</td>
                            <td class="p-3 text-center font-bold text-ues">{{ number_format($r->puntaje, 2) }}</td>
                            <td class="p-3 text-center">
                                <button wire:click="editarEspecializacion({{ $r->id }})" class="text-blue-500 hover:text-blue-700 mr-2 cursor-pointer">✏</button>
                                <button wire:click="eliminarEspecializacion({{ $r->id }})" wire:confirm="¿Eliminar?" class="text-red-500 hover:text-red-700 cursor-pointer">✕</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-6 text-center text-gray-400">Sin registros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="text-xs text-gray-400 mt-2">* Total (grados siempre válidos, cursos últimos 5 años máx 3): <strong>{{ number_format($this->totalEspecializacion, 2) }} pts</strong></p>
    </div>

</div>
