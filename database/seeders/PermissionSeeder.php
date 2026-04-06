<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'create_application',
            'view_application',
            'review_application',
            'approve_application',
            'manage_users'
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $admin = Role::where('name', 'admin')->first();
        $coordinador = Role::where('name', 'coordinador')->first();
        $aspirante = Role::where('name', 'aspirante')->first();

        // Admin → todo
        $admin->permissions()->sync(Permission::all());

        // Coordinador
        $coordinador->permissions()->sync(
            Permission::whereIn('name', [
                'review_application',
                'approve_application'
            ])->pluck('id')
        );

        // Aspirante
        $aspirante->permissions()->sync(
            Permission::whereIn('name', [
                'create_application',
                'view_application'
            ])->pluck('id')
        );
    }
}