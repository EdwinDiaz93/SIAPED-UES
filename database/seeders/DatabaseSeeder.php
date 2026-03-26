<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $admin = Role::create(([
            "name" => "admin"
        ]));
        $docente = Role::create(([
            "name" => "docente"
        ]));

        $inactivo = Role::create(([
            "name" => "inactivo"
        ]));
        $jefeInmediato = Role::create(([
            "name" => "Jefe"
        ]));

        // permissos para el rol inactivo
        $account_details = Permission::create(["name" => "account.details"]);
        $inactivo->givePermissionTo([$account_details]);

        $this->call(CatalogSeeder::class);
    }
}
