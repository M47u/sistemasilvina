<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'Coordinadora']);
        Role::firstOrCreate(['name' => 'Profesional']);
        Role::firstOrCreate(['name' => 'Administrativo']);

        // Usuario coordinadora por defecto (cambiar contraseña en producción)
        $user = User::firstOrCreate(
            ['email' => 'coordinadora@silvina.gob.ar'],
            [
                'name'     => 'Coordinadora',
                'password' => Hash::make('Silvina2025!'),
            ]
        );

        $user->assignRole('Coordinadora');
    }
}
