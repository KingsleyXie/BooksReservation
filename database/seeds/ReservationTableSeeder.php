<?php

use Illuminate\Database\Seeder;

class ReservationTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('reservation')->delete();
        
        \DB::table('reservation')->insert(array (
            0 => 
            array (
                'id' => 6001,
                'stuname' => '章保滑',
                'stuno' => '201623336666',
                'dorm' => 'C10',
                'contact' => '13755555555',
                'takeday' => '5月13日',
                'taketime' => '16:30 - 18:00',
                'book0' => 1004,
                'book1' => 1003,
                'book2' => NULL,
                'submited' => '2018-11-17 10:40:39',
                'updated' => '2018-11-17 10:40:39',
            ),
        ));
        
        
    }
}