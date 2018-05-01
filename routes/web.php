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
Route::get(
	'/api/admin/login',
	'SessionController@login'
);
Route::get(
	'/api/admin/logout',
	'SessionController@logout'
);

Route::get(
	'/api/admin/book/all',
	'AdminBookController@index'
);
Route::post(
	'/api/admin/book/add',
	'AdminBookController@add'
);
Route::post(
	'/api/admin/book/update/{id}',
	'AdminBookController@updateById'
);

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
	'/api/user/book/search',
	'UserBookController@search'
);
