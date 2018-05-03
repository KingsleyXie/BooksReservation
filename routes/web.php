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
)->middleware('admin.auth');

/*
==================================================================
	Admin Route 1. Query Books
==================================================================
*/
Route::get(
	'/api/admin/book/all',
	'AdminBookController@index'
)->middleware('admin.auth');

Route::get(
	'/api/admin/book/id/{id}',
	'AdminBookController@getById'
)->middleware('admin.auth');

Route::get(
	'/api/admin/book/page/{page}/limit/{limit}',
	'AdminBookController@getByPage'
)->middleware('admin.auth');

// This Interface Is Currently Still Reserved
Route::get(
	'/api/admin/book/isbn/{isbn}',
	'AdminBookController@searchByISBN'
)->middleware('admin.auth');

/*
==================================================================
	Admin Route 2. Manage Books
==================================================================
*/
Route::post(
	'/api/admin/book/add/raw',
	'AdminBookController@addByRaw'
)->middleware('admin.auth');

Route::post(
	'/api/admin/book/add/isbn',
	'AdminBookController@addByISBN'
)->middleware('admin.auth');

Route::post(
	'/api/admin/book/update/{id}',
	'AdminBookController@updateById'
)->middleware('admin.auth');

/*
==================================================================
	Admin Route 3. Query Reservations
==================================================================
*/
Route::get(
	'/api/admin/reservation/all',
	'ReservationController@index'
)->middleware('admin.auth');



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
	'ReservationController@searchByStuNo'
);

/*
==================================================================
	User Route 2. Manage Reservation
==================================================================
*/
Route::post(
	'/api/user/reserve/add',
	'ReserveController@add'
)->middleware('reserve.add');

Route::post(
	'/api/user/reserve/modify/{id}',
	'ReserveController@modifyById'
)->middleware('reserve.add', 'reserve.modify');
