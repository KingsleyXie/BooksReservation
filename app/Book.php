<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Elasticquent\ElasticquentTrait;

class Book extends Model
{
    use ElasticquentTrait;

    protected $table = 'book';

    protected $mappingProperties = [
        'title' => ['analyzer' => 'ik_max_word'],
        'author' => ['analyzer' => 'ik_max_word'],
        'publisher' => ['analyzer' => 'ik_max_word']
    ];

    function getIndexName()
    {
        return 'books_reservation_index';
    }

    function getIndexDocumentData()
    {
        return [
            'title'   => $this->title,
            'author'  => $this->author,
            'publisher'  => $this->publisher
        ];
    }
}
