<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogValue extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'value', 'catalog_type_id'];

    public function catalogType()
    {
        return $this->belongsTo(CatalogType::class);
    }
}
