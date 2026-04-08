<?php

namespace Database\Seeders;

use App\Models\CatalogType;
use App\Models\CatalogValue;
use Illuminate\Database\Seeder;

class TipoNombramientoSeeder extends Seeder
{
    public function run(): void
    {
        $tipo = CatalogType::create([
            'name'  => 'Tipo Nombramiento',
            'value' => 'Tipo Nombramiento',
        ]);

        $nombramientos = [
            ['name' => 'Tiempo Completo', 'value' => 'tiempo_completo'],
            ['name' => 'Medio Tiempo',    'value' => 'medio_tiempo'],
            ['name' => 'Cuarto de Tiempo','value' => 'cuarto_tiempo'],
        ];

        foreach ($nombramientos as $item) {
            CatalogValue::create([
                'name'            => $item['name'],
                'value'           => $item['value'],
                'catalog_type_id' => $tipo->id,
            ]);
        }
    }
}
