<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'username' => 'superadmin',
            'password' => bcrypt('superadmin'),
        ]);

        User::create([
            'username' => 'booksadmin',
            'password' => bcrypt('booksadmin'),
        ]);
    }
}
