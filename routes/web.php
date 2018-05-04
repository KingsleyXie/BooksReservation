<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!

|
*/



/*
==================================================================
	Admin Route 0. Session Control
==================================================================
*/
Route::post(
	'/api/admin/login',
	'AdminSessionController@login'
);

Route::get(
	'/api/admin/logout',
	'AdminSessionController@logout'
)->middleware('admin');

/*
==================================================================
	Admin Route 1. Query Books
==================================================================
*/
Route::get(
	'/api/admin/book/all',
	'AdminBookController@index'
)->middleware('admin');

Route::get(
	'/api/admin/book/id/{id}',
	'AdminBookController@getById'
)->middleware('admin');

Route::get(
	'/api/admin/book/page/{page}/limit/{limit}',
	'AdminBookController@getByPage'
)->middleware('admin');

// This Interface Is Currently Still Reserved
Route::get(
	'/api/admin/book/isbn/{isbn}',
	'AdminBookController@searchByISBN'
)->middleware('admin');

/*
==================================================================
	Admin Route 2. Manage Books
==================================================================
*/
Route::post(
	'/api/admin/book/add/raw',
	'AdminBookController@addByRaw'
)->middleware('admin', 'book');

Route::post(
	'/api/admin/book/add/isbn',
	'AdminBookController@addByISBN'
)->middleware('admin', 'book');

Route::post(
	'/api/admin/book/update/{id}',
	'AdminBookController@updateById'
)->middleware('admin', 'book');

/*
==================================================================
	Admin Route 3. Query Reservations
==================================================================
*/
Route::get(
	'/api/admin/reservation/all',
	'ReservationController@index'
)->middleware('admin');



/*
==================================================================
	User Route 0. Query Books
==================================================================
*/
Route::get(
	'/api/user/book/all',
	'UserBookController@index'
);

Route::get(
	'/api/user/book/page/{page}/limit/{limit}',
	'UserBookController@getByPage'
);

Route::post(
	'/api/user/book/search',
	'UserBookController@search'
);

/*
==================================================================
	User Route 1. Search Reservation
==================================================================
*/
Route::get(
	'/api/user/reservation/stuno/{stuno}',
	'ReservationController@searchByStuno'
);

/*
==================================================================
	User Route 2. Manage Reservation
==================================================================
*/
Route::post(
	'/api/user/reserve/add',
	'ReserveController@add'
)->middleware(
	'add', 'list', 'collision'
);

Route::post(
	'/api/user/reserve/modify',
	'ReserveController@modify'
)->middleware(
	'add', 'list', 'modify', 'list', 'collision'
);
