<?php

namespace Database\Seeders;

use App\Models\CatalogType;
use App\Models\CatalogValue;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
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
            'name' => 'Isss',
            "value" => 'isss',
            "catalog_type_id" => $documento->id,
        ]);
        CatalogValue::create([
            'name' => 'Afp',
            "value" => 'afp',
            "catalog_type_id" => $documento->id,
        ]);

        $this->seedCatalog('Nacionalidades', 'Nacionalidades', [
            ['name' => 'Argentino/a',      'value' => 'AR'],
            ['name' => 'Boliviano/a',      'value' => 'BO'],
            ['name' => 'Brasileño/a',      'value' => 'BR'],
            ['name' => 'Chileno/a',        'value' => 'CL'],
            ['name' => 'Colombiano/a',     'value' => 'CO'],
            ['name' => 'Costarricense',    'value' => 'CR'],
            ['name' => 'Cubano/a',         'value' => 'CU'],
            ['name' => 'Dominicano/a',     'value' => 'DO'],
            ['name' => 'Ecuatoriano/a',    'value' => 'EC'],
            ['name' => 'Salvadoreño/a',    'value' => 'SV'],
            ['name' => 'Guatemalteco/a',   'value' => 'GT'],
            ['name' => 'Hondureño/a',      'value' => 'HN'],
            ['name' => 'Mexicano/a',       'value' => 'MX'],
            ['name' => 'Nicaragüense',     'value' => 'NI'],
            ['name' => 'Panameño/a',       'value' => 'PA'],
            ['name' => 'Paraguayo/a',      'value' => 'PY'],
            ['name' => 'Peruano/a',        'value' => 'PE'],
            ['name' => 'Puertorriqueño/a', 'value' => 'PR'],
            ['name' => 'Uruguayo/a',       'value' => 'UY'],
            ['name' => 'Venezolano/a',     'value' => 'VE'],
        ]);

        $this->seedCatalog('Estado Civil', 'Estado Civil', [
            ['name' => 'Soltero/a',    'value' => 'S'],
            ['name' => 'Casado/a',     'value' => 'C'],
            ['name' => 'Divorciado/a', 'value' => 'D'],
        ]);

        $this->seedCatalog('Documentos', 'Documents', [
            ['name' => 'Dui',  'value' => 'dui'],
            ['name' => 'Nit',  'value' => 'nit'],
            ['name' => 'Isss', 'value' => 'isss'],
            ['name' => 'Afp',  'value' => 'afp'],
        ]);

        $this->seedCatalog('Grado Academico', 'Grado Academico', [
            ['name' => 'Técnico',      'value' => 'técnico'],
            ['name' => 'Licenciatura', 'value' => 'licenciatura'],
            ['name' => 'Ingeniería',   'value' => 'ingeniería'],
            ['name' => 'Maestría',     'value' => 'maestría'],
            ['name' => 'Doctorado',    'value' => 'doctorado'],
        ]);

        $this->seedCatalog('Instituciones Educativas', 'Instituciones Educativas', [
            ['name' => 'Universidad De El Salvador (UES)',                        'value' => 'ues'],
            ['name' => 'Universidad Centroamericana "José Simeón Cañas" (UCA)',   'value' => 'uca'],
            ['name' => 'Universidad Don Bosco (UDB)',                             'value' => 'udb'],
            ['name' => 'Universidad Tecnológica de El Salvador (UTEC)',           'value' => 'utec'],
            ['name' => 'Universidad Francisco Gavidia (UFG)',                     'value' => 'ufg'],
            ['name' => 'Universidad Dr. José Matías Delgado (UJMD)',              'value' => 'ujmd'],
            ['name' => 'Universidad Católica de El Salvador (UNICAES)',           'value' => 'unicaes'],
            ['name' => 'Universidad Evangélica de El Salvador (UEES)',            'value' => 'uees'],
            ['name' => 'Universidad Salvadoreña Alberto Masferrer (USAM)',        'value' => 'usam'],
            ['name' => 'Universidad Politécnica de El Salvador (UPES / UPESS)',   'value' => 'upes'],
            ['name' => 'Universidad Pedagógica de El Salvador',                   'value' => 'ups'],
            ['name' => 'Universidad Modular Abierta (UMA)',                       'value' => 'uma'],
            ['name' => 'Universidad Gerardo Barrios (UGB)',                       'value' => 'ugb'],
            ['name' => 'Universidad de Oriente (UNIVO)',                          'value' => 'univo'],
            ['name' => 'Universidad Panamericana de El Salvador (UPAN)',          'value' => 'upan'],
            ['name' => 'Universidad Luterana Salvadoreña (ULS)',                  'value' => 'uls'],
            ['name' => 'Universidad Autónoma de Santa Ana (UNASA)',               'value' => 'unasa'],
            ['name' => 'Universidad Albert Einstein (UAE)',                        'value' => 'uae'],
            ['name' => 'Universidad Cristiana de las Asambleas de Dios (UCAD)',   'value' => 'ucad'],
            ['name' => 'Universidad Monseñor Óscar Arnulfo Romero (UMOAR)',       'value' => 'umoar'],
            ['name' => 'Universidad Técnica Latinoamericana (UTLA)',              'value' => 'utla'],
            ['name' => 'Universidad de Sonsonate (USO)',                          'value' => 'uso'],
            ['name' => 'Universidad Andrés Bello (UNAB)',                         'value' => 'unab'],
        ]);

        $this->seedCatalog('Escuelas', 'Escuelas', [
            ['name' => 'Escuela De Ingenieria De Sistemas', 'value' => 'I10515'],
        ]);

        $this->seedCatalog('Categoria Escalafonaria', 'Categoria Escalafonaria', [
            ['name' => 'PU-I',   'value' => 'pu-i'],
            ['name' => 'PU-II',  'value' => 'pu-ii'],
            ['name' => 'PU-III', 'value' => 'pu-iii'],
            ['name' => 'PU-IV',  'value' => 'pu-iv'],
        ]);

        $this->seedCatalog('Area De Desempeño', 'Area De Desempeño', [
            ['name' => 'Docencia',       'value' => 'docencia'],
            ['name' => 'Administrativa', 'value' => 'administrativa'],
            ['name' => 'Docencia UCB',   'value' => 'docencia ucb'],
            ['name' => 'CIAN',           'value' => 'cian'],
        ]);
    }

    private function seedCatalog(string $name, string $value, array $items): void
    {
        $type = CatalogType::firstOrCreate(['value' => $value], ['name' => $name]);

        foreach ($items as $item) {
            CatalogValue::firstOrCreate(
                ['value' => $item['value'], 'catalog_type_id' => $type->id],
                ['name' => $item['name']]
            );
        }
    }
}
