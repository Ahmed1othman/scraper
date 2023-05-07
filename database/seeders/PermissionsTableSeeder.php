<?php

namespace Database\Seeders;

use App\Http\Controllers\Admin\RoleController;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'setting' => [
                'view setting',
                'delete setting',
                'edit setting',
                'add setting',
            ],
            'users' => [
                'view users',
                'delete users',
                'edit users',
                'add users',
            ],
            'products' => [
                'view products',
                'delete products',
                'edit products',
                'add products',
            ],
            // add more permissions as needed
        ];

        foreach ($permissions as $group => $permis) {
            foreach ($permis as $permission) {
                $name = $group . '-' . $permission;
                Permission::updateOrCreate(
                    ['name' => $name, 'group' => $group,],
                    ['name' => $name, 'group' => $group,],
                );
            }
        }

        $user1 = User::updateOrCreate(
            ['email' => 'super@email.com'],
            ['name' => 'super admin', 'email' => 'super@email.com','phone'=>'01011052263', 'password' =>'12345678','status'=>1]
        );
        $user2 = User::updateOrCreate(
            ['email' => 'admin@email.com'],
            ['name' => 'Mohamed Adel', 'email' => 'admin@email.com','phone'=>'01023774267', 'password' => '12345678','status'=>1]
        );

        $adminRole = Role::updateOrCreate(['name' => 'Super Admin'],['name' => 'Super Admin']);
        $user1->assignRole($adminRole);
        $user2->assignRole($adminRole);

        $userRole = Role::updateOrCreate(['name' => 'normal user'],['name' => 'normal user']);
        $userPermissions = Permission::whereIn('group',['products'])->get('name')->toArray();
        $userRole->syncPermissions($userPermissions);
    }
}
