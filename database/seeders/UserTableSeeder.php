<?php

namespace Database\Seeders;

use App\Models\User;
use DB;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager =
            [

                'email' => 'manager@gmail.com',
                'username' => 'managerial',
                'name' => 'manager',
                'password' => bcrypt('manager123'),
            ];
        $employee =
            [

                'email' => 'employee@gmail.com',
                'username' => 'employers',
                'name' => 'employee',
                'password' => bcrypt('employee123'),
            ];

        DB::transaction(function () use ($manager, $employee) {

            $createManager = User::create($manager);

            $createManager->syncRoles('manager');

            $createEmployee = User::create($employee);

            $createEmployee->syncRoles('employee');

        });
    }
}
