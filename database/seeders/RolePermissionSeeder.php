<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Permissions untuk menu
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'manage settings']);

        // Buat Role dan assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(['view users', 'view reports', 'manage settings']);

        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo(['view reports']);

        // Buat User Admin
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@tabler.com',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('admin');
    }
}
