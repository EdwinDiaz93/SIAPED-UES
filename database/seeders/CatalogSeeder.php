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
        $sex_catalog = CatalogType::firstOrCreate(['value' => 'Sexo'], ['name' => 'Sexo']);

        $this->value($sex_catalog, 'Masculino', 'M');
        $this->value($sex_catalog, 'Femenino', 'F');

        // Catalog para nacionalidad 2
        $nacionalidad_catalog = CatalogType::firstOrCreate(['value' => 'Nacionalidades'], ['name' => 'Nacionalidades']);

        $data = [
            ['name' => 'Argentino/a', 'value' => 'AR'],
            ['name' => 'Boliviano/a', 'value' => 'BO'],
            ['name' => 'Brasileño/a', 'value' => 'BR'],
            ['name' => 'Chileno/a', 'value' => 'CL'],
            ['name' => 'Colombiano/a', 'value' => 'CO'],
            ['name' => 'Costarricense', 'value' => 'CR'],
            ['name' => 'Cubano/a', 'value' => 'CU'],
            ['name' => 'Dominicano/a', 'value' => 'DO'],
            ['name' => 'Ecuatoriano/a', 'value' => 'EC'],
            ['name' => 'Salvadoreño/a', 'value' => 'SV'],
            ['name' => 'Guatemalteco/a', 'value' => 'GT'],
            ['name' => 'Hondureño/a', 'value' => 'HN'],
            ['name' => 'Mexicano/a', 'value' => 'MX'],
            ['name' => 'Nicaragüense', 'value' => 'NI'],
            ['name' => 'Panameño/a', 'value' => 'PA'],
            ['name' => 'Paraguayo/a', 'value' => 'PY'],
            ['name' => 'Peruano/a', 'value' => 'PE'],
            ['name' => 'Puertorriqueño/a', 'value' => 'PR'],
            ['name' => 'Uruguayo/a', 'value' => 'UY'],
            ['name' => 'Venezolano/a', 'value' => 'VE'],
        ];

        foreach ($data as $item) {
            $this->value($nacionalidad_catalog, $item['name'], $item['value']);
        }

        // Catalogo de estado civil
        $estado_civil = CatalogType::firstOrCreate(['value' => 'Estado Civil'], ['name' => 'Estado Civil']);

        $this->value($estado_civil, 'Soltero/a', 'S');
        $this->value($estado_civil, 'Casado/a', 'C');
        $this->value($estado_civil, 'Divorciado/a', 'D');

        $documento = CatalogType::firstOrCreate(['value' => 'Documents'], ['name' => 'Documentos']);

        $this->value($documento, 'Dui', 'dui');
        $this->value($documento, 'Isss', 'isss');
        $this->value($documento, 'Afp', 'afp');

        $grado_academico = CatalogType::firstOrCreate(['value' => 'Grado Academico'], ['name' => 'Grado Academico']);

        $this->value($grado_academico, 'Técnico', 'técnico');
        $this->value($grado_academico, 'Licenciatura', 'licenciatura');
        $this->value($grado_academico, 'Ingeniería', 'ingeniería');
        $this->value($grado_academico, 'Maestría', 'maestría');
        $this->value($grado_academico, 'Doctorado', 'doctorado');

        $instituciones = CatalogType::firstOrCreate(['value' => 'Instituciones Educativas'], ['name' => 'Instituciones Educativas']);

        $this->value($instituciones, 'Universidad De El Salvador (UES)', 'ues');
        $this->value($instituciones, 'Universidad Centroamericana “José Simeón Cañas” (UCA)', 'uca');
        $this->value($instituciones, 'Universidad Don Bosco (UDB)', 'udb');
        $this->value($instituciones, 'Universidad Tecnológica de El Salvador (UTEC)', 'utec');
        $this->value($instituciones, 'Universidad Francisco Gavidia (UFG)', 'ufg');
        $this->value($instituciones, 'Universidad Dr. José Matías Delgado (UJMD)', 'ujmd');
        $this->value($instituciones, 'Universidad Católica de El Salvador (UNICAES)', 'unicaes');
        $this->value($instituciones, 'Universidad Evangélica de El Salvador (UEES)', 'uees');
        $this->value($instituciones, 'Universidad Salvadoreña Alberto Masferrer (USAM)', 'usam');
        $this->value($instituciones, 'Universidad Politécnica de El Salvador (UPES / UPESS)', 'upes');
        $this->value($instituciones, 'Universidad Pedagógica de El Salvador', 'ups');
        $this->value($instituciones, 'Universidad Modular Abierta (UMA)', 'uma');
        $this->value($instituciones, 'Universidad Gerardo Barrios (UGB)', 'ugb');
        $this->value($instituciones, 'Universidad de Oriente (UNIVO)', 'univo');
        $this->value($instituciones, 'Universidad Panamericana de El Salvador (UPAN)', 'upan');
        $this->value($instituciones, 'Universidad Luterana Salvadoreña (ULS)', 'uls');
        $this->value($instituciones, 'Universidad Autónoma de Santa Ana (UNASA)', 'unasa');
        $this->value($instituciones, 'Universidad Albert Einstein (UAE)', 'uae');
        $this->value($instituciones, 'Universidad Cristiana de las Asambleas de Dios (UCAD)', 'ucad');
        $this->value($instituciones, 'Universidad Monseñor Óscar Arnulfo Romero (UMOAR)', 'umoar');
        $this->value($instituciones, 'Universidad Técnica Latinoamericana (UTLA)', 'utla');
        $this->value($instituciones, 'Universidad de Sonsonate (USO)', 'uso');
        $this->value($instituciones, 'Universidad Andrés Bello (UNAB)', 'unab');

        $escuelas = CatalogType::firstOrCreate(['value' => 'Escuelas'], ['name' => 'Escuelas']);

        $this->value($escuelas, 'Escuela De Ingenieria De Sistemas', 'I10515');

        $categoria_escalafonaria = CatalogType::firstOrCreate(['value' => 'Categoria Escalafonaria'], ['name' => 'Categoria Escalafonaria']);

        $this->value($categoria_escalafonaria, 'PU-I', 'pu-i');
        $this->value($categoria_escalafonaria, 'PU-II', 'pu-ii');
        $this->value($categoria_escalafonaria, 'PU-III', 'pu-iii');
        $this->value($categoria_escalafonaria, 'PU-IV', 'pu-iv');

        $area_desempeño = CatalogType::firstOrCreate(['value' => 'Area De Desempeño'], ['name' => 'Area De Desempeño']);

        $this->value($area_desempeño, 'Docencia', 'docencia');
        $this->value($area_desempeño, 'Administrativa', 'administrativa');
        $this->value($area_desempeño, 'Docencia UCB', 'docencia ucb');
        $this->value($area_desempeño, 'CIAN', 'cian');
    }

    private function value(CatalogType $catalogType, string $name, string $value): CatalogValue
    {
        return CatalogValue::firstOrCreate(
            ['catalog_type_id' => $catalogType->id, 'value' => $value],
            ['name' => $name]
        );
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
