<?php

namespace Modules\UserManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\UserManagement\Entities\Roles;
use Modules\UserManagement\Entities\UserRole;

class UserRoleSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'role_name' => 'Super Admin',
            ],
            [
                'role_name' => 'Admin',
            ],
            [
                'role_name' => 'User',
            ]
        ]);

        // $this->call("OthersTableSeeder");
    }
}
