<?php
use App\Http\Controllers\CategoryController;

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


Route::group(['middleware' => 'auth'], function () {
    // Logout
    Route::post('/logout', 'Auth\LoginController@logout');
    // Admin
    Route::get('/','AdminController@admin');
    Route::group(['prefix' => 'admin','as' => 'admin.'], function() {
        Route::get('list', 'AdminController@list');
        Route::get('table/list', 'AdminController@adminTable')->name('list.table');
        Route::post('store', 'AdminController@store');
        Route::get('edit', 'AdminController@edit');
        Route::get('delete/{id}','AdminController@delete');
    });
});

// Login page
Route::get('login','Auth\loginController@index')->name('login');
Route::post('login', 'Auth\LoginController@store');
// Home
// Each product description
Route::get('/product/details/{id}', 'ProductController@details');
// Products with categories
Route::get('/products', 'ProductController@show')->name('products');
