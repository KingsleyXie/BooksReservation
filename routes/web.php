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

// Admin Part
Route::post(
	'/api/admin/login',
	'AdminSessionController@login'
);
Route::get(
	'/api/admin/logout',
	'AdminSessionController@logout'
);
Route::get(
	'/api/admin/status',
	'AdminSessionController@status'
);

Route::get(
	'/api/admin/book/all',
	'AdminBookController@index'
);
Route::get(
	'/api/admin/book/id/{id}',
	'AdminBookController@getById'
);
Route::get(
	'/api/admin/book/page/{page}/limit/{limit}',
	'AdminBookController@getByPage'
);

Route::post(
	'/api/admin/book/add/raw',
	'AdminBookController@addByRaw'
);
Route::post(
	'/api/admin/book/add/isbn',
	'AdminBookController@addByISBN'
);
Route::post(
	'/api/admin/book/update/{id}',
	'AdminBookController@updateById'
);

// This Interface Is Currently Still Reserved
Route::get(
	'/api/admin/book/isbn/{isbn}',
	'AdminBookController@searchByISBN'
);

Route::get(
	'/api/admin/reservation/all',
	'ReservationController@index'
);



// User Part
Route::get(
	'/api/user/reservation/stuno/{stuno}',
	'ReservationController@searchByStuNo'
);

Route::post(
	'/api/user/reserve/add',
	'ReserveController@add'
);
Route::post(
	'/api/user/reserve/modify/{id}',
	'ReserveController@modifyById'
);

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
