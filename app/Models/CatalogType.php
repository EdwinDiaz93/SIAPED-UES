<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatalogType extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'value'];

    public function catalogValues()
    {
        return $this->hasMany(CatalogValue::class);
    }
}
