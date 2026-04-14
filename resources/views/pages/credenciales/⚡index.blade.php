<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use App\Models\CredencialCapacitacion;
use App\Models\CredencialProyeccionSocial;
use App\Models\CredencialEspecializacion;
use App\Models\CredencialInvestigacion;
use App\Models\CredencialSeguimiento;

new class extends Component {

    // ID del docente a gestionar (admin puede ver cualquiera, docente solo el suyo)
    #[Url(as: 'docenteId')]
    public int $docenteId = 0;

    // ── Capacitación ──────────────────────────────────────────────────────────
    public string $cap_tipo         = 'curso';
    public string $cap_nombre       = '';
    public string $cap_institucion  = '';
    public string $cap_horas        = '';
    public string $cap_fecha_inicio = '';
    public string $cap_fecha_fin    = '';
    public ?int   $cap_editando     = null;

    // ── Proyección Social ─────────────────────────────────────────────────────
    public string $proy_nombre          = '';
    public string $proy_responsabilidad = 'formulador';
    public string $proy_cobertura       = 'local';
    public string $proy_duracion        = 'lte3meses';
    public string $proy_fecha_inicio    = '';
    public string $proy_fecha_fin       = '';
    public ?int   $proy_editando        = null;

    // ── Especialización ───────────────────────────────────────────────────────
    public string $esp_tipo        = 'maestria';
    public string $esp_titulo      = '';
    public string $esp_institucion = '';
    public string $esp_horas       = '';
    public string $esp_fecha       = '';
    public ?int   $esp_editando    = null;

    // ── Investigación ─────────────────────────────────────────────────────────
    public string $inv_tipo              = 'proyecto';
    public string $inv_titulo            = '';
    public string $inv_fecha             = '';
    public string $inv_financiamiento    = 'institucional';
    public string $inv_participacion     = 'investigador';
    public string $inv_duracion_proyecto = 'lt1anio';
    public string $inv_tipo_publicacion  = 'articulo_indexado';
    public ?int   $inv_editando          = null;

    // ── Seguimiento ───────────────────────────────────────────────────────────
    public string $seg_tipo        = 'grado_adicional';
    public string $seg_descripcion = '';
    public string $seg_horas       = '';
    public string $seg_fecha       = '';
    public ?int   $seg_editando    = null;

    public function mount()
    {
        if (!$this->docenteId) {
            $this->docenteId = auth()->id();
        }
    }

    #[Computed]
    public function esAdmin(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    // ── Computed: listas ──────────────────────────────────────────────────────

    #[Computed]
    public function capacitaciones()
    {
        return CredencialCapacitacion::where('docente_id', $this->docenteId)
            ->orderByDesc('fecha_fin')->get();
    }

    #[Computed]
    public function proyecciones()
    {
        return CredencialProyeccionSocial::where('docente_id', $this->docenteId)
            ->orderByDesc('fecha_fin')->get();
    }

    #[Computed]
    public function especializaciones()
    {
        return CredencialEspecializacion::where('docente_id', $this->docenteId)
            ->orderByDesc('fecha')->get();
    }

    #[Computed]
    public function investigaciones()
    {
        return CredencialInvestigacion::where('docente_id', $this->docenteId)
            ->orderByDesc('fecha')->get();
    }

    #[Computed]
    public function seguimientos()
    {
        return CredencialSeguimiento::where('docente_id', $this->docenteId)
            ->orderByDesc('fecha')->get();
    }

    // ── Computed: totales ─────────────────────────────────────────────────────

    #[Computed]
    public function totalCapacitacion()
    {
        return CredencialCapacitacion::puntajeTotalDocente($this->docenteId);
    }

    #[Computed]
    public function totalProyeccion()
    {
        return CredencialProyeccionSocial::puntajeTotalDocente($this->docenteId);
    }

    #[Computed]
    public function totalEspecializacion()
    {
        return CredencialEspecializacion::puntajeTotalDocente($this->docenteId);
    }

    #[Computed]
    public function totalInvestigacion()
    {
        return CredencialInvestigacion::puntajeTotalDocente($this->docenteId);
    }

    #[Computed]
    public function totalSeguimiento()
    {
        return CredencialSeguimiento::puntajeTotalDocente($this->docenteId);
    }

    // ── CRUD: Capacitación ────────────────────────────────────────────────────

    public function guardarCapacitacion()
    {
        $this->validate([
            'cap_tipo'         => 'required|in:curso,diplomado_maestria',
            'cap_nombre'       => 'required|string|max:255',
            'cap_institucion'  => 'nullable|string|max:255',
            'cap_horas'        => 'required_if:cap_tipo,curso|nullable|integer|min:1',
            'cap_fecha_inicio' => 'required|date',
            'cap_fecha_fin'    => 'required|date|after_or_equal:cap_fecha_inicio',
        ], [
            'cap_horas.required_if' => 'Las horas son requeridas para cursos.',
            'cap_fecha_fin.after_or_equal' => 'La fecha fin debe ser posterior al inicio.',
        ]);

        $puntaje = CredencialCapacitacion::calcularPuntaje(
            $this->cap_tipo,
            $this->cap_tipo === 'curso' ? (int) $this->cap_horas : null
        );

        $data = [
            'docente_id'   => $this->docenteId,
            'tipo'         => $this->cap_tipo,
            'nombre'       => $this->cap_nombre,
            'institucion'  => $this->cap_institucion ?: null,
            'horas'        => $this->cap_tipo === 'curso' ? (int) $this->cap_horas : null,
            'fecha_inicio' => $this->cap_fecha_inicio,
            'fecha_fin'    => $this->cap_fecha_fin,
            'puntaje'      => $puntaje,
        ];

        if ($this->cap_editando) {
            CredencialCapacitacion::findOrFail($this->cap_editando)->update($data);
        } else {
            CredencialCapacitacion::create($data);
        }

        $this->resetCap();
        $this->dispatch('notify', type: 'success', message: 'Credencial de capacitación guardada.');
    }

    public function editarCapacitacion(int $id)
    {
        $r = CredencialCapacitacion::findOrFail($id);
        abort_if($r->estado === 'aprobado', 403);
        $this->cap_editando    = $id;
        $this->cap_tipo        = $r->tipo;
        $this->cap_nombre      = $r->nombre;
        $this->cap_institucion = $r->institucion ?? '';
        $this->cap_horas       = (string) ($r->horas ?? '');
        $this->cap_fecha_inicio = $r->fecha_inicio->format('Y-m-d');
        $this->cap_fecha_fin    = $r->fecha_fin->format('Y-m-d');
    }

    public function eliminarCapacitacion(int $id)
    {
        $r = CredencialCapacitacion::findOrFail($id);
        abort_if($r->estado === 'aprobado', 403);
        $r->delete();
        $this->dispatch('notify', type: 'success', message: 'Registro eliminado.');
    }

    private function resetCap()
    {
        $this->cap_editando = null;
        $this->cap_tipo = 'curso'; $this->cap_nombre = ''; $this->cap_institucion = '';
        $this->cap_horas = ''; $this->cap_fecha_inicio = ''; $this->cap_fecha_fin = '';
        $this->resetValidation();
    }

    // ── CRUD: Proyección Social ───────────────────────────────────────────────

    public function guardarProyeccion()
    {
        $this->validate([
            'proy_nombre'          => 'required|string|max:255',
            'proy_responsabilidad' => 'required|in:formulador,ejecutor,coordinador',
            'proy_cobertura'       => 'required|in:local,regional,nacional',
            'proy_duracion'        => 'required|in:lte3meses,3a6meses,gt6meses',
            'proy_fecha_inicio'    => 'required|date',
            'proy_fecha_fin'       => 'required|date|after_or_equal:proy_fecha_inicio',
        ]);

        $puntaje = CredencialProyeccionSocial::calcularPuntaje(
            $this->proy_responsabilidad,
            $this->proy_cobertura,
            $this->proy_duracion
        );

        $data = [
            'docente_id'      => $this->docenteId,
            'nombre'          => $this->proy_nombre,
            'responsabilidad' => $this->proy_responsabilidad,
            'cobertura'       => $this->proy_cobertura,
            'duracion'        => $this->proy_duracion,
            'fecha_inicio'    => $this->proy_fecha_inicio,
            'fecha_fin'       => $this->proy_fecha_fin,
            'puntaje'         => $puntaje,
        ];

        if ($this->proy_editando) {
            CredencialProyeccionSocial::findOrFail($this->proy_editando)->update($data);
        } else {
            CredencialProyeccionSocial::create($data);
        }

        $this->resetProy();
        $this->dispatch('notify', type: 'success', message: 'Proyecto de proyección social guardado.');
    }

    public function editarProyeccion(int $id)
    {
        $r = CredencialProyeccionSocial::findOrFail($id);
        abort_if($r->estado === 'aprobado', 403);
        $this->proy_editando        = $id;
        $this->proy_nombre          = $r->nombre;
        $this->proy_responsabilidad = $r->responsabilidad;
        $this->proy_cobertura       = $r->cobertura;
        $this->proy_duracion        = $r->duracion;
        $this->proy_fecha_inicio    = $r->fecha_inicio->format('Y-m-d');
        $this->proy_fecha_fin       = $r->fecha_fin->format('Y-m-d');
    }

    public function eliminarProyeccion(int $id)
    {
        $r = CredencialProyeccionSocial::findOrFail($id);
        abort_if($r->estado === 'aprobado', 403);
        $r->delete();
        $this->dispatch('notify', type: 'success', message: 'Registro eliminado.');
    }

    private function resetProy()
    {
        $this->proy_editando = null;
        $this->proy_nombre = ''; $this->proy_responsabilidad = 'formulador';
        $this->proy_cobertura = 'local'; $this->proy_duracion = 'lte3meses';
        $this->proy_fecha_inicio = ''; $this->proy_fecha_fin = '';
        $this->resetValidation();
    }

    // ── CRUD: Especialización ─────────────────────────────────────────────────

    public function guardarEspecializacion()
    {
        $this->validate([
            'esp_tipo'        => 'required|in:phd,maestria,curso',
            'esp_titulo'      => 'required|string|max:255',
            'esp_institucion' => 'nullable|string|max:255',
            'esp_horas'       => 'required_if:esp_tipo,curso|nullable|integer|min:1',
            'esp_fecha'       => 'required|date',
        ]);

        $puntaje = CredencialEspecializacion::calcularPuntaje(
            $this->esp_tipo,
            $this->esp_tipo === 'curso' ? (int) $this->esp_horas : null
        );

        $data = [
            'docente_id'  => $this->docenteId,
            'tipo'        => $this->esp_tipo,
            'titulo'      => $this->esp_titulo,
            'institucion' => $this->esp_institucion ?: null,
            'horas'       => $this->esp_tipo === 'curso' ? (int) $this->esp_horas : null,
            'fecha'       => $this->esp_fecha,
            'puntaje'     => $puntaje,
        ];

        if ($this->esp_editando) {
            CredencialEspecializacion::findOrFail($this->esp_editando)->update($data);
        } else {
            CredencialEspecializacion::create($data);
        }

        $this->resetEsp();
        $this->dispatch('notify', type: 'success', message: 'Credencial de especialización guardada.');
    }

    public function editarEspecializacion(int $id)
    {
        $r = CredencialEspecializacion::findOrFail($id);
        abort_if($r->estado === 'aprobado', 403);
        $this->esp_editando    = $id;
        $this->esp_tipo        = $r->tipo;
        $this->esp_titulo      = $r->titulo;
        $this->esp_institucion = $r->institucion ?? '';
        $this->esp_horas       = (string) ($r->horas ?? '');
        $this->esp_fecha       = $r->fecha->format('Y-m-d');
    }

    public function eliminarEspecializacion(int $id)
    {
        $r = CredencialEspecializacion::findOrFail($id);
        abort_if($r->estado === 'aprobado', 403);
        $r->delete();
        $this->dispatch('notify', type: 'success', message: 'Registro eliminado.');
    }

    private function resetEsp()
    {
        $this->esp_editando = null;
        $this->esp_tipo = 'maestria'; $this->esp_titulo = ''; $this->esp_institucion = '';
        $this->esp_horas = ''; $this->esp_fecha = '';
        $this->resetValidation();
    }

    // ── CRUD: Investigación ───────────────────────────────────────────────────

    public function guardarInvestigacion()
    {
        $this->validate([
            'inv_tipo'              => 'required|in:proyecto,publicacion,red,patente',
            'inv_titulo'            => 'required|string|max:255',
            'inv_fecha'             => 'required|date',
            'inv_financiamiento'    => 'required_if:inv_tipo,proyecto',
            'inv_participacion'     => 'required_if:inv_tipo,proyecto',
            'inv_duracion_proyecto' => 'required_if:inv_tipo,proyecto',
            'inv_tipo_publicacion'  => 'required_if:inv_tipo,publicacion',
        ]);

        $puntaje = CredencialInvestigacion::calcularPuntaje([
            'tipo'              => $this->inv_tipo,
            'financiamiento'    => $this->inv_financiamiento,
            'participacion'     => $this->inv_participacion,
            'duracion_proyecto' => $this->inv_duracion_proyecto,
            'tipo_publicacion'  => $this->inv_tipo_publicacion,
        ]);

        $data = [
            'docente_id'         => $this->docenteId,
            'tipo'               => $this->inv_tipo,
            'titulo'             => $this->inv_titulo,
            'fecha'              => $this->inv_fecha,
            'financiamiento'     => $this->inv_tipo === 'proyecto' ? $this->inv_financiamiento : null,
            'participacion'      => $this->inv_tipo === 'proyecto' ? $this->inv_participacion : null,
            'duracion_proyecto'  => $this->inv_tipo === 'proyecto' ? $this->inv_duracion_proyecto : null,
            'tipo_publicacion'   => $this->inv_tipo === 'publicacion' ? $this->inv_tipo_publicacion : null,
            'puntaje'            => $puntaje,
        ];

        if ($this->inv_editando) {
            CredencialInvestigacion::findOrFail($this->inv_editando)->update($data);
        } else {
            CredencialInvestigacion::create($data);
        }

        $this->resetInv();
        $this->dispatch('notify', type: 'success', message: 'Credencial de investigación guardada.');
    }

    public function editarInvestigacion(int $id)
    {
        $r = CredencialInvestigacion::findOrFail($id);
        abort_if($r->estado === 'aprobado', 403);
        $this->inv_editando           = $id;
        $this->inv_tipo               = $r->tipo;
        $this->inv_titulo             = $r->titulo;
        $this->inv_fecha              = $r->fecha->format('Y-m-d');
        $this->inv_financiamiento     = $r->financiamiento ?? 'institucional';
        $this->inv_participacion      = $r->participacion ?? 'investigador';
        $this->inv_duracion_proyecto  = $r->duracion_proyecto ?? 'lt1anio';
        $this->inv_tipo_publicacion   = $r->tipo_publicacion ?? 'articulo_indexado';
    }

    public function eliminarInvestigacion(int $id)
    {
        $r = CredencialInvestigacion::findOrFail($id);
        abort_if($r->estado === 'aprobado', 403);
        $r->delete();
        $this->dispatch('notify', type: 'success', message: 'Registro eliminado.');
    }

    private function resetInv()
    {
        $this->inv_editando = null;
        $this->inv_tipo = 'proyecto'; $this->inv_titulo = ''; $this->inv_fecha = '';
        $this->inv_financiamiento = 'institucional'; $this->inv_participacion = 'investigador';
        $this->inv_duracion_proyecto = 'lt1anio'; $this->inv_tipo_publicacion = 'articulo_indexado';
        $this->resetValidation();
    }

    // ── CRUD: Seguimiento ─────────────────────────────────────────────────────

    public function guardarSeguimiento()
    {
        $this->validate([
            'seg_tipo'        => 'required|in:grado_adicional,curso,coordinacion_comision,idioma',
            'seg_descripcion' => 'required|string|max:255',
            'seg_horas'       => 'required_if:seg_tipo,curso|nullable|integer|min:20|max:60',
            'seg_fecha'       => 'required|date',
        ], [
            'seg_horas.required_if' => 'Las horas son requeridas para cursos.',
            'seg_horas.min'         => 'Los cursos deben tener mínimo 20 horas.',
            'seg_horas.max'         => 'Los cursos de seguimiento tienen máximo 60 horas.',
        ]);

        $puntaje = CredencialSeguimiento::calcularPuntaje(
            $this->seg_tipo,
            $this->seg_tipo === 'curso' ? (int) $this->seg_horas : null
        );

        $data = [
            'docente_id'  => $this->docenteId,
            'tipo'        => $this->seg_tipo,
            'descripcion' => $this->seg_descripcion,
            'horas'       => $this->seg_tipo === 'curso' ? (int) $this->seg_horas : null,
            'fecha'       => $this->seg_fecha,
            'puntaje'     => $puntaje,
        ];

        if ($this->seg_editando) {
            CredencialSeguimiento::findOrFail($this->seg_editando)->update($data);
        } else {
            CredencialSeguimiento::create($data);
        }

        $this->resetSeg();
        $this->dispatch('notify', type: 'success', message: 'Credencial de seguimiento guardada.');
    }

    public function editarSeguimiento(int $id)
    {
        $r = CredencialSeguimiento::findOrFail($id);
        abort_if($r->estado === 'aprobado', 403);
        $this->seg_editando    = $id;
        $this->seg_tipo        = $r->tipo;
        $this->seg_descripcion = $r->descripcion;
        $this->seg_horas       = (string) ($r->horas ?? '');
        $this->seg_fecha       = $r->fecha->format('Y-m-d');
    }

    public function eliminarSeguimiento(int $id)
    {
        $r = CredencialSeguimiento::findOrFail($id);
        abort_if($r->estado === 'aprobado', 403);
        $r->delete();
        $this->dispatch('notify', type: 'success', message: 'Registro eliminado.');
    }

    private function resetSeg()
    {
        $this->seg_editando = null;
        $this->seg_tipo = 'grado_adicional'; $this->seg_descripcion = '';
        $this->seg_horas = ''; $this->seg_fecha = '';
        $this->resetValidation();
    }

    // ── Aprobación (solo admin) ───────────────────────────────────────────────

    public function aprobarCredencial(string $tipo, int $id): void
    {
        abort_if(!$this->esAdmin, 403);
        $this->cambiarEstadoCredencial($tipo, $id, 'aprobado');
        $this->dispatch('notify', type: 'success', message: 'Credencial aprobada.');
    }

    public function rechazarCredencial(string $tipo, int $id): void
    {
        abort_if(!$this->esAdmin, 403);
        $this->cambiarEstadoCredencial($tipo, $id, 'rechazado');
        $this->dispatch('notify', type: 'warning', message: 'Credencial rechazada.');
    }

    private function cambiarEstadoCredencial(string $tipo, int $id, string $estado): void
    {
        $modelo = match ($tipo) {
            'capacitacion'    => CredencialCapacitacion::class,
            'proyeccion'      => CredencialProyeccionSocial::class,
            'especializacion' => CredencialEspecializacion::class,
            'investigacion'   => CredencialInvestigacion::class,
            'seguimiento'     => CredencialSeguimiento::class,
            default           => abort(400),
        };

        $credencial = $modelo::findOrFail($id);
        abort_if($credencial->docente_id !== $this->docenteId, 403);
        $credencial->update(['estado' => $estado]);
    }
};
?>

<div class="p-4" x-data="{ tab: 'capacitacion' }">

    <div class="flex items-center justify-between mb-2">
        <h1 class="text-2xl font-bold">Credenciales Escalafonarias</h1>
        @if ($this->esAdmin)
            <a href="{{ route('users.info', ['id' => $docenteId]) }}" wire:navigate
               class="text-sm text-ues underline">← Volver al perfil del docente</a>
        @endif
    </div>
    <p class="text-sm text-gray-500 mb-6">
        @if ($this->esAdmin)
            Modo revisión — puede aprobar o rechazar cada credencial.
        @else
            Solo se cuentan registros de los últimos 5 años, salvo grados académicos.
        @endif
    </p>

    {{-- Resumen de puntajes --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        @foreach ([
            ['Cap. Didáctica', $this->totalCapacitacion, 'capacitacion', 8],
            ['Proyección Social', $this->totalProyeccion, 'proyeccion', 9],
            ['Especialización', $this->totalEspecializacion, 'especializacion', 12],
            ['Investigación', $this->totalInvestigacion, 'investigacion', 9],
            ['Seg. Curricular', $this->totalSeguimiento, 'seguimiento', 6],
        ] as [$label, $total, $key, $max])
            <div x-on:click="tab = '{{ $key }}'"
                class="p-3 rounded-xl border cursor-pointer transition-all"
                x-bind:class="tab === '{{ $key }}' ? 'border-ues bg-ues/10' : 'border-outline hover:border-ues/50 dark:border-outline-dark'">
                <p class="text-xs text-gray-500">{{ $label }}</p>
                <p class="text-xl font-bold" x-bind:class="tab === '{{ $key }}' ? 'text-ues' : ''">
                    {{ number_format($total, 2) }}
                    <span class="text-xs font-normal text-gray-400">/ {{ $max }}</span>
                </p>
            </div>
        @endforeach
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 border-b border-outline dark:border-outline-dark mb-6 overflow-x-auto">
        @foreach ([
            ['capacitacion',  'Capacitación'],
            ['proyeccion',    'Proyección Social'],
            ['especializacion','Especialización'],
            ['investigacion', 'Investigación'],
            ['seguimiento',   'Seg. Curricular'],
        ] as [$key, $label])
            <button x-on:click="tab = '{{ $key }}'"
                x-bind:class="tab === '{{ $key }}'
                    ? 'font-bold bg-ues text-white border-b-2 border-primary'
                    : 'text-on-surface hover:border-b-2 hover:border-b-outline-strong font-medium'"
                class="px-4 py-2 text-sm whitespace-nowrap">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ── TAB: CAPACITACIÓN ── --}}
    <div x-show="tab === 'capacitacion'" x-cloak>
        @include('pages.credenciales.partials.capacitacion', ['esAdmin' => $this->esAdmin])
    </div>

    {{-- ── TAB: PROYECCIÓN SOCIAL ── --}}
    <div x-show="tab === 'proyeccion'" x-cloak>
        @include('pages.credenciales.partials.proyeccion', ['esAdmin' => $this->esAdmin])
    </div>

    {{-- ── TAB: ESPECIALIZACIÓN ── --}}
    <div x-show="tab === 'especializacion'" x-cloak>
        @include('pages.credenciales.partials.especializacion', ['esAdmin' => $this->esAdmin])
    </div>

    {{-- ── TAB: INVESTIGACIÓN ── --}}
    <div x-show="tab === 'investigacion'" x-cloak>
        @include('pages.credenciales.partials.investigacion', ['esAdmin' => $this->esAdmin])
    </div>

    {{-- ── TAB: SEGUIMIENTO CURRICULAR ── --}}
    <div x-show="tab === 'seguimiento'" x-cloak>
        @include('pages.credenciales.partials.seguimiento', ['esAdmin' => $this->esAdmin])
    </div>

</div>
