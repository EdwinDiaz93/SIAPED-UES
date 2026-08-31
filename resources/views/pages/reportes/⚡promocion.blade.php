<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\User;
use App\Models\CatalogType;
use App\Services\PuntajeEscalafonarioCalculator;
use Spatie\Permission\Models\Role;
use Barryvdh\DomPDF\Facade\Pdf;

new class extends Component {

    public string $filtroEscuela = '';
    public string $filtroEstado  = 'aptos'; // 'aptos' | 'no_aptos' | 'todos'

    #[Computed]
    public function escuelas()
    {
        return CatalogType::where('value', 'Escuelas')->first()
            ?->catalogValues ?? collect();
    }

    #[Computed]
    public function docentes()
    {
        $rolDocente = Role::where('name', 'docente')->first();
        if (!$rolDocente) return collect();

        $docentes = User::role($rolDocente->name)
            ->with('institution.categoria', 'institution.escuela')
            ->when($this->filtroEscuela, fn ($q) => $q->whereHas('institution', fn ($qi) =>
                $qi->whereHas('escuela', fn ($qv) => $qv->where('value', $this->filtroEscuela))
            ))
            ->orderBy('name')
            ->get();

        $resultados = $docentes->map(fn ($docente) => [
            'docente' => $docente,
            'calculo' => PuntajeEscalafonarioCalculator::calcularTotal($docente->id),
        ]);

        return (match ($this->filtroEstado) {
            'aptos'    => $resultados->filter(fn ($r) => $r['calculo']['cumple_ascenso']),
            'no_aptos' => $resultados->filter(fn ($r) => !$r['calculo']['cumple_ascenso']),
            default    => $resultados,
        })->values();
    }

    #[Computed]
    public function escuelaSeleccionadaNombre()
    {
        return $this->filtroEscuela
            ? $this->escuelas->firstWhere('value', $this->filtroEscuela)?->name
            : null;
    }

    public function updatedFiltroEscuela() {}
    public function updatedFiltroEstado() {}

    public function generarReportePdf()
    {
        $pdf = Pdf::loadView('pdf.aptos-promocion', [
            'docentes'      => $this->docentes,
            'escuelaNombre' => $this->escuelaSeleccionadaNombre,
            'filtroEstado'  => $this->filtroEstado,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'docentes-aptos-promocion-' . now()->format('Y-m-d') . '.pdf'
        );
    }
};
?>

<div class="p-4">

    <div class="flex items-center justify-between mb-2">
        <h1 class="text-2xl font-bold">Docentes Aptos para Promoción</h1>
        <button wire:click="generarReportePdf"
            class="flex items-center gap-2 px-4 py-2 bg-ues text-white rounded-lg cursor-pointer hover:opacity-90 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
            </svg>
            Generar reporte PDF
        </button>
    </div>
    <p class="text-sm text-gray-500 mb-6">
        Docentes que cumplen (o no) el puntaje escalafonario requerido para ascender de categoría.
    </p>

    {{-- Filtros --}}
    <div class="flex flex-wrap gap-4 items-end mb-6">
        <div>
            <label class="text-sm font-semibold block mb-1">Escuela / Unidad</label>
            <select wire:model.live="filtroEscuela"
                class="border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark min-w-48">
                <option value="">Todas</option>
                @foreach ($this->escuelas as $e)
                    <option value="{{ $e->value }}">{{ $e->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-semibold block mb-1">Estado</label>
            <select wire:model.live="filtroEstado"
                class="border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark min-w-48">
                <option value="aptos">Aptos para promoción</option>
                <option value="no_aptos">No aptos</option>
                <option value="todos">Todos</option>
            </select>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-outline dark:border-outline-dark">
        <table class="w-full text-sm">
            <thead class="bg-ues text-white">
                <tr>
                    <th class="p-3 text-left">Docente</th>
                    <th class="p-3 text-center">Escuela / Unidad</th>
                    <th class="p-3 text-center">Categoría Actual</th>
                    <th class="p-3 text-center">Categoría Siguiente</th>
                    <th class="p-3 text-center">Puntaje</th>
                    <th class="p-3 text-center">%</th>
                    <th class="p-3 text-center">¿Apto?</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline dark:divide-outline-dark">
                @forelse ($this->docentes as $r)
                    @php [$docente, $c] = [$r['docente'], $r['calculo']]; @endphp
                    <tr class="hover:bg-surface-alt/40">
                        <td class="p-3">
                            <p class="font-medium">{{ $docente->name }} {{ $docente->apellidos }}</p>
                            <p class="text-xs text-gray-400">{{ $docente->email }}</p>
                        </td>
                        <td class="p-3 text-center text-xs">{{ $docente->institution?->escuela?->name ?? '—' }}</td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-ues/10 text-ues uppercase">
                                {{ $c['categoria_actual'] }}
                            </span>
                        </td>
                        <td class="p-3 text-center text-xs font-semibold uppercase">
                            {{ $c['siguiente_categoria'] ?? '— (máxima)' }}
                        </td>
                        <td class="p-3 text-center font-bold text-ues">
                            {{ number_format($c['total_ganado'], 2) }} / {{ number_format($c['total_maximo'], 2) }} pts
                        </td>
                        <td class="p-3 text-center">{{ $c['porcentaje'] }}%</td>
                        <td class="p-3 text-center">
                            @if ($c['cumple_ascenso'])
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">Sí</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">No</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-gray-400">No hay docentes que coincidan con los filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="text-xs text-gray-400 mt-2">Total: {{ $this->docentes->count() }} docentes</p>

</div>
