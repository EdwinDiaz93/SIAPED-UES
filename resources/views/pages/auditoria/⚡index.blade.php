<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\AuditLog;

new class extends Component {
    use WithPagination;

    #[Url]
    public string $filtroTabla  = '';

    #[Url]
    public string $filtroAccion = '';

    #[Url]
    public string $filtroUsuario = '';

    #[Url]
    public string $desde = '';

    #[Url]
    public string $hasta = '';

    public ?int $detalleId = null;

    #[Computed]
    public function logs()
    {
        return AuditLog::with('user')
            ->when($this->filtroTabla, fn ($q) => $q->where('table_name', $this->filtroTabla))
            ->when($this->filtroAccion, fn ($q) => $q->where('action', $this->filtroAccion))
            ->when($this->filtroUsuario, fn ($q) => $q->where('user_id', $this->filtroUsuario))
            ->when($this->desde, fn ($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn ($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    #[Computed]
    public function tablas()
    {
        return AuditLog::query()->select('table_name')->distinct()->orderBy('table_name')->pluck('table_name');
    }

    #[Computed]
    public function usuarios()
    {
        return \App\Models\User::whereIn('id', AuditLog::query()->select('user_id')->distinct())
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function detalle()
    {
        return $this->detalleId ? AuditLog::with('user')->find($this->detalleId) : null;
    }

    public function verDetalle(int $id)
    {
        $this->detalleId = $id;
    }

    public function cerrarDetalle()
    {
        $this->detalleId = null;
    }

    public function updatedFiltroTabla()   { $this->resetPage(); }
    public function updatedFiltroAccion()  { $this->resetPage(); }
    public function updatedFiltroUsuario() { $this->resetPage(); }
    public function updatedDesde()         { $this->resetPage(); }
    public function updatedHasta()         { $this->resetPage(); }
};
?>

<div class="p-4">

    <h1 class="text-2xl font-bold mb-6">Bitácora de Auditoría</h1>

    {{-- Filtros --}}
    <div class="mb-4 grid grid-cols-2 gap-3 md:grid-cols-5">
        <div>
            <label class="text-xs font-semibold">Tabla</label>
            <select wire:model.live="filtroTabla"
                class="w-full mt-1 border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark">
                <option value="">Todas</option>
                @foreach ($this->tablas as $tabla)
                    <option value="{{ $tabla }}">{{ $tabla }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold">Acción</label>
            <select wire:model.live="filtroAccion"
                class="w-full mt-1 border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark">
                <option value="">Todas</option>
                <option value="CREATE">Creación</option>
                <option value="EDIT">Edición</option>
                <option value="DELETE">Eliminación</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold">Usuario</label>
            <select wire:model.live="filtroUsuario"
                class="w-full mt-1 border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark">
                <option value="">Todos</option>
                @foreach ($this->usuarios as $usuario)
                    <option value="{{ $usuario->id }}">{{ $usuario->name }} {{ $usuario->apellidos }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold">Desde</label>
            <input type="date" wire:model.live="desde"
                class="w-full mt-1 border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark">
        </div>
        <div>
            <label class="text-xs font-semibold">Hasta</label>
            <input type="date" wire:model.live="hasta"
                class="w-full mt-1 border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark">
        </div>
    </div>

    {{-- Tabla --}}
    <div class="overflow-hidden rounded-radius border border-outline dark:border-outline-dark">
        <table class="w-full text-left text-sm text-on-surface dark:text-on-surface-dark">
            <thead class="border-b border-outline bg-ues text-white dark:border-outline-dark">
                <tr>
                    <th class="p-4">Fecha</th>
                    <th class="p-4">Usuario</th>
                    <th class="p-4">Acción</th>
                    <th class="p-4">Tabla</th>
                    <th class="p-4">Registro</th>
                    <th class="p-4 text-center">Detalle</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline dark:divide-outline-dark">
                @forelse ($this->logs as $log)
                    <tr class="hover:bg-surface-alt dark:hover:bg-surface-dark-alt">
                        <td class="p-4 text-xs">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td class="p-4">{{ $log->user?->name }} {{ $log->user?->apellidos }}</td>
                        <td class="p-4">
                            @php
                                $colors = [
                                    'CREATE' => 'bg-green-100 text-green-800',
                                    'EDIT'   => 'bg-yellow-100 text-yellow-800',
                                    'DELETE' => 'bg-red-100 text-red-800',
                                ];
                                $etiquetas = [
                                    'CREATE' => 'Creación',
                                    'EDIT'   => 'Edición',
                                    'DELETE' => 'Eliminación',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $colors[$log->action] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $etiquetas[$log->action] ?? $log->action }}
                            </span>
                        </td>
                        <td class="p-4 text-xs font-mono">{{ $log->table_name }}</td>
                        <td class="p-4 text-xs font-mono">#{{ $log->record_id }}</td>
                        <td class="p-4 text-center">
                            <button wire:click="verDetalle({{ $log->id }})" title="Ver detalle"
                                class="p-1 rounded text-blue-600 hover:bg-blue-50 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">No hay registros en la bitácora.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $this->logs->links() }}
    </div>

    {{-- Modal de detalle --}}
    @if ($detalleId && $this->detalle)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-2xl p-6">

                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold">
                        {{ ['CREATE' => 'Creación', 'EDIT' => 'Edición', 'DELETE' => 'Eliminación'][$this->detalle->action] ?? $this->detalle->action }} — {{ $this->detalle->table_name }} #{{ $this->detalle->record_id }}
                    </h2>
                    <button wire:click="cerrarDetalle" class="text-gray-400 hover:text-gray-600 cursor-pointer">✕</button>
                </div>

                <p class="text-sm text-gray-500 mb-4">
                    {{ $this->detalle->user?->name }} {{ $this->detalle->user?->apellidos }}
                    — {{ $this->detalle->created_at->format('d/m/Y H:i:s') }}
                </p>

                <div class="grid grid-cols-2 gap-4 max-h-96 overflow-y-auto">
                    <div>
                        <h3 class="font-semibold text-sm mb-1">Valor anterior</h3>
                        <pre class="text-xs bg-red-50 border border-red-100 rounded-lg p-3 whitespace-pre-wrap break-words">{{ $this->detalle->old_value ? json_encode($this->detalle->old_value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '—' }}</pre>
                    </div>
                    <div>
                        <h3 class="font-semibold text-sm mb-1">Valor nuevo</h3>
                        <pre class="text-xs bg-green-50 border border-green-100 rounded-lg p-3 whitespace-pre-wrap break-words">{{ $this->detalle->new_value ? json_encode($this->detalle->new_value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '—' }}</pre>
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button wire:click="cerrarDetalle"
                        class="px-4 py-2 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700 text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
