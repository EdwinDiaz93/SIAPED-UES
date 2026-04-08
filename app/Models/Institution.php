<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected  $fillable = [
        'user_id',
        'grado_id',
        'institucion_id',
        'escuela_id',
        'categoria_id',
        'area_id',
        'fecha_graduacion',
        'fecha_ingreso',
        'tipo_nombramiento_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_graduacion' => 'date',
            'fecha_ingreso'    => 'date',
        ];
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function grado(){
        return $this->belongsTo(CatalogValue::class,'grado_id');
    }
    public function institucion(){
        return $this->belongsTo(CatalogValue::class,'institucion_id');
    }
    public function escuela(){
        return $this->belongsTo(CatalogValue::class,'escuela_id');
    }
    public function categoria(){
        return $this->belongsTo(CatalogValue::class,'categoria_id');
    }
    public function area(){
        return $this->belongsTo(CatalogValue::class,'area_id');
    }

    public function tipoNombramiento(){
        return $this->belongsTo(CatalogValue::class,'tipo_nombramiento_id');
    }

    /**
     * Puntaje de Tiempo de Servicio calculado según reglamento:
     * Tiempo completo: 2.0 pts/año | Medio tiempo: 1.0 | Cuarto: 0.5
     */
    public function getPuntajeTiempoServicioAttribute(): float
    {
        if (!$this->fecha_ingreso || !$this->tipoNombramiento) {
            return 0;
        }

        $anos = \Carbon\Carbon::parse($this->fecha_ingreso)->diffInYears(now());

        $multiplier = match ($this->tipoNombramiento->value) {
            'tiempo_completo' => 2.0,
            'medio_tiempo'    => 1.0,
            'cuarto_tiempo'   => 0.5,
            default           => 0,
        };

        return round($anos * $multiplier, 2);
    }
}
