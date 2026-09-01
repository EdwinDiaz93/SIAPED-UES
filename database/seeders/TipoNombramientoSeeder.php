<?php

namespace Database\Seeders;

use App\Models\CatalogType;
use App\Models\CatalogValue;
use Illuminate\Database\Seeder;

class TipoNombramientoSeeder extends Seeder
{
    /**
     * Idempotente: seguro de re-ejecutar en instancias que ya tienen este catálogo.
     */
    public function run(): void
    {
        $tipo = CatalogType::firstOrCreate(
            ['value' => 'Tipo Nombramiento'],
            ['name' => 'Tipo Nombramiento']
        );

        $nombramientos = [
            ['name' => 'Tiempo Completo', 'value' => 'tiempo_completo'],
            ['name' => 'Medio Tiempo',    'value' => 'medio_tiempo'],
            ['name' => 'Cuarto de Tiempo','value' => 'cuarto_tiempo'],
        ];

        foreach ($nombramientos as $item) {
            CatalogValue::firstOrCreate(
                ['catalog_type_id' => $tipo->id, 'value' => $item['value']],
                ['name' => $item['name']]
            );
        }
    }
}
