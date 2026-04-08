<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\PeriodoEvaluacion;
use App\Models\FormularioConsolidado;
use App\Models\SolicitudPromocion;
use App\Services\PuntajeEscalafonarioCalculator;
use Spatie\Permission\Models\Role;

new class extends Component {

    #[Url]
    public int $docenteId;

    #[Url]
    public ?int $periodoId = null;

    public ?array $resultado      = null;
    public ?int   $guardadoId    = null;
    public bool   $generado      = false;
    public bool   $yaSolicito    = false;
    public bool   $puedesSolicitar = false;

    public function mount()
    {
        // Docente solo puede ver su propio formulario
        if (auth()->user()->hasRole('docente')) {
            $this->docenteId = auth()->id();
        }
    }

    public function generar()
    {
        $this->resultado = PuntajeEscalafonarioCalculator::calcular(
            $this->docenteId,
            $this->periodoId ?: null
        );
        $this->generado  = true;
        $this->guardadoId = null;

        // Verificar si puede solicitar promoción
        $this->puedesSolicitar = $this->resultado['cumple_ascenso']
            && $this->resultado['siguiente_categoria'] !== null;

        // Verificar si ya tiene una solicitud pendiente
        $this->yaSolicito = SolicitudPromocion::where('docente_id', $this->docenteId)
            ->where('estado', 'pendiente')
            ->exists();
    }

    public function solicitarPromocion()
    {
        if (!$this->resultado || !$this->resultado['cumple_ascenso']) return;

        if ($this->yaSolicito) {
            $this->dispatch('notify', type: 'error', message: 'Ya tiene una solicitud de promoción pendiente.');
            return;
        }

        // Guardar snapshot si no existe aún
        if (!$this->guardadoId) {
            $this->guardarFormulario();
        }

        SolicitudPromocion::create([
            'docente_id'          => $this->docenteId,
            'periodo_id'          => $this->periodoId ?: null,
            'formulario_id'       => $this->guardadoId,
            'categoria_actual'    => $this->resultado['categoria_actual'],
            'categoria_solicitada'=> $this->resultado['siguiente_categoria'],
            'puntaje_obtenido'    => $this->resultado['total_ganado'],
            'puntaje_requerido'   => $this->resultado['total_maximo'],
            'estado'              => 'pendiente',
        ]);

        $this->yaSolicito = true;
        $this->dispatch('notify', type: 'success',
            message: 'Solicitud de promoción enviada. El administrador la revisará próximamente.');
    }

    public function guardarFormulario()
    {
        if (!$this->resultado) return;

        $formulario = FormularioConsolidado::updateOrCreate(
            [
                'docente_id' => $this->docenteId,
                'periodo_id' => $this->periodoId ?: null,
            ],
            [
                'generado_por'        => auth()->id(),
                'aspectos'            => $this->resultado['aspectos'],
                'total_ganado'        => $this->resultado['total_ganado'],
                'total_maximo'        => $this->resultado['total_maximo'],
                'categoria_actual'    => $this->resultado['categoria_actual'],
                'siguiente_categoria' => $this->resultado['siguiente_categoria'],
                'cumple_ascenso'      => $this->resultado['cumple_ascenso'],
            ]
        );

        $this->guardadoId = $formulario->id;
        $this->dispatch('notify', type: 'success', message: 'Formulario consolidado guardado.');
    }

    public function volver()
    {
        $this->redirectRoute('manage.evaluaciones');
    }
};
?>

