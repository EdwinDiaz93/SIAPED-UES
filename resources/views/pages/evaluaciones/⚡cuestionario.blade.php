<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Evaluacion;
use App\Models\EvaluacionLaborAcademica;

new class extends Component {

    #[Url]
    public int $id;

    public ?Evaluacion $evaluacion = null;

    // Tab activo
    public string $tab = 'estudiante';

    // Puntajes de cada cuestionario (inicializados en mount)
    public array $criteriosEstudiante = [];
    public array $criteriosJefe       = [];
    public array $criteriosAuto       = [];

    public function mount()
    {
        $this->evaluacion = Evaluacion::with([
            'docente.institution.categoria',
            'docente.institution.escuela',
            'cuestionarioEstudiante',
            'cuestionarioJefe',
            'cuestionarioAuto',
        ])->findOrFail($this->id);

        // Inicializar criterios con ceros o valores existentes
        $this->criteriosEstudiante = $this->cargarCriterios('estudiante');
        $this->criteriosJefe       = $this->cargarCriterios('jefe');
        $this->criteriosAuto       = $this->cargarCriterios('auto');
    }

    private function cargarCriterios(string $tipo): array
    {
        $pesos     = EvaluacionLaborAcademica::pesosParaTipo($tipo);
        $existente = $this->evaluacion->laborAcademica->where('tipo', $tipo)->first();
        $valores   = $existente?->criterios ?? [];

        $result = [];
        foreach (array_keys($pesos) as $key) {
            $result[$key] = $valores[$key] ?? 0;
        }
        return $result;
    }

    private function validarCriterios(array $criterios, string $tipo): bool
    {
        foreach ($criterios as $key => $val) {
            $v = (int) $val;
            if ($v < 0 || $v > 10) {
                $this->addError("criterios_{$tipo}.{$key}", 'Valor debe estar entre 0 y 10.');
                return false;
            }
        }
        return true;
    }

    public function guardarEstudiante()
    {
        $this->resetErrorBag();
        if (!$this->validarCriterios($this->criteriosEstudiante, 'estudiante')) return;

        $nota = EvaluacionLaborAcademica::calcularNotaPonderada('estudiante', $this->criteriosEstudiante);

        EvaluacionLaborAcademica::updateOrCreate(
            ['evaluacion_id' => $this->evaluacion->id, 'tipo' => 'estudiante'],
            [
                'evaluador_id'   => null,
                'criterios'      => $this->criteriosEstudiante,
                'nota_ponderada' => $nota,
            ]
        );

        $this->evaluacion->nota_estudiante = $nota;
        $this->evaluacion->recalcularPuntaje();
        $this->evaluacion->refresh();

        $this->dispatch('notify', type: 'success', message: 'Cuestionario de estudiantes guardado. Nota: ' . number_format($nota, 2));
    }

    public function guardarJefe()
    {
        $this->resetErrorBag();
        if (!$this->validarCriterios($this->criteriosJefe, 'jefe')) return;

        $nota = EvaluacionLaborAcademica::calcularNotaPonderada('jefe', $this->criteriosJefe);

        EvaluacionLaborAcademica::updateOrCreate(
            ['evaluacion_id' => $this->evaluacion->id, 'tipo' => 'jefe'],
            [
                'evaluador_id'   => auth()->id(),
                'criterios'      => $this->criteriosJefe,
                'nota_ponderada' => $nota,
            ]
        );

        $this->evaluacion->nota_jefe = $nota;
        $this->evaluacion->recalcularPuntaje();
        $this->evaluacion->refresh();

        $this->dispatch('notify', type: 'success', message: 'Cuestionario de Jefe Inmediato guardado. Nota: ' . number_format($nota, 2));
    }

    public function guardarAuto()
    {
        $this->resetErrorBag();
        if (!$this->validarCriterios($this->criteriosAuto, 'auto')) return;

        $nota = EvaluacionLaborAcademica::calcularNotaPonderada('auto', $this->criteriosAuto);

        EvaluacionLaborAcademica::updateOrCreate(
            ['evaluacion_id' => $this->evaluacion->id, 'tipo' => 'auto'],
            [
                'evaluador_id'   => $this->evaluacion->docente_id,
                'criterios'      => $this->criteriosAuto,
                'nota_ponderada' => $nota,
            ]
        );

        $this->evaluacion->nota_auto = $nota;
        $this->evaluacion->recalcularPuntaje();
        $this->evaluacion->refresh();

        $this->dispatch('notify', type: 'success', message: 'Autoevaluación guardada. Nota: ' . number_format($nota, 2));
    }

    public function volver()
    {
        $this->redirectRoute('manage.evaluaciones');
    }
};
?>

