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
	Resources Route
==================================================================
*/
Route::get('/', function() {
	return \File::get(public_path() . '/html/index.html');
});
Route::get('/' . config('app.adminpath'). '/books', function() {
	return \File::get(public_path() . '/html/admin/books.html');
});
Route::get('/' . config('app.adminpath'). '/reservations', function() {
	return \File::get(public_path() . '/html/admin/reservations.html');
});



/*
==================================================================
	Admin Route 0. Session Control
==================================================================
*/
Route::post(
	'/api/' . config('app.adminpath'). '/login',
	'AdminSessionController@login'
);

Route::get(
	'/api/' . config('app.adminpath'). '/logout',
	'AdminSessionController@logout'
)->middleware('admin');

/*
==================================================================
	Admin Route 1. Query Books
==================================================================
*/
Route::get(
	'/api/' . config('app.adminpath'). '/book/all',
	'AdminBookController@index'
)->middleware('admin');

Route::get(
	'/api/' . config('app.adminpath'). '/book/id/{id}',
	'AdminBookController@getById'
)->middleware('admin');

Route::get(
	'/api/' . config('app.adminpath'). '/book/page/{page}/limit/{limit}',
	'AdminBookController@getByPage'
)->middleware('admin');

// This Interface Is Currently Still Reserved
Route::get(
	'/api/' . config('app.adminpath'). '/book/isbn/{isbn}',
	'AdminBookController@searchByISBN'
)->middleware('admin');

/*
==================================================================
	Admin Route 2. Manage Books
==================================================================
*/
Route::post(
	'/api/' . config('app.adminpath'). '/book/add/raw',
	'AdminBookController@addByRaw'
)->middleware('admin', 'book');

Route::post(
	'/api/' . config('app.adminpath'). '/book/add/isbn',
	'AdminBookController@addByISBN'
)->middleware('admin');

Route::post(
	'/api/' . config('app.adminpath'). '/book/update/{id}',
	'AdminBookController@updateById'
)->middleware('admin', 'book');

/*
==================================================================
	Admin Route 3. Query Reservations
==================================================================
*/
Route::get(
	'/api/' . config('app.adminpath'). '/reservation/all',
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
	'/api/user/book/all/count',
	'UserBookController@countedIndex'
);
Route::get(
	'/api/user/book/all/page/{page}/limit/{limit}',
	'UserBookController@pagedIndex'
);

Route::post(
	'/api/user/book/search',
	'UserBookController@search'
);
Route::post(
	'/api/user/book/search/count',
	'UserBookController@countedSearch'
);
Route::post(
	'/api/user/book/search/page/{page}/limit/{limit}',
	'UserBookController@pagedSearch'
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
