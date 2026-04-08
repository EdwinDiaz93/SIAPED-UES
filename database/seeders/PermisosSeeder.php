<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Ejecutar en instancias existentes para sincronizar permisos:
 *   php artisan db:seed --class=PermisosSeeder
 */
class PermisosSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            'account.details',
            'manage.users',
            'manage.periodos',
            'manage.evaluaciones',
            'manage.reportes',
            'manage.promociones',
            'fill.cuestionario.jefe',
            'fill.cuestionario.auto',
            'fill.credenciales',
            'solicitar.promocion',
        ];

        foreach ($permisos as $nombre) {
            Permission::firstOrCreate(['name' => $nombre]);
        }

        $admin   = Role::where('name', 'admin')->first();
        $jefe    = Role::where('name', 'Jefe')->first();
        $docente = Role::where('name', 'docente')->first();
        $inactivo= Role::where('name', 'inactivo')->first();

        if ($admin) {
            $admin->syncPermissions([
                'account.details',
                'manage.users',
                'manage.periodos',
                'manage.evaluaciones',
                'manage.reportes',
                'manage.promociones',
                'fill.credenciales',
            ]);
        }

        if ($docente) {
            $docente->syncPermissions([
                'account.details',
                'fill.cuestionario.auto',
                'fill.credenciales',
                'solicitar.promocion',
            ]);
        }

        if ($jefe) {
            $jefe->syncPermissions([
                'account.details',
                'fill.cuestionario.jefe',
            ]);
        }

        if ($inactivo) {
            $inactivo->syncPermissions(['account.details']);
        }
    }
}
