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
        // Catalog para sexo de usuario 1
        $sex_catalog = CatalogType::create((["name" => 'Sexo', "value" => 'Sexo']));

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
        // Catalog para nacionalidad 2
        $nacionalidad_catalog = CatalogType::create(["name" => "Nacionalidades", "value" => "Nacionalidades"]);

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

        // Catalogo de estado civil
        $estado_civil = CatalogType::create(["name" => "Estado Civil", "value" => "Estado Civil"]);

        CatalogValue::create([
            'name' => 'Soltero/a',
            "value" => 'S',
            "catalog_type_id" => $estado_civil->id,
        ]);
        CatalogValue::create([
            'name' => 'Casado/a',
            "value" => 'C',
            "catalog_type_id" => $estado_civil->id,
        ]);
        CatalogValue::create([
            'name' => 'Divorciado/a',
            "value" => 'D',
            "catalog_type_id" => $estado_civil->id,
        ]);
        $documento = CatalogType::create(["name" => "Documentos", "value" => "Documents"]);

        CatalogValue::create([
            'name' => 'Dui',
            "value" => 'dui',
            "catalog_type_id" => $documento->id,
        ]);
        CatalogValue::create([
            'name' => 'Nit',
            "value" => 'nit',
            "catalog_type_id" => $documento->id,
        ]);
        CatalogValue::create([
            'name' => 'Isss',
            "value" => 'isss',
            "catalog_type_id" => $documento->id,
        ]);
        CatalogValue::create([
            'name' => 'Afp',
            "value" => 'afp',
            "catalog_type_id" => $documento->id,
        ]);

        $grado_academico = CatalogType::create(["name" => "Grado Academico", "value" => "Grado Academico"]);

        CatalogValue::create([
            'name' => 'Técnico',
            "value" => 'técnico',
            "catalog_type_id" => $grado_academico->id,
        ]);
        CatalogValue::create([
            'name' => 'Licenciatura',
            "value" => 'licenciatura',
            "catalog_type_id" => $grado_academico->id,
        ]);
        CatalogValue::create([
            'name' => 'Ingeniería',
            "value" => 'ingeniería',
            "catalog_type_id" => $grado_academico->id,
        ]);
        CatalogValue::create([
            'name' => 'Maestría',
            "value" => 'maestría',
            "catalog_type_id" => $grado_academico->id,
        ]);
        CatalogValue::create([
            'name' => 'Doctorado',
            "value" => 'doctorado',
            "catalog_type_id" => $grado_academico->id,
        ]);

        $instituciones = CatalogType::create(["name" => "Instituciones Educativas", "value" => "Instituciones Educativas"]);
        CatalogValue::create([
            'name' => 'Universidad Centroamericana “José Simeón Cañas” (UCA)',
            "value" => 'uca',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Don Bosco (UDB)',
            "value" => 'udb',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Tecnológica de El Salvador (UTEC)',
            "value" => 'utec',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Francisco Gavidia (UFG)',
            "value" => 'ufg',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Dr. José Matías Delgado (UJMD)',
            "value" => 'ujmd',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Católica de El Salvador (UNICAES)',
            "value" => 'unicaes',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Evangélica de El Salvador (UEES)',
            "value" => 'uees',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Salvadoreña Alberto Masferrer (USAM)',
            "value" => 'usam',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Politécnica de El Salvador (UPES / UPESS)',
            "value" => 'upes',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Pedagógica de El Salvador',
            "value" => 'ups',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Modular Abierta (UMA)',
            "value" => 'uma',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Gerardo Barrios (UGB)',
            "value" => 'ugb',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad de Oriente (UNIVO)',
            "value" => 'univo',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Panamericana de El Salvador (UPAN)',
            "value" => 'upan',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Luterana Salvadoreña (ULS)',
            "value" => 'uls',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Autónoma de Santa Ana (UNASA)',
            "value" => 'unasa',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Albert Einstein (UAE)',
            "value" => 'uae',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Cristiana de las Asambleas de Dios (UCAD)',
            "value" => 'ucad',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Monseñor Óscar Arnulfo Romero (UMOAR)',
            "value" => 'umoar',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Técnica Latinoamericana (UTLA)',
            "value" => 'utla',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad de Sonsonate (USO)',
            "value" => 'uso',
            "catalog_type_id" => $instituciones->id,
        ]);
        CatalogValue::create([
            'name' => 'Universidad Andrés Bello (UNAB)',
            "value" => 'unab',
            "catalog_type_id" => $instituciones->id,
        ]);

        $escuelas = CatalogType::create(["name" => "Escuelas", "value" => "Escuelas"]);

        CatalogValue::create([
            'name' => 'Escuela De Ingenieria De Sistemas',
            "value" => 'I10515',
            "catalog_type_id" => $escuelas->id,
        ]);


        $escuelas = CatalogType::create(["name" => "Escuelas", "value" => "Escuelas"]);

        CatalogValue::create([
            'name' => 'Escuela De Ingenieria De Sistemas',
            "value" => 'I10515',
            "catalog_type_id" => $escuelas->id,
        ]);

        $categoria_escalafonaria = CatalogType::create(["name" => "Categoria Escalafonaria", "value" => "Categoria Escalafonaria"]);

        CatalogValue::create([
            'name' => 'PU-I',
            "value" => 'pu-i',
            "catalog_type_id" => $categoria_escalafonaria->id,
        ]);
        CatalogValue::create([
            'name' => 'PU-II',
            "value" => 'pu-ii',
            "catalog_type_id" => $categoria_escalafonaria->id,
        ]);
        CatalogValue::create([
            'name' => 'PU-III',
            "value" => 'pu-iii',
            "catalog_type_id" => $categoria_escalafonaria->id,
        ]);
        CatalogValue::create([
            'name' => 'PU-IV',
            "value" => 'pu-iv',
            "catalog_type_id" => $categoria_escalafonaria->id,
        ]);

        $area_desempeño = CatalogType::create(["name" => "Area De Desempeño", "value" => "Area De Desempeño"]);

        CatalogValue::create([
            'name' => 'Docencia',
            "value" => 'docencia',
            "catalog_type_id" => $area_desempeño->id,
        ]);
        CatalogValue::create([
            'name' => 'Administrativa',
            "value" => 'administrativa',
            "catalog_type_id" => $area_desempeño->id,
        ]);
        CatalogValue::create([
            'name' => 'Docencia UCB',
            "value" => 'docencia ucb',
            "catalog_type_id" => $area_desempeño->id,
        ]);
        CatalogValue::create([
            'name' => 'CIAN',
            "value" => 'cian',
            "catalog_type_id" => $area_desempeño->id,
        ]);
    }
}
