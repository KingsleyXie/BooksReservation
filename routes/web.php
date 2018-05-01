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
	'AdminController@login'
);
Route::get(
	'/api/admin/logout',
	'AdminController@logout'
);

Route::get(
	'/api/admin/book/all',
	'AdminController@getAllBooks'
);
Route::post(
	'/api/admin/book/add',
	'AdminController@addBook'
);
Route::post(
	'/api/admin/book/update/{id}',
	'AdminController@updateBookById'
);

Route::get(
	'/api/admin/reservation/all',
	'ReservationController@index'
);

Route::get(
	'/api/admin/douban/search/isbn/{isbn}',
	'DoubanController@search'
);

// User Part
Route::get(
	'/api/user/reservation/stuno/{stuno}',
	'ReservationController@searchByStuNo'
);

Route::post(
	'/api/user/reserve/add'
	'ReserveController@add'
);
Route::post(
	'/api/user/reserve/modify/{id}',
	'ReserveController@modifyReservationById'
);

Route::get(
	'/api/user/book/all',
	'BookController@index'
);
Route::get(
	'/api/user/book/search',
	'BookController@search'
);
