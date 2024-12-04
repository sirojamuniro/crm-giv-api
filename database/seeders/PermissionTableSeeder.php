<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Schema::disableForeignKeyConstraints();

        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();

        Schema::enableForeignKeyConstraints();

        Role::create(['name' => 'manager', 'guard_name' => 'api']);
        Role::create(['name' => 'employee', 'guard_name' => 'api']);

        $permissions = [
            'list-employee',
            'show-employee',
            'update-employee',
            'create-employee',
            'delete-employee',

            'list-manager',
            'show-manager',
            'update-manager',
            'create-manager',
            'delete-manager',
        ];

        foreach ($permissions as $permission) {
            $guardName = 'api';

            Permission::create(['name' => $permission, 'guard_name' => $guardName]);
        }

        $roles = Role::get();

        foreach ($roles as $role) {
            $roleName = $role->name;

            $ids = [];

            if ($roleName == 'manager') {
                $ids = Permission::whereIn('name', $this->getManagerPermission())->pluck('id');
            } elseif ($roleName == 'employee') {
                $ids = Permission::whereIn('name', $this->getEmployeePermission())->pluck('id');
            }
            $role->syncPermissions($ids);
        }
    }

    private function getManagerPermission()
    {
        $permissions = [

            'list-employee',
            'show-employee',
            'update-employee',
            'create-employee',
            'delete-employee',

            'list-manager',
            'show-manager',
            'update-manager',
            'create-manager',
            'delete-manager',

        ];

        return $permissions;
    }

    private function getEmployeePermission()
    {
        $permissions = [
            'list-employee',
            'show-employee',
        ];

        return $permissions;
    }
}
