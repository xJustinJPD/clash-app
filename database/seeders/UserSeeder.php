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
          $admin->image = 'images/no_image_available.jpg';
          $admin->password = Hash::make('password');
          $admin->save();

          $additionalAdmin = new User();
          $additionalAdmin->username = 'admin_user';
          $additionalAdmin->email = 'kacper@gmail.com';
          $additionalAdmin->image = 'images/no_image_available.jpg';
          $additionalAdmin->password = Hash::make('password');
          $additionalAdmin->save();
          // attach the admin role to the users
          $admin->roles()->attach($role_admin);
          $additionalAdmin->roles()->attach($role_admin);

          $customer = new User();
          $customer->username = 'jpd';
          $customer->email = 'justinperrydoyle@gmail.com';
          $customer->image = 'images/no_image_available.jpg';
          $customer->password = Hash::make('password');
          $customer->save();

          $additionalCustomer = new User();
          $additionalCustomer->username = 'customer_user';
          $additionalCustomer->email = 'kadex@gmail.com';
          $additionalCustomer->image = 'images/no_image_available.jpg';
          $additionalCustomer->password = Hash::make('password');
          $additionalCustomer->save();
          //attach the customer role to users.
          $customer->roles()->attach($role_customer);
          $additionalCustomer->roles()->attach($role_customer);
    }
}