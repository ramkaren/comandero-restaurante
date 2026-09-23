<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed the application roles, permissions, and initial super user.
     */
    public function run(): void
    {
        $permissions = [
            'users.view', 'users.create', 'users.update', 'users.toggle-status',
            'roles.view', 'roles.manage', 'permissions.manage',
            'tables.view', 'tables.select', 'tables.release', 'tables.create', 'tables.update', 'tables.toggle-status',
            'categories.view', 'categories.create', 'categories.update', 'categories.delete',
            'products.view', 'products.create', 'products.update', 'products.delete',
            'inventory.view', 'inventory.create', 'inventory.update', 'inventory.entries', 'inventory.exits',
            'orders.view', 'orders.create', 'orders.update', 'orders.delete-product', 'orders.send-to-kitchen',
            'kitchen.view', 'kitchen.update-status',
            'accounts.view',
            'payments.view', 'payments.create',
            'tickets.view', 'tickets.create',
            'sales.view',
            'cash-closing.view', 'cash-closing.create',
            'dashboard.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $allPermissions = Permission::where('guard_name', 'web')->get();
        $roles = [
            'super_usuario' => $allPermissions,
            'administrador' => [
                'users.view', 'users.create', 'users.update', 'users.toggle-status',
                'tables.view', 'tables.create', 'tables.update', 'tables.toggle-status',
                'categories.view', 'categories.create', 'categories.update', 'categories.delete',
                'products.view', 'products.create', 'products.update', 'products.delete',
                'inventory.view', 'inventory.create', 'inventory.update', 'inventory.entries', 'inventory.exits',
                'orders.view', 'orders.create', 'orders.update', 'orders.send-to-kitchen',
                'kitchen.view', 'kitchen.update-status', 'accounts.view',
                'payments.view', 'tickets.view', 'sales.view',
                'cash-closing.view', 'cash-closing.create', 'dashboard.view',
            ],
            'mesero' => [
                'tables.view', 'tables.select', 'tables.release', 'categories.view', 'products.view',
                'orders.view', 'orders.create', 'orders.update', 'orders.delete-product', 'orders.send-to-kitchen',
                'accounts.view',
            ],
            'cocinero' => ['kitchen.view', 'kitchen.update-status', 'orders.view'],
            'cajero' => [
                'accounts.view', 'payments.view', 'payments.create',
                'tickets.view', 'tickets.create', 'sales.view', 'cash-closing.view', 'cash-closing.create',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($rolePermissions);
        }

        $password = env('SUPER_USER_PASSWORD');
        $email = env('SUPER_USER_EMAIL');

        if (! $email || ! $password) {
            throw new RuntimeException(
                'Define SUPER_USER_EMAIL y SUPER_USER_PASSWORD en .env antes de ejecutar db:seed.'
            );
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('SUPER_USER_NAME', 'Super Usuario'),
                'password' => $password,
            ],
        );

        $user->assignRole('super_usuario');
    }
}