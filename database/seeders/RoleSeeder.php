<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles (or get existing)
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $clientRole = Role::firstOrCreate(['name' => 'client']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create permissions for students
        $studentPermissions = [
            'create service listings',
            'edit own service listings',
            'delete own service listings',
            'view orders',
            'accept orders',
            'decline orders',
            'upload deliverables',
            'view earnings',
            'request withdrawals',
            'send messages',
            'view own profile',
            'edit own profile',
        ];

        foreach ($studentPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create permissions for clients
        $clientPermissions = [
            'browse services',
            'place orders',
            'view own orders',
            'approve deliverables',
            'request revisions',
            'leave reviews',
            'send messages',
            'view own profile',
            'edit own profile',
        ];

        foreach ($clientPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create permissions for admins
        $adminPermissions = [
            'view all users',
            'edit any user',
            'delete any user',
            'view all orders',
            'resolve disputes',
            'moderate content',
            'view all transactions',
            'manage categories',
            'view analytics',
        ];

        foreach ($adminPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $studentRole->givePermissionTo([
            'create service listings',
            'edit own service listings',
            'delete own service listings',
            'view orders',
            'accept orders',
            'decline orders',
            'upload deliverables',
            'view earnings',
            'request withdrawals',
            'send messages',
            'view own profile',
            'edit own profile',
        ]);

        $clientRole->givePermissionTo([
            'browse services',
            'place orders',
            'view own orders',
            'approve deliverables',
            'request revisions',
            'leave reviews',
            'send messages',
            'view own profile',
            'edit own profile',
        ]);

        $adminRole->givePermissionTo(Permission::all());

        $this->command->info('Roles and permissions created successfully!');
    }
}
