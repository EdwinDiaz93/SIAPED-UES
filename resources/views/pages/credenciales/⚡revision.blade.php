<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\User;
use Illuminate\Support\Facades\DB;

new class extends Component {

    public string $busqueda = '';

    #[Computed]
    public function docentes()
    {
        // Docentes que tienen al menos un registro en cualquier tabla de credenciales
        $tablas = [
            'credenciales_capacitacion',
            'credenciales_proyeccion_social',
            'credenciales_especializacion',
            'credenciales_investigacion',
            'credenciales_seguimiento',
        ];

        // IDs de docentes que tienen credenciales
        $idsConCredenciales = collect();
        foreach ($tablas as $tabla) {
            $ids = DB::table($tabla)->pluck('docente_id');
            $idsConCredenciales = $idsConCredenciales->merge($ids);
        }
        $idsConCredenciales = $idsConCredenciales->unique()->values();

        $query = User::whereIn('id', $idsConCredenciales)
            ->whereHas('roles', fn($q) => $q->where('name', 'docente'));

        if ($this->busqueda) {
            $query->where(function ($q) {
                $q->where('name', 'ilike', '%' . $this->busqueda . '%')
                  ->orWhere('apellidos', 'ilike', '%' . $this->busqueda . '%')
                  ->orWhere('email', 'ilike', '%' . $this->busqueda . '%');
            });
        }

        return $query->get()->map(function ($docente) use ($tablas) {
            $pendientes = 0;
            $aprobadas  = 0;
            $rechazadas = 0;
            $total      = 0;

            foreach ($tablas as $tabla) {
                $registros = DB::table($tabla)->where('docente_id', $docente->id)->get();
                $total      += $registros->count();
                $pendientes += $registros->where('estado', 'pendiente')->count();
                $aprobadas  += $registros->where('estado', 'aprobado')->count();
                $rechazadas += $registros->where('estado', 'rechazado')->count();
            }

            $docente->total_credenciales  = $total;
            $docente->pendientes          = $pendientes;
            $docente->aprobadas           = $aprobadas;
            $docente->rechazadas          = $rechazadas;

            return $docente;
        });
    }
};
?>

<div class="p-4">

    <h1 class="text-2xl font-bold mb-1">Revisión de Credenciales</h1>
    <p class="text-sm text-gray-500 mb-6">Docentes que han ingresado credenciales escalafonarias pendientes de revisión.</p>

    {{-- Buscador --}}
    <div class="mb-4 max-w-sm">
        <input type="text" wire:model.live.debounce.300ms="busqueda"
            placeholder="Buscar por nombre o email..."
            class="w-full p-2 border rounded-lg border-outline text-sm">
    </div>

    <div class="overflow-hidden rounded-xl border border-outline dark:border-outline-dark">
        <table class="w-full text-sm">
            <thead class="bg-ues text-white">
                <tr>
                    <th class="p-3 text-left">Docente</th>
                    <th class="p-3 text-center">Total</th>
                    <th class="p-3 text-center">Pendientes</th>
                    <th class="p-3 text-center">Aprobadas</th>
                    <th class="p-3 text-center">Rechazadas</th>
                    <th class="p-3 text-center">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline dark:divide-outline-dark">
                @forelse ($this->docentes as $docente)
                    <tr class="hover:bg-surface-alt/50">
                        <td class="p-3">
                            <p class="font-medium">{{ $docente->name }} {{ $docente->apellidos }}</p>
                            <p class="text-xs text-gray-400">{{ $docente->email }}</p>
                        </td>
                        <td class="p-3 text-center font-bold">{{ $docente->total_credenciales }}</td>
                        <td class="p-3 text-center">
                            @if ($docente->pendientes > 0)
                                <span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700 font-medium">
                                    {{ $docente->pendientes }}
                                </span>
                            @else
                                <span class="text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700 font-medium">
                                {{ $docente->aprobadas }}
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            @if ($docente->rechazadas > 0)
                                <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700 font-medium">
                                    {{ $docente->rechazadas }}
                                </span>
                            @else
                                <span class="text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            <a href="{{ route('credenciales', ['docenteId' => $docente->id]) }}"
                               wire:navigate
                               class="px-3 py-1.5 bg-ues text-white rounded-lg text-xs font-medium hover:opacity-90">
                                Revisar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">
                            No hay docentes con credenciales registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