<div class="p-4">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6 print:hidden">
        @cannotRole('docente')
            <button wire:click="volver"
                class="flex items-center gap-2 px-3 py-2 bg-ues text-white rounded-lg cursor-pointer hover:opacity-90 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Volver
            </button>
        @endcannotRole
        <h1 class="text-2xl font-bold">Formulario Consolidado de Evaluación Escalafonaria</h1>
    </div>

    {{-- Controles --}}
    <div class="flex flex-wrap gap-4 items-end mb-6 p-4 bg-surface-alt dark:bg-surface-dark-alt rounded-xl print:hidden">
        <div>
            <label class="text-sm font-semibold block mb-1">Periodo (opcional)</label>
            <select wire:model="periodoId"
                class="border border-outline rounded-lg px-3 py-2 text-sm dark:bg-surface-dark-alt dark:border-outline-dark min-w-48">
                <option value="">— Todos los periodos —</option>
                @foreach (\App\Models\PeriodoEvaluacion::orderBy('anio','desc')->orderBy('ciclo')->get() as $p)
                    <option value="{{ $p->id }}">Ciclo {{ $p->ciclo }} - {{ $p->anio }}</option>
                @endforeach
            </select>
        </div>

        <button wire:click="generar"
            class="flex items-center gap-2 px-5 py-2 bg-ues text-white rounded-lg cursor-pointer font-medium hover:opacity-90">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 15.75l-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            Calcular Puntaje
        </button>

        @if ($generado)
            <button wire:click="guardarFormulario"
                class="flex items-center gap-2 px-5 py-2 border border-ues text-ues rounded-lg cursor-pointer font-medium hover:bg-ues/5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Guardar Snapshot
            </button>

            <button onclick="window.print()"
                class="flex items-center gap-2 px-5 py-2 border border-gray-400 text-gray-600 rounded-lg cursor-pointer font-medium hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                </svg>
                Imprimir
            </button>
        @endif
    </div>

    {{-- Formulario generado --}}
    @if ($generado && $resultado)
        @php $r = $resultado; $d = $r['docente']; @endphp

        {{-- Encabezado del formulario --}}
        <div class="border-2 border-gray-300 rounded-xl overflow-hidden mb-6 print:border-black">

            {{-- Header institucional --}}
            <div class="bg-ues p-4 text-white text-center print:bg-white print:text-black print:border-b print:border-black">
                <p class="font-bold text-lg">UNIVERSIDAD DE EL SALVADOR</p>
                <p class="text-sm">FACULTAD DE INGENIERÍA Y ARQUITECTURA</p>
                <p class="font-semibold mt-1">FORMULARIO DE EVALUACIÓN ESCALAFONARIA DEL PERSONAL DOCENTE</p>
                <p class="text-xs mt-1">{{ now()->format('d/m/Y') }}</p>
            </div>

            {{-- Datos del docente --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-3 p-5 border-b border-gray-200">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Nombre</p>
                    <p class="font-semibold">{{ $d->name }} {{ $d->apellidos }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Correo</p>
                    <p class="font-semibold">{{ $d->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Escuela / Unidad</p>
                    <p class="font-semibold">{{ $d->institution?->escuela?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Categoría Actual</p>
                    <p class="font-bold text-lg text-ues">{{ $r['categoria_actual'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Fecha Ingreso UES</p>
                    <p class="font-semibold">{{ $d->institution?->fecha_ingreso?->format('d/m/Y') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Tipo Nombramiento</p>
                    <p class="font-semibold">{{ $d->institution?->tipoNombramiento?->name ?? '—' }}</p>
                </div>
            </div>

            {{-- Tabla de puntajes --}}
            <div class="p-5">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-zinc-700">
                            <th class="border border-gray-300 p-3 text-left">Aspecto de Evaluación</th>
                            <th class="border border-gray-300 p-3 text-center w-28">Puntaje Obtenido</th>
                            <th class="border border-gray-300 p-3 text-center w-28">Puntaje Máximo<br><span class="font-normal text-xs">({{ $r['siguiente_categoria'] ?? $r['categoria_actual'] }})</span></th>
                            <th class="border border-gray-300 p-3 text-center w-36">Progreso</th>
                            <th class="border border-gray-300 p-3 text-left">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($r['aspectos'] as $key => $aspecto)
                            @php
                                $pct = $aspecto['maximo'] > 0
                                    ? min(100, round(($aspecto['ganado'] / $aspecto['maximo']) * 100))
                                    : 0;
                                $color = $pct >= 100 ? 'bg-green-500' : ($pct >= 60 ? 'bg-yellow-400' : 'bg-red-400');
                            @endphp
                            <tr class="hover:bg-surface-alt/30">
                                <td class="border border-gray-200 p-3 font-medium">{{ $aspecto['label'] }}</td>
                                <td class="border border-gray-200 p-3 text-center">
                                    <span class="font-bold text-base {{ $pct >= 100 ? 'text-green-600' : '' }}">
                                        {{ number_format($aspecto['ganado'], 2) }}
                                    </span>
                                    @if ($aspecto['bruto'] > $aspecto['maximo'])
                                        <span class="text-xs text-gray-400 block">({{ number_format($aspecto['bruto'], 2) }} bruto)</span>
                                    @endif
                                </td>
                                <td class="border border-gray-200 p-3 text-center font-semibold">
                                    {{ $aspecto['maximo'] }}
                                </td>
                                <td class="border border-gray-200 p-3">
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="{{ $color }} h-3 rounded-full transition-all"
                                            style="width: {{ $pct }}%"></div>
                                    </div>
                                    <p class="text-xs text-center mt-0.5 text-gray-500">{{ $pct }}%</p>
                                </td>
                                <td class="border border-gray-200 p-3 text-xs text-gray-500">{{ $aspecto['detalle'] }}</td>
                            </tr>
                        @endforeach

                        {{-- Fila total --}}
                        <tr class="font-bold bg-gray-50 dark:bg-zinc-700">
                            <td class="border border-gray-300 p-3">TOTAL</td>
                            <td class="border border-gray-300 p-3 text-center text-xl
                                {{ $r['cumple_ascenso'] ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($r['total_ganado'], 2) }}
                            </td>
                            <td class="border border-gray-300 p-3 text-center text-xl">
                                {{ $r['total_maximo'] }}
                            </td>
                            <td class="border border-gray-300 p-3" colspan="2">
                                @php
                                    $pctTotal = $r['total_maximo'] > 0
                                        ? min(100, round(($r['total_ganado'] / $r['total_maximo']) * 100))
                                        : 0;
                                @endphp
                                <div class="w-full bg-gray-200 rounded-full h-4">
                                    <div class="{{ $r['cumple_ascenso'] ? 'bg-green-500' : 'bg-red-400' }} h-4 rounded-full"
                                        style="width: {{ $pctTotal }}%"></div>
                                </div>
                                <p class="text-sm text-center mt-1">{{ $pctTotal }}% completado</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Resultado --}}
            <div class="p-5 border-t border-gray-200">
                @if ($r['cumple_ascenso'])
                    <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-300 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-8 text-green-600 flex-shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <div>
                            <p class="font-bold text-green-800 text-lg">
                                ¡El docente CUMPLE los requisitos para ascender a {{ $r['siguiente_categoria'] }}!
                            </p>
                            <p class="text-green-700 text-sm">
                                Puntaje obtenido: <strong>{{ number_format($r['total_ganado'], 2) }}</strong> /
                                Requerido: <strong>{{ $r['total_maximo'] }}</strong>
                            </p>
                        </div>
                    </div>
                @elseif ($r['siguiente_categoria'])
                    <div class="flex items-center gap-3 p-4 bg-yellow-50 border border-yellow-300 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-8 text-yellow-600 flex-shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <div>
                            <p class="font-bold text-yellow-800 text-lg">
                                No cumple aún los requisitos para ascender a {{ $r['siguiente_categoria'] }}
                            </p>
                            <p class="text-yellow-700 text-sm">
                                Puntaje obtenido: <strong>{{ number_format($r['total_ganado'], 2) }}</strong> —
                                Faltan: <strong>{{ number_format($r['puntaje_faltante'], 2) }} pts</strong>
                                para llegar a {{ $r['total_maximo'] }}
                            </p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-300 rounded-xl">
                        <p class="font-bold text-blue-800">El docente se encuentra en la categoría máxima (PU-IV).</p>
                    </div>
                @endif
            </div>

            {{-- Cuadro comparativo por categorías --}}
            <div class="p-5 border-t border-gray-200">
                <h3 class="font-bold mb-3 text-sm uppercase tracking-wide text-gray-500">Cuadro comparativo — Escala de calificación (Art. 45)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 p-2 text-left">Aspecto</th>
                                <th class="border border-gray-300 p-2 text-center">PU-II (25 pts)</th>
                                <th class="border border-gray-300 p-2 text-center">PU-III (57 pts)</th>
                                <th class="border border-gray-300 p-2 text-center">PU-IV (86 pts)</th>
                                <th class="border border-gray-300 p-2 text-center font-bold text-ues">Obtenido</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($r['aspectos'] as $key => $aspecto)
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-200 p-2">{{ $aspecto['label'] }}</td>
                                    <td class="border border-gray-200 p-2 text-center">{{ $r['maximos_referencia']['pu-ii'][$key] }}</td>
                                    <td class="border border-gray-200 p-2 text-center">{{ $r['maximos_referencia']['pu-iii'][$key] }}</td>
                                    <td class="border border-gray-200 p-2 text-center">{{ $r['maximos_referencia']['pu-iv'][$key] }}</td>
                                    <td class="border border-gray-200 p-2 text-center font-bold text-ues">{{ number_format($aspecto['bruto'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-bold bg-gray-100">
                                <td class="border border-gray-300 p-2">TOTAL</td>
                                <td class="border border-gray-300 p-2 text-center">25</td>
                                <td class="border border-gray-300 p-2 text-center">57</td>
                                <td class="border border-gray-300 p-2 text-center">86</td>
                                <td class="border border-gray-300 p-2 text-center text-ues">{{ number_format($r['total_ganado'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Firmas --}}
            <div class="grid grid-cols-3 gap-8 p-6 border-t border-gray-200 mt-4">
                @foreach (['Docente', 'Jefe de Unidad', 'Decanato'] as $firma)
                    <div class="text-center">
                        <div class="border-t-2 border-gray-400 pt-2 mt-8">
                            <p class="text-xs font-semibold">{{ $firma }}</p>
                            <p class="text-xs text-gray-400">Firma y sello</p>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

        @if ($guardadoId)
            <p class="text-xs text-green-600 print:hidden">✓ Formulario guardado como snapshot (ID #{{ $guardadoId }})</p>
        @endif

        {{-- Botón solicitar promoción --}}
        @if ($puedesSolicitar)
            <div class="mt-4 print:hidden">
                @if ($yaSolicito)
                    <div class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-gray-100 border border-gray-300 text-gray-600 text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Solicitud de promoción pendiente de revisión
                    </div>
                @else
                    <button wire:click="solicitarPromocion"
                        wire:confirm="¿Confirma que desea solicitar la promoción a {{ $resultado['siguiente_categoria'] }}? Se notificará al administrador."
                        class="flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-xl cursor-pointer font-semibold hover:bg-green-700 shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                        </svg>
                        Solicitar Promoción a {{ $resultado['siguiente_categoria'] }}
                    </button>
                @endif
            </div>
        @endif
    @else
        <div class="flex items-center justify-center h-48 border border-dashed border-outline rounded-xl text-gray-400">
            Seleccione el periodo y haga clic en <strong class="mx-1">Calcular Puntaje</strong> para generar el formulario.
        </div>
    @endif

</div>

{{-- Estilos de impresión --}}
<style>
    @media print {
        .print\:hidden { display: none !important; }
        body { background: white !important; }
        .bg-ues { background-color: #960000 !important; -webkit-print-color-adjust: exact; }
    }
</style>
