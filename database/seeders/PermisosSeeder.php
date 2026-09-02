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
            'manage.catalogos',
            'manage.auditoria',
            'fill.cuestionario.auto',
            'fill.credenciales',
            'solicitar.promocion',
            'reportes.promocion',
            'reportes.atestados',
        ];

        foreach ($permisos as $nombre) {
            Permission::firstOrCreate(['name' => $nombre]);
        }

        $admin   = Role::firstOrCreate(['name' => 'admin']);
        $docente = Role::firstOrCreate(['name' => 'docente']);
        $comite  = Role::firstOrCreate(['name' => 'comite']);
        $inactivo= Role::firstOrCreate(['name' => 'inactivo']);
        $junta   = Role::firstOrCreate(['name' => 'junta']);

        $admin->syncPermissions([
            'account.details',
            'manage.users',
            'manage.periodos',
            'manage.evaluaciones',
            'manage.reportes',
            'manage.promociones',
            'manage.catalogos',
            'manage.auditoria',
            'fill.credenciales',
        ]);

        $docente->syncPermissions([
            'account.details',
            'fill.cuestionario.auto',
            'fill.credenciales',
            'solicitar.promocion',
        ]);

        $comite->syncPermissions([
            'account.details',
            'manage.users',
            'manage.promociones',
            'manage.periodos',
            'reportes.promocion',
            'reportes.atestados',
        ]);

        $inactivo->syncPermissions(['account.details']);

        $junta->syncPermissions([
            'account.details',
            'manage.reportes',
            'reportes.promocion',
            'reportes.atestados',
        ]);

        // El rol "Jefe" quedó obsoleto: se elimina si existe en instancias previas.
        Role::where('name', 'Jefe')->delete();
        Permission::where('name', 'fill.cuestionario.jefe')->delete();
    }
}
