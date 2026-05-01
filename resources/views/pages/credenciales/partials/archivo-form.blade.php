{{--
    Variables esperadas:
    - $prefix: 'cap', 'proy', 'esp', 'inv', 'seg'
    - $registro: el modelo actual (cuando se edita), o null
--}}
<div class="border-t border-outline dark:border-outline-dark pt-3 mt-3">
    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Soporte documental (opcional)</p>

    <div>
        <label class="text-sm font-semibold">Archivo (PDF, máx 5 MB)</label>
        <input type="file" wire:model="{{ $prefix }}_archivo"
            accept=".pdf"
            class="w-full mt-1 text-sm text-gray-600 file:mr-3 file:py-1 file:px-3
                   file:rounded-lg file:border-0 file:text-sm file:font-medium
                   file:bg-ues file:text-white hover:file:opacity-90 cursor-pointer">
        @error($prefix . '_archivo')
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror

        {{-- Preview mientras sube --}}
        @if ($this->{$prefix . '_archivo'})
            <p class="text-xs text-green-600 mt-1">
                Archivo seleccionado: {{ $this->{$prefix . '_archivo'}->getClientOriginalName() }}
            </p>
        @elseif ($registro?->archivo_path)
            <div class="flex items-center gap-2 mt-1">
                <a href="{{ Storage::disk('public')->url($registro->archivo_path) }}"
                   target="_blank"
                   class="text-xs text-ues underline">
                    Ver archivo actual
                </a>
                <span class="text-xs text-gray-400">(subir uno nuevo lo reemplazará)</span>
            </div>
        @endif
    </div>

    <div class="mt-2">
        <label class="text-sm font-semibold">Descripción del archivo</label>
        <input type="text" wire:model="{{ $prefix }}_archivo_desc"
            placeholder="Ej: Diploma de maestría en docencia universitaria"
            class="w-full mt-1 p-2 border rounded-lg border-ues text-sm">
        @error($prefix . '_archivo_desc')
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror
    </div>
</div>
