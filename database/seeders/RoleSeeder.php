<?php

namespace Database\Seeders;


use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_customer = new Role();
        $role_customer->name = 'customer';
        $role_customer->description = 'An ordinary user';
        $role_customer->save();

        $role_admin = new Role();
        $role_admin->name = 'admin';
        $role_admin->description = 'An admin to maintain each upload';
        $role_admin->save();
    }
}