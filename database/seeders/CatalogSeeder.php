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
        $sex_catalog = CatalogType::create((["name" => 'Sex Catalog', "value" => 'Sex Catalog']));

        CatalogValue::create([
            'name' => 'Masculino',
            "value" => 'M',
            "catalog_type_id" => $sex_catalog->id,
        ]);
    }
}
