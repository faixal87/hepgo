<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect(config('hep.permissions'))
            ->mapWithKeys(fn (string $permission): array => [
                $permission => Permission::findOrCreate($permission, 'web'),
            ]);

        $roles = collect(array_keys(config('hep.roles')))
            ->mapWithKeys(fn (string $role): array => [
                $role => Role::findOrCreate($role, 'web'),
            ]);

        $roles['super_admin']->syncPermissions($permissions->values());

        $roles['hep_admin']->syncPermissions([
            'view dashboard',
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage roles',
            'access api',
            'view owners',
            'create owners',
            'edit owners',
            'delete owners',
            'verify owners',
            'view properties',
            'create properties',
            'edit properties',
            'delete properties',
            'verify properties',
            'update property availability',
            'view areas',
            'create areas',
            'edit areas',
            'delete areas',
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            'view facilities',
            'create facilities',
            'edit facilities',
            'delete facilities',
            'view reports',
            'create reports',
            'edit reports',
            'resolve reports',
            'export reports',
        ]);

        $roles['hep_staff']->syncPermissions([
            'view dashboard',
            'access api',
            'view users',
            'edit users',
            'view owners',
            'create owners',
            'edit owners',
            'verify owners',
            'view properties',
            'create properties',
            'edit properties',
            'verify properties',
            'update property availability',
            'view areas',
            'view categories',
            'view facilities',
            'view reports',
            'create reports',
            'edit reports',
            'resolve reports',
        ]);

        $roles['owner']->syncPermissions(['view dashboard']);
        $roles['student']->syncPermissions(['view dashboard']);
        $roles['parent']->syncPermissions(['view dashboard']);
        $roles['api_client']->syncPermissions(['access api']);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
