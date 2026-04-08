{{--
    Partial reutilizable para los 3 cuestionarios de Labor Académica.
    Variables esperadas:
      $tipo       string  'estudiante' | 'jefe' | 'auto'
      $criterios  array   valores actuales (wire:model)
      $pesos      array   pesos por criterio (0.0 - 1.0)
      $etiquetas  array   labels para mostrar
      $wireModel  string  nombre de la propiedad Livewire
      $wireSubmit string  nombre del método Livewire
      $titulo     string  título del cuestionario
--}}

<div class="p-4 border border-outline dark:border-outline-dark rounded-xl">
    <h3 class="text-lg font-bold mb-4">{{ $titulo }}</h3>
    <p class="text-sm text-gray-500 mb-4">
        Ingrese una calificación de <strong>0 a 10</strong> (números enteros) para cada criterio.
    </p>

    <form wire:submit.prevent="{{ $wireSubmit }}">
        <div class="overflow-hidden rounded-lg border border-outline dark:border-outline-dark mb-4">
            <table class="w-full text-sm">
                <thead class="bg-surface-alt dark:bg-surface-dark-alt">
                    <tr>
                        <th class="p-3 text-left">Criterio</th>
                        <th class="p-3 text-center w-24">Peso</th>
                        <th class="p-3 text-center w-32">Calificación (0–10)</th>
                        <th class="p-3 text-center w-28">Aporte</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline dark:divide-outline-dark"
                    x-data="{
                        criterios: $wire.entangle('{{ $wireModel }}').live,
                        pesos: {{ json_encode($pesos) }},
                        get notaPonderada() {
                            let total = 0;
                            for (const [k, peso] of Object.entries(this.pesos)) {
                                total += (parseFloat(this.criterios[k]) || 0) * peso;
                            }
                            return total.toFixed(2);
                        }
                    }">
                    @foreach ($etiquetas as $key => $label)
                        <tr class="hover:bg-surface-alt/50">
                            <td class="p-3">{{ $label }}</td>
                            <td class="p-3 text-center text-gray-500">
                                {{ number_format($pesos[$key] * 100, 0) }}%
                            </td>
                            <td class="p-3 text-center">
                                <input
                                    type="number"
                                    min="0" max="10" step="1"
                                    wire:model.live="{{ $wireModel }}.{{ $key }}"
                                    class="w-20 text-center border border-ues rounded-lg p-1.5 focus:outline-none focus:ring-2 focus:ring-ues/40"
                                >
                                @error("criterios_{$tipo}.{$key}")
                                    <span class="text-red-500 text-xs block">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="p-3 text-center text-gray-600 font-mono"
                                x-text="((parseFloat(criterios['{{ $key }}']) || 0) * {{ $pesos[$key] }}).toFixed(2)">
                            </td>
                        </tr>
                    @endforeach

                    {{-- Fila total --}}
                    <tr class="bg-ues/10 font-bold">
                        <td class="p-3" colspan="2">Nota Ponderada Total</td>
                        <td class="p-3 text-center" colspan="2">
                            <span class="text-xl text-ues" x-text="notaPonderada"></span>
                            <span class="text-sm text-gray-500"> / 10</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="px-6 py-2 bg-ues text-white rounded-lg cursor-pointer font-medium hover:opacity-90">
                Guardar {{ $titulo }}
            </button>
        </div>
    </form>
</div>
