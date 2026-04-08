<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudPromocion extends Model
{
    protected $table = 'solicitudes_promocion';

    protected $fillable = [
        'docente_id',
        'periodo_id',
        'formulario_id',
        'categoria_actual',
        'categoria_solicitada',
        'puntaje_obtenido',
        'puntaje_requerido',
        'estado',
        'revisado_por',
        'fecha_revision',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_revision'  => 'datetime',
            'puntaje_obtenido'=> 'decimal:2',
            'puntaje_requerido'=> 'decimal:2',
        ];
    }

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    public function periodo()
    {
        return $this->belongsTo(PeriodoEvaluacion::class, 'periodo_id');
    }

    public function formulario()
    {
        return $this->belongsTo(FormularioConsolidado::class, 'formulario_id');
    }

    public function revisadoPor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getBadgeColorAttribute(): string
    {
        return match ($this->estado) {
            'aprobada'  => 'bg-green-100 text-green-800',
            'rechazada' => 'bg-red-100 text-red-800',
            default     => 'bg-yellow-100 text-yellow-800',
        };
    }

    /**
     * Ejecuta la promoción: actualiza la categoría en institutions
     * usando el catalog_value correspondiente.
     */
    public function ejecutarPromocion(): void
    {
        $nuevaCategoria = \App\Models\CatalogValue::where('value', strtolower($this->categoria_solicitada))
            ->whereHas('catalogType', fn($q) => $q->where('value', 'Categoria Escalafonaria'))
            ->first();

        if ($nuevaCategoria) {
            \App\Models\Institution::where('user_id', $this->docente_id)
                ->update(['categoria_id' => $nuevaCategoria->id]);
        }
    }
}
