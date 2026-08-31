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

        $admin = Role::create(([
            "name" => "admin"
        ]));
        $docente = Role::create(([
            "name" => "docente"
        ]));

        $inactivo = Role::create(([
            "name" => "inactivo"
        ]));
        $comite = Role::create(([
            "name" => "comite"
        ]));

        // ── Permisos base ─────────────────────────────────────────────────────
        $account_details  = Permission::create(['name' => 'account.details']);
        $manageUsers      = Permission::create(['name' => 'manage.users']);
        $managePeriodos   = Permission::create(['name' => 'manage.periodos']);
        $manageEval       = Permission::create(['name' => 'manage.evaluaciones']);
        $manageReportes   = Permission::create(['name' => 'manage.reportes']);
        $manageProm       = Permission::create(['name' => 'manage.promociones']);
        $manageCatalogos  = Permission::create(['name' => 'manage.catalogos']);
        $manageAuditoria  = Permission::create(['name' => 'manage.auditoria']);
        $fillAuto         = Permission::create(['name' => 'fill.cuestionario.auto']);
        $fillCred         = Permission::create(['name' => 'fill.credenciales']);
        $solicitarProm    = Permission::create(['name' => 'solicitar.promocion']);
        $reportesProm     = Permission::create(['name' => 'reportes.promocion']);
        $reportesAtestados= Permission::create(['name' => 'reportes.atestados']);

        // ── Asignación de permisos por rol ────────────────────────────────────
        $admin->givePermissionTo([
            $account_details,
            $manageUsers,
            $managePeriodos,
            $manageEval,
            $manageReportes,
            $manageProm,
            $manageCatalogos,
            $manageAuditoria,
            $fillCred,
        ]);

        $jefeInmediato->syncPermissions([
            $account_details, $fillJefe,
        ]);

        $comite->givePermissionTo([
            $account_details,
            $manageUsers,
            $manageProm,
            $managePeriodos,
            $reportesProm,
            $reportesAtestados,
        ]);

        $inactivo->givePermissionTo([
            $account_details,
        ]);

        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole($admin);
        }

        $this->call(CatalogSeeder::class);
        $this->call(TipoNombramientoSeeder::class);
    }
}
