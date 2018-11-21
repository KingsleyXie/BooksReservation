<?php

use Illuminate\Database\Seeder;

class ReservationBookTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('reservation_book')->delete();

        \DB::table('reservation_book')->insert(array (
            0 =>
            array (
                'id' => 1,
                'reservation_id' => 6001,
                'book_id' => 1004,
            ),
            1 =>
            array (
                'id' => 2,
                'reservation_id' => 6001,
                'book_id' => 1003,
            ),
        ));
    }
}
