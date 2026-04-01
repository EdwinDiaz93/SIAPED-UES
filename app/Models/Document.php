<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        "user_id",
        "document_type_id",
        "value",
        "fecha_expedicion",
        "lugar_expedicion",
        "fecha_expiracion",
        "institucion"
    ];

    public function documentType()
    {
        return $this->belongsTo(CatalogValue::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
