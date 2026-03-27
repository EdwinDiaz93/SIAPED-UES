<?php

namespace Database\Seeders;

use App\Models\CatalogType;
use App\Models\CatalogValue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Catalog para sexo de usuario
        $sex_catalog = CatalogType::create((["name" => 'Sex Catalog', "value" => 'Sex Catalog']));

        CatalogValue::create([
            'name' => 'Masculino',
            "value" => 'M',
            "catalog_type_id" => $sex_catalog->id,
        ]);
        CatalogValue::create([
            'name' => 'Femenino',
            "value" => 'F',
            "catalog_type_id" => $sex_catalog->id,
        ]);
        // Catalog para nacionalidad
        $nacionalidad_catalog = CatalogType::create(["name" => "Nacionalidad Catalog", "value" => "Nacionalidad Catalog"]);

        $data = [
            ['name' => 'Argentino/a', 'value' => 'AR', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Boliviano/a', 'value' => 'BO', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Brasileño/a', 'value' => 'BR', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Chileno/a', 'value' => 'CL', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Colombiano/a', 'value' => 'CO', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Costarricense', 'value' => 'CR', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Cubano/a', 'value' => 'CU', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Dominicano/a', 'value' => 'DO', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Ecuatoriano/a', 'value' => 'EC', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Salvadoreño/a', 'value' => 'SV', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Guatemalteco/a', 'value' => 'GT', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Hondureño/a', 'value' => 'HN', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Mexicano/a', 'value' => 'MX', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Nicaragüense', 'value' => 'NI', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Panameño/a', 'value' => 'PA', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Paraguayo/a', 'value' => 'PY', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Peruano/a', 'value' => 'PE', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Puertorriqueño/a', 'value' => 'PR', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Uruguayo/a', 'value' => 'UY', 'catalog_type_id' => $nacionalidad_catalog->id],
            ['name' => 'Venezolano/a', 'value' => 'VE', 'catalog_type_id' => $nacionalidad_catalog->id]
        ];

        foreach ($data as $item) {
            CatalogValue::updateOrCreate(
                ['value' => $item['value']],
                [
                    'name' => $item['name'],
                    'catalog_type_id' => $nacionalidad_catalog->id
                ]
            );
        }
    }
}
