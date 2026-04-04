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
        'fecha_graduacion'
    ];

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
}
