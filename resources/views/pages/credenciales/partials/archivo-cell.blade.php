{{-- $r = el registro con archivo_path y archivo_descripcion --}}
@if ($r->archivo_path)
    <button
        type="button"
        @click="$dispatch('abrir-pdf', { url: '{{ Storage::disk('public')->url($r->archivo_path) }}', titulo: '{{ addslashes($r->archivo_descripcion ?? 'Documento') }}' })"
        title="{{ $r->archivo_descripcion ?? 'Ver PDF' }}"
        class="inline-flex items-center gap-1 text-xs text-ues underline hover:opacity-80 cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
        </svg>
        {{ Str::limit($r->archivo_descripcion ?? 'Ver PDF', 20) }}
    </button>
@else
    <span class="text-xs text-gray-400">—</span>
@endif
