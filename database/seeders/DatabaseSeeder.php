<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Idempotente: seguro de re-ejecutar (ej. en cada deploy) sin chocar
     * con roles, permisos, catálogos o el usuario admin ya existentes.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@ues.edu.sv'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin'),
            ]
        );

        // Crea/actualiza roles, permisos y su asignación (ver PermisosSeeder).
        $this->call(PermisosSeeder::class);

        $admin = Role::where('name', 'admin')->first();
        if ($admin && !$adminUser->hasRole($admin)) {
            $adminUser->assignRole($admin);
        }

        $this->call(CatalogSeeder::class);
        $this->call(TipoNombramientoSeeder::class);
    }
}
