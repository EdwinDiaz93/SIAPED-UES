<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisosSeeder extends Seeder
{
    public function run(): void
    {
        // Permisos
        $periodos     = Permission::firstOrCreate(['name' => 'manage.periodos']);
        $evalAdmin    = Permission::firstOrCreate(['name' => 'manage.evaluaciones']);
        $evalJefe     = Permission::firstOrCreate(['name' => 'fill.cuestionario.jefe']);
        $evalDocente  = Permission::firstOrCreate(['name' => 'fill.cuestionario.auto']);
        $credenciales = Permission::firstOrCreate(['name' => 'fill.credenciales']);
        $reportes     = Permission::firstOrCreate(['name' => 'manage.reportes']);

        $admin   = Role::where('name', 'admin')->first();
        $jefe    = Role::where('name', 'Jefe')->first();
        $docente = Role::where('name', 'docente')->first();

        if ($admin) {
            $admin->givePermissionTo([$periodos, $evalAdmin]);
        }
        if ($jefe) {
            $jefe->givePermissionTo([$evalJefe]);
        }
        if ($docente) {
            $docente->givePermissionTo([$evalDocente, $credenciales]);
        }

        // Admin también puede ver/gestionar credenciales y reportes
        if ($admin) {
            $admin->givePermissionTo([$credenciales, $reportes]);
        }
    }
}