<div class="p-4">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <button wire:click="volver"
            class="flex items-center gap-2 px-3 py-2 bg-ues text-white rounded-lg cursor-pointer hover:opacity-90">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Volver
        </button>
        <h1 class="text-2xl font-bold">Evaluación Labor Académica</h1>
    </div>

    {{-- Info del docente --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-surface-alt dark:bg-surface-dark-alt rounded-xl mb-6 shadow">
        <div>
            <p class="text-xs text-gray-500">Docente</p>
            <p class="font-semibold">{{ $evaluacion->docente->name }} {{ $evaluacion->docente->apellidos }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Periodo</p>
            <p class="font-semibold">Ciclo {{ $evaluacion->periodo->ciclo }} - {{ $evaluacion->periodo->anio }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Categoría</p>
            <p class="font-semibold uppercase">{{ $evaluacion->docente->institution?->categoria?->name ?? '—' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Nota mínima requerida</p>
            <p class="font-bold text-lg">{{ $evaluacion->getNotaMinimaRequerida() }}.0</p>
        </div>
    </div>

    {{-- Resumen de puntajes --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        @php
            $cards = [
                ['label' => 'Estudiantes (33.33%)', 'value' => $evaluacion->nota_estudiante],
                ['label' => 'Jefe Inmediato (33.33%)', 'value' => $evaluacion->nota_jefe],
                ['label' => 'Autoevaluación (33.33%)', 'value' => $evaluacion->nota_auto],
                ['label' => 'Nota Promedio', 'value' => $evaluacion->nota_promedio, 'destacado' => true],
                ['label' => 'Puntaje Escalafón', 'value' => $evaluacion->puntaje, 'sufijo' => ' pts', 'destacado' => true],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="p-4 rounded-xl border {{ $card['destacado'] ?? false ? 'border-ues bg-ues/5' : 'border-outline dark:border-outline-dark' }} text-center">
                <p class="text-xs text-gray-500 mb-1">{{ $card['label'] }}</p>
                <p class="text-2xl font-bold {{ $card['destacado'] ?? false ? 'text-ues' : '' }}">
                    @if ($card['value'] !== null)
                        {{ number_format($card['value'], 2) }}{{ $card['sufijo'] ?? '' }}
                    @else
                        <span class="text-gray-300">—</span>
                    @endif
                </p>
            </div>
        @endforeach
    </div>

    @if ($evaluacion->nota_promedio !== null)
        <div class="mb-4 p-3 rounded-lg text-sm font-medium {{ $evaluacion->cumpleNotaMinima() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            @if ($evaluacion->cumpleNotaMinima())
                ✓ El docente cumple la nota mínima requerida ({{ $evaluacion->getNotaMinimaRequerida() }}) para su categoría.
            @else
                ✗ El docente NO alcanza la nota mínima requerida ({{ $evaluacion->getNotaMinimaRequerida() }}) para su categoría.
            @endif
        </div>
    @endif

    {{-- Tabs de cuestionarios --}}
    <div x-data="{ tab: @entangle('tab') }">
        <div class="flex gap-2 border-b border-outline dark:border-outline-dark mb-4">
            @foreach ([
                ['estudiante', 'Cuestionario 1 — Estudiantes', $evaluacion->nota_estudiante],
                ['jefe',       'Cuestionario 2 — Jefe Inmediato', $evaluacion->nota_jefe],
                ['auto',       'Cuestionario 3 — Autoevaluación', $evaluacion->nota_auto],
            ] as [$key, $label, $nota])
                <button
                    x-on:click="tab = '{{ $key }}'"
                    x-bind:class="tab === '{{ $key }}'
                        ? 'font-bold bg-ues text-white border-b-2 border-primary'
                        : 'hover:border-b-2 hover:border-b-outline-strong text-on-surface font-medium'"
                    class="px-4 py-2 text-sm flex items-center gap-2">
                    {{ $label }}
                    @if ($nota !== null)
                        <span class="ml-1 px-1.5 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-bold">
                            {{ number_format($nota, 1) }}
                        </span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Cuestionario 1: Estudiantes --}}
        <div x-show="tab === 'estudiante'" x-cloak>
            @include('pages.evaluaciones.partials.cuestionario-form', [
                'tipo'       => 'estudiante',
                'criterios'  => $criteriosEstudiante,
                'pesos'      => \App\Models\EvaluacionLaborAcademica::PESOS_ESTUDIANTE,
                'etiquetas'  => \App\Models\EvaluacionLaborAcademica::ETIQUETAS_ESTUDIANTE,
                'wireModel'  => 'criteriosEstudiante',
                'wireSubmit' => 'guardarEstudiante',
                'titulo'     => 'Evaluación por Estudiantes',
            ])
        </div>

        {{-- Cuestionario 2: Jefe Inmediato --}}
        <div x-show="tab === 'jefe'" x-cloak>
            @include('pages.evaluaciones.partials.cuestionario-form', [
                'tipo'       => 'jefe',
                'criterios'  => $criteriosJefe,
                'pesos'      => \App\Models\EvaluacionLaborAcademica::PESOS_JEFE,
                'etiquetas'  => \App\Models\EvaluacionLaborAcademica::ETIQUETAS_JEFE,
                'wireModel'  => 'criteriosJefe',
                'wireSubmit' => 'guardarJefe',
                'titulo'     => 'Evaluación por Jefe Inmediato',
            ])
        </div>

        {{-- Cuestionario 3: Autoevaluación --}}
        <div x-show="tab === 'auto'" x-cloak>
            @include('pages.evaluaciones.partials.cuestionario-form', [
                'tipo'       => 'auto',
                'criterios'  => $criteriosAuto,
                'pesos'      => \App\Models\EvaluacionLaborAcademica::PESOS_AUTO,
                'etiquetas'  => \App\Models\EvaluacionLaborAcademica::ETIQUETAS_AUTO,
                'wireModel'  => 'criteriosAuto',
                'wireSubmit' => 'guardarAuto',
                'titulo'     => 'Autoevaluación del Docente',
            ])
        </div>
    </div>

</div>
