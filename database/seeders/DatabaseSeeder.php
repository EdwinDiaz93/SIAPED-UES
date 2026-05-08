<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
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

        $admin         = Role::firstOrCreate(['name' => 'admin']);
        $docente       = Role::firstOrCreate(['name' => 'docente']);
        $inactivo      = Role::firstOrCreate(['name' => 'inactivo']);
        $jefeInmediato = Role::firstOrCreate(['name' => 'Jefe']);

        $account_details = Permission::firstOrCreate(['name' => 'account.details']);
        $manageUsers     = Permission::firstOrCreate(['name' => 'manage.users']);
        $managePeriodos  = Permission::firstOrCreate(['name' => 'manage.periodos']);
        $manageEval      = Permission::firstOrCreate(['name' => 'manage.evaluaciones']);
        $manageReportes  = Permission::firstOrCreate(['name' => 'manage.reportes']);
        $manageProm      = Permission::firstOrCreate(['name' => 'manage.promociones']);
        $fillJefe        = Permission::firstOrCreate(['name' => 'fill.cuestionario.jefe']);
        $fillAuto        = Permission::firstOrCreate(['name' => 'fill.cuestionario.auto']);
        $fillCred        = Permission::firstOrCreate(['name' => 'fill.credenciales']);
        $solicitarProm   = Permission::firstOrCreate(['name' => 'solicitar.promocion']);

        $admin->syncPermissions([
            $account_details, $manageUsers, $managePeriodos,
            $manageEval, $manageReportes, $manageProm, $fillCred,
        ]);

        $docente->syncPermissions([
            $account_details, $fillAuto, $fillCred, $solicitarProm,
        ]);

        $jefeInmediato->syncPermissions([
            $account_details, $fillJefe,
        ]);

        $inactivo->syncPermissions([
            $account_details,
        ]);

        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole($admin);
        }

        $this->call(CatalogSeeder::class);
        $this->call(TipoNombramientoSeeder::class);
    }
}
