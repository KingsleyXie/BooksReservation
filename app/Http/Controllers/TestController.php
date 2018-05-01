<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function index()
    {
        $books = DB::table('book')->get();
        return $books;
    }
}
