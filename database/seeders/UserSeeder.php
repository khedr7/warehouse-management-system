<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'fname'    => 'super',
            'lname'    => 'admin',
            'email'    => 'super@gmail.com',
            'password' => '$2y$10$UYDQgc6DYMdXo19G0se1meMCW0Hs7YB2QXiUNPd5SrytR394XPLGi',
            'phone'    => '0999999999',
        ]);

        $user->assignRole('superAdmin');
    }
}
