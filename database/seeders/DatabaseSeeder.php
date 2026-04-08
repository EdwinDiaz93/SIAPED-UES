<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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

        $adminUser = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@ues.edu.sv',
            'password' => Hash::make('admin')
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

        // ── Permisos base ─────────────────────────────────────────────────────
        $account_details  = Permission::create(['name' => 'account.details']);
        $manageUsers      = Permission::create(['name' => 'manage.users']);
        $managePeriodos   = Permission::create(['name' => 'manage.periodos']);
        $manageEval       = Permission::create(['name' => 'manage.evaluaciones']);
        $manageReportes   = Permission::create(['name' => 'manage.reportes']);
        $manageProm       = Permission::create(['name' => 'manage.promociones']);
        $fillJefe         = Permission::create(['name' => 'fill.cuestionario.jefe']);
        $fillAuto         = Permission::create(['name' => 'fill.cuestionario.auto']);
        $fillCred         = Permission::create(['name' => 'fill.credenciales']);
        $solicitarProm    = Permission::create(['name' => 'solicitar.promocion']);

        // ── Asignación de permisos por rol ────────────────────────────────────
        $admin->givePermissionTo([
            $account_details,
            $manageUsers,
            $managePeriodos,
            $manageEval,
            $manageReportes,
            $manageProm,
            $fillCred,
        ]);

        $docente->givePermissionTo([
            $account_details,
            $fillAuto,
            $fillCred,
            $solicitarProm,
        ]);

        $jefeInmediato->givePermissionTo([
            $account_details,
            $fillJefe,
        ]);

        $inactivo->givePermissionTo([
            $account_details,
        ]);

        $adminUser->assignRole([$admin]);

        $this->call(CatalogSeeder::class);
        $this->call(TipoNombramientoSeeder::class);
    }
}
