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

Route::prefix('api/admin')->group(function () {
	/*
	==================================================================
		Admin Route 0. Session Control
	==================================================================
	*/
	Route::post('login', 'AdminSessionController@login');
	Route::get('logout', 'AdminSessionController@logout');

	Route::group(['middleware' => ['admin']], function () {
		Route::prefix('book')->group(function () {
			/*
			==================================================================
			Admin Route 1. Query Books
			==================================================================
			*/
			Route::get('all', 'AdminBookController@index');
			Route::get('id/{id}', 'AdminBookController@getById');
			Route::get('page/{page}/limit/{limit}', 'AdminBookController@getByPage');

			Route::get('init-index', 'AdminBookController@initElasticIndex');
			Route::get('reset-index', 'AdminBookController@resetElasticIndex');

			// This Interface Is Currently Still Reserved
			Route::get('isbn/{isbn}', 'AdminBookController@searchByISBN');

			/*
			==================================================================
				Admin Route 2. Manage Books
			==================================================================
			*/
			Route::post('add/raw', 'AdminBookController@addByRaw')->middleware('book');
			Route::post('add/isbn', 'AdminBookController@addByISBN');
			Route::post('update/{id}', 'AdminBookController@updateById')->middleware('book');
		});

		/*
		==================================================================
			Admin Route 3. Query Reservations
		==================================================================
		*/
		Route::get('reservation/all', 'ReservationController@index');
	});
});



Route::prefix('api/user')->group(function () {
	Route::prefix('book')->group(function () {
		/*
		==================================================================
			User Route 0. Query Books
		==================================================================
		*/
		Route::get('all', 'UserBookController@index');
		Route::get('all/count', 'UserBookController@countedIndex');
		Route::get('all/page/{page}/limit/{limit}', 'UserBookController@pagedIndex');

		Route::post('search', 'UserBookController@search');
		Route::post('search/count', 'UserBookController@countedSearch');
		Route::post('search/page/{page}/limit/{limit}', 'UserBookController@pagedSearch');
	});

	/*
	==================================================================
		User Route 1. Search Reservation
	==================================================================
	*/
	Route::get('reservation/stuno/{stuno}', 'ReservationController@searchByStuno');

	/*
	==================================================================
		User Route 2. Manage Reservation
	==================================================================
	*/
	Route::post('reserve/add', 'ReserveController@add')->middleware('add', 'list', 'collision');

	Route::post('reserve/modify', 'ReserveController@modify')->middleware('add', 'list', 'modify', 'list', 'collision');
});





/*
==================================================================
	Resources Route
==================================================================
*/
Route::get('/', function() {
	return \File::get(public_path() . '/html/index.html');
});

Route::get(config('app.adminpath'). '/books', function() {
	return \File::get(public_path() . '/html/admin/books.html');
});

Route::get(config('app.adminpath'). '/reservations', function() {
	return \File::get(public_path() . '/html/admin/reservations.html');
});
