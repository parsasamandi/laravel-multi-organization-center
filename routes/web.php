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


Route::group(['middleware' => 'auth'], function () {
    // Logout
    Route::post('/logout', 'Auth\LoginController@logout');
    // Center
    Route::get('/','CenterController@center');
    Route::group(['prefix' => 'center','as' => 'center.'], function() {
        Route::get('list', 'CenterController@list');
        Route::get('table/list', 'CenterController@centerTable')->name('list.table');
        Route::post('store', 'CenterController@store');
        Route::get('edit', 'CenterController@edit');
        Route::get('delete/{id}','CenterController@delete');
    });

    // General Info
    Route::group(['prefix' => 'generalInfoReport','as' => 'generalInfoReport.'], function() {
        Route::get('list', 'GeneralInfoReportController@list');
        Route::get('/table/list', 'GeneralInfoController@generalInfoTable')->name('generalInfo.table');
        Route::get('/table/list', 'GeneralInfoController@reportTable')->name('report.table');
        Route::post('store', 'GeneralInfoController@store');
        Route::get('edit', 'GeneralInfoController@edit');
        Route::get('delete/{id}','GeneralInfoController@delete');
    });

    // Report
    Route::group(['prefix' => 'report','as' => 'report.'], function() {
        Route::get('list', 'ReportController@list');
        Route::get('/table/list', 'Report@generalInfoTable')->name('generalInfo.table');
        Route::get('/table/list', 'ReportController@reportTable')->name('report.table');
        Route::post('store', 'ReportController@store');
        Route::get('edit', 'ReportController@edit');
        Route::get('delete/{id}','ReportController@delete');
    });


});

// Login page
Route::get('login','Auth\loginController@index')->name('login');
Route::post('login', 'Auth\LoginController@store');
// Home
// Each product description
Route::get('/product/details/{id}', 'ProductController@details');
