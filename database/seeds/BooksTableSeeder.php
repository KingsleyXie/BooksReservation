<?php

use Illuminate\Database\Seeder;

class BooksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('books')->delete();
        
        \DB::table('books')->insert(array (
            0 => 
            array (
                'id' => 1001,
                'isbn' => '9787208061699',
                'title' => '江泽民和他的母校上海交通大学',
                'author' => '上海交通大学 编著',
                'publisher' => '上海人民出版社',
                'pubdate' => '2006-01-01',
                'cover' => 'https://img3.doubanio.com/lpic/s2927042.jpg',
                'quantity' => 1,
                'imported' => '2018-11-17 10:38:55',
                'updated' => '2018-11-17 10:38:55',
            ),
            1 => 
            array (
                'id' => 1002,
                'isbn' => '9780321714114',
                'title' => 'C++ Primer',
                'author' => 'Stanley B. Lippman 等',
                'publisher' => 'Addison-Wesley Professional',
                'pubdate' => '2012-8-16',
                'cover' => 'https://img1.doubanio.com/lpic/s29252317.jpg',
                'quantity' => 0,
                'imported' => '2018-11-17 10:38:55',
                'updated' => '2018-11-17 10:38:55',
            ),
            2 => 
            array (
                'id' => 1003,
                'isbn' => '9787111075660',
                'title' => 'TCP/IP详解 卷1：协议',
                'author' => 'W.Richard Stevens',
                'publisher' => '机械工业出版社',
                'pubdate' => '2000-4-1',
                'cover' => 'https://img3.doubanio.com/lpic/s1543906.jpg',
                'quantity' => 26,
                'imported' => '2018-11-17 10:38:55',
                'updated' => '2018-11-17 10:40:39',
            ),
            3 => 
            array (
                'id' => 1004,
                'isbn' => '9787532736546',
                'title' => '他改变了中国',
                'author' => '[美] 罗伯特·劳伦斯·库恩',
                'publisher' => '上海译文出版社',
                'pubdate' => '2005-01',
                'cover' => 'https://img1.doubanio.com/lpic/s1305338.jpg',
                'quantity' => 5,
                'imported' => '2018-11-17 10:38:55',
                'updated' => '2018-11-17 10:40:39',
            ),
            4 => 
            array (
                'id' => 1005,
                'isbn' => '9787208100039',
                'title' => '江泽民在上海',
                'author' => '明锐 等',
                'publisher' => '上海人民出版社',
                'pubdate' => '2011-7-28',
                'cover' => 'https://img1.doubanio.com/lpic/s26582097.jpg',
                'quantity' => 3,
                'imported' => '2018-11-17 10:38:55',
                'updated' => '2018-11-17 10:38:55',
            ),
            5 => 
            array (
                'id' => 1006,
                'isbn' => '9787111407010',
                'title' => '算法导论（原书第3版）',
                'author' => 'Thomas H.Cormen 等',
                'publisher' => '机械工业出版社',
                'pubdate' => '2012-12',
                'cover' => 'https://img3.doubanio.com/lpic/s25648004.jpg',
                'quantity' => 17,
                'imported' => '2018-11-17 10:38:55',
                'updated' => '2018-11-17 10:38:55',
            ),
        ));
        
        
    }
}