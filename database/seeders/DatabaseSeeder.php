<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@ues.edu.sv'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin'),
                'email_verified_at' => now(),
            ]
        );

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
