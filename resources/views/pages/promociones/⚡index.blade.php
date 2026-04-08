<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\SolicitudPromocion;
use App\Models\CatalogValue;
use App\Models\Institution;

new class extends Component {
    use WithPagination;

    public string $filtroEstado     = 'pendiente';
    public bool   $showModal        = false;
    public ?int   $solicitudActiva  = null;
    public string $observaciones    = '';
    public string $accion           = ''; // 'aprobar' | 'rechazar'

    #[Computed]
    public function solicitudes()
    {
        return SolicitudPromocion::with([
            'docente.institution.categoria',
            'docente.institution.escuela',
            'periodo',
            'revisadoPor',
        ])
        ->when($this->filtroEstado, fn($q) => $q->where('estado', $this->filtroEstado))
        ->orderByDesc('created_at')
        ->paginate(15);
    }

    #[Computed]
    public function contadores()
    {
        return [
            'pendiente'  => SolicitudPromocion::where('estado', 'pendiente')->count(),
            'aprobada'   => SolicitudPromocion::where('estado', 'aprobada')->count(),
            'rechazada'  => SolicitudPromocion::where('estado', 'rechazada')->count(),
        ];
    }

    public function updatedFiltroEstado() { $this->resetPage(); }

    public function abrirRevision(int $id, string $accion)
    {
        $this->solicitudActiva = $id;
        $this->accion          = $accion;
        $this->observaciones   = '';
        $this->showModal       = true;
    }

    public function confirmarRevision()
    {
        $solicitud = SolicitudPromocion::findOrFail($this->solicitudActiva);

        if ($solicitud->estado !== 'pendiente') {
            $this->dispatch('notify', type: 'error', message: 'Esta solicitud ya fue procesada.');
            $this->showModal = false;
            return;
        }

        $nuevoEstado = $this->accion === 'aprobar' ? 'aprobada' : 'rechazada';

        $solicitud->update([
            'estado'         => $nuevoEstado,
            'revisado_por'   => auth()->id(),
            'fecha_revision' => now(),
            'observaciones'  => $this->observaciones ?: null,
        ]);

        // Si se aprueba → actualizar categoría del docente
        if ($nuevoEstado === 'aprobada') {
            $solicitud->ejecutarPromocion();

            // Notificar al docente por email
            try {
                \Illuminate\Support\Facades\Mail::to($solicitud->docente->email)
                    ->send(new \App\Mail\PromocionAprobadaMail($solicitud));
            } catch (\Throwable) {
                // Mail falla silenciosamente para no bloquear el flujo
            }
        }

        $this->showModal = false;
        $this->dispatch('notify', type: 'success',
            message: 'Solicitud ' . ($nuevoEstado === 'aprobada' ? 'aprobada' : 'rechazada') . ' correctamente.');
    }

    public function verFormulario(int $docenteId, ?int $periodoId = null)
    {
        $this->redirectRoute('formulario.show', [
            'docenteId' => $docenteId,
            'periodoId' => $periodoId,
        ]);
    }
};
?>

