<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(PermissionsSeeder::class);

        $this->call(BooksTableSeeder::class);
        $this->call(ReservationsTableSeeder::class);
        $this->call(ReservationBookTableSeeder::class);
    }
}
