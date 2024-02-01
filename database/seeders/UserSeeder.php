<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          //get the admin role from the role table. Later(line 31) attach this role to a user
          $role_admin = Role::where('name', 'admin')->first();

          //get the user role from the role table. Later(line 42) attach this role to a user
          $role_customer = Role::where('name', 'customer')->first();

          $admin = new User();
          $admin->username = 'kapper';
          $admin->email = 'kacper.agatowski75@gmail.com';
          $admin->password = Hash::make('password');
          $admin->save();

          // attach the admin role to the user that was created above.
          $admin->roles()->attach($role_admin);


          $customer = new User();
          $customer->username = 'jpd';
          $customer->email = 'n00212272@iadt.ie';
          $customer->password = Hash::make('password');
          $customer->save();
          //attach the customer role to this user.
          $customer->roles()->attach($role_customer);
    }
}