<div class="p-4">

    <h1 class="text-2xl font-bold mb-6">Gestión de Promociones Escalafonarias</h1>

    {{-- Contadores --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        @foreach ([
            ['pendiente', 'Pendientes',  'bg-yellow-100 text-yellow-800 border-yellow-300'],
            ['aprobada',  'Aprobadas',   'bg-green-100 text-green-800 border-green-300'],
            ['rechazada', 'Rechazadas',  'bg-red-100 text-red-800 border-red-300'],
        ] as [$key, $label, $cls])
            <button wire:click="$set('filtroEstado', '{{ $key }}')"
                class="p-4 rounded-xl border text-center cursor-pointer transition-all
                    {{ $filtroEstado === $key ? $cls . ' ring-2 ring-offset-1' : 'border-outline dark:border-outline-dark hover:opacity-80' }}">
                <p class="text-2xl font-bold">{{ $this->contadores[$key] }}</p>
                <p class="text-sm font-medium">{{ $label }}</p>
            </button>
        @endforeach
    </div>

    {{-- Tabla de solicitudes --}}
    <div class="overflow-hidden rounded-xl border border-outline dark:border-outline-dark">
        <table class="w-full text-sm">
            <thead class="bg-ues text-white">
                <tr>
                    <th class="p-3 text-left">Docente</th>
                    <th class="p-3 text-center">Categoría Actual</th>
                    <th class="p-3 text-center">Solicita</th>
                    <th class="p-3 text-center">Puntaje</th>
                    <th class="p-3 text-center">Periodo</th>
                    <th class="p-3 text-center">Fecha Solicitud</th>
                    <th class="p-3 text-center">Estado</th>
                    @if ($filtroEstado !== 'pendiente')
                        <th class="p-3 text-center">Revisado por</th>
                        <th class="p-3 text-center">Observaciones</th>
                    @endif
                    <th class="p-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline dark:divide-outline-dark">
                @forelse ($this->solicitudes as $sol)
                    <tr class="hover:bg-surface-alt/40">
                        <td class="p-3">
                            <p class="font-medium">{{ $sol->docente->name }} {{ $sol->docente->apellidos }}</p>
                            <p class="text-xs text-gray-400">{{ $sol->docente->institution?->escuela?->name ?? '—' }}</p>
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-700 uppercase">
                                {{ $sol->categoria_actual }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-ues/10 text-ues uppercase">
                                {{ $sol->categoria_solicitada }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <p class="font-bold {{ $sol->puntaje_obtenido >= $sol->puntaje_requerido ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($sol->puntaje_obtenido, 2) }}
                            </p>
                            <p class="text-xs text-gray-400">/ {{ number_format($sol->puntaje_requerido, 2) }} requerido</p>
                        </td>
                        <td class="p-3 text-center text-xs">
                            {{ $sol->periodo ? 'Ciclo ' . $sol->periodo->ciclo . ' - ' . $sol->periodo->anio : '—' }}
                        </td>
                        <td class="p-3 text-center text-xs">
                            {{ $sol->created_at->format('d/m/Y') }}
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $sol->badge_color }}">
                                {{ ucfirst($sol->estado) }}
                            </span>
                        </td>
                        @if ($filtroEstado !== 'pendiente')
                            <td class="p-3 text-center text-xs">
                                {{ $sol->revisadoPor?->name ?? '—' }}<br>
                                <span class="text-gray-400">{{ $sol->fecha_revision?->format('d/m/Y') }}</span>
                            </td>
                            <td class="p-3 text-xs text-gray-500 max-w-xs">{{ $sol->observaciones ?? '—' }}</td>
                        @endif
                        <td class="p-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                {{-- Ver formulario --}}
                                <button wire:click="verFormulario({{ $sol->docente_id }}, {{ $sol->periodo_id ?? 'null' }})"
                                    title="Ver formulario consolidado"
                                    class="p-1 rounded text-blue-600 hover:bg-blue-50 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                    </svg>
                                </button>

                                @if ($sol->estado === 'pendiente')
                                    {{-- Aprobar --}}
                                    <button wire:click="abrirRevision({{ $sol->id }}, 'aprobar')"
                                        title="Aprobar promoción"
                                        class="p-1 rounded text-green-600 hover:bg-green-50 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    </button>
                                    {{-- Rechazar --}}
                                    <button wire:click="abrirRevision({{ $sol->id }}, 'rechazar')"
                                        title="Rechazar solicitud"
                                        class="p-1 rounded text-red-600 hover:bg-red-50 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="p-8 text-center text-gray-400">
                            No hay solicitudes {{ $filtroEstado === 'pendiente' ? 'pendientes' : ($filtroEstado === 'aprobada' ? 'aprobadas' : 'rechazadas') }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $this->solicitudes->links() }}</div>

    {{-- Modal de confirmación --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-md p-6">

                @php $sol = \App\Models\SolicitudPromocion::find($solicitudActiva); @endphp

                <h2 class="text-xl font-bold mb-1">
                    {{ $accion === 'aprobar' ? '✓ Aprobar Promoción' : '✗ Rechazar Solicitud' }}
                </h2>
                @if ($sol)
                    <p class="text-sm text-gray-500 mb-4">
                        <strong>{{ $sol->docente->name }} {{ $sol->docente->apellidos }}</strong>
                        — {{ $sol->categoria_actual }} → {{ $sol->categoria_solicitada }}
                    </p>

                    @if ($accion === 'aprobar')
                        <div class="p-3 mb-4 rounded-lg bg-green-50 border border-green-200 text-sm text-green-800">
                            Al aprobar, la categoría del docente se actualizará automáticamente a
                            <strong>{{ $sol->categoria_solicitada }}</strong>.
                        </div>
                    @else
                        <div class="p-3 mb-4 rounded-lg bg-red-50 border border-red-200 text-sm text-red-800">
                            Al rechazar, la categoría del docente no cambiará. Puede volver a solicitar la promoción.
                        </div>
                    @endif
                @endif

                <div class="mb-4">
                    <label class="text-sm font-semibold block mb-1">
                        Observaciones {{ $accion === 'rechazar' ? '(requeridas)' : '(opcional)' }}
                    </label>
                    <textarea wire:model="observaciones" rows="3"
                        class="w-full p-2 border rounded-lg border-outline resize-none text-sm
                            {{ $accion === 'rechazar' ? 'border-red-300' : 'border-ues' }}"
                        placeholder="{{ $accion === 'rechazar' ? 'Indique el motivo del rechazo...' : 'Comentarios adicionales...' }}">
                    </textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$set('showModal', false)"
                        class="px-4 py-2 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700 text-sm">
                        Cancelar
                    </button>
                    <button wire:click="confirmarRevision"
                        @if ($accion === 'rechazar') wire:confirm="¿Confirma el rechazo de esta solicitud?" @endif
                        class="px-4 py-2 rounded-lg cursor-pointer text-sm font-medium text-white
                            {{ $accion === 'aprobar' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}">
                        {{ $accion === 'aprobar' ? 'Sí, Aprobar' : 'Sí, Rechazar' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
