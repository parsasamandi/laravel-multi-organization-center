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
    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

    // Center
    Route::get('/','CenterController@center');
    Route::group(['prefix' => 'center','as' => 'center.'], function() {
        Route::get('list', 'CenterController@list');
        Route::get('table/list', 'CenterController@centerTable')->name('list.table');
        Route::post('store', 'CenterController@store');
        Route::get('edit', 'CenterController@edit');
        Route::get('delete','CenterController@delete');
    });

    // Center Status Report
    Route::group(['prefix' => 'centerStatusReport','as' => 'centerStatusReport.'], function() {
        Route::get('list', 'CenterStatusReportController@list');
        Route::get('table/list', 'CenterStatusReportController@centerStatusReportTable')->name('list.table');
        Route::post('store', 'CenterStatusReportController@store');
        Route::get('edit', 'CenterStatusReportController@edit');
        Route::get('delete','CenterStatusReportController@delete');
    });

    // Golestan Team
    Route::group(['prefix' => 'golestanTeam','as' => 'golestanTeam.'], function() {
        Route::get('list', 'GolestanTeamController@list');
        Route::get('table/list', 'GolestanTeamController@golestanTeamTable')->name('list.table');
        Route::post('store', 'GolestanTeamController@store');
        Route::get('edit', 'GolestanTeamController@edit');
        Route::get('delete','GolestanTeamController@delete');
    });

    // General Info
    Route::group(['prefix' => 'generalInfo','as' => 'generalInfo.'], function() {
        Route::get('list', 'GeneralInfoController@list');
        Route::get('/table/list', 'GeneralInfoController@generalInfoTable')->name('list.table');
        Route::post('store', 'GeneralInfoController@store');
        Route::get('/edit', 'GeneralInfoController@edit');
        Route::get('/details', 'GeneralInfoController@details');
        Route::post('/update', 'GeneralInfoController@update');
        Route::post('/confirmStatus', 'GeneralInfoController@confirmStatus');
        Route::get('delete','GeneralInfoController@delete');
    });

    // Report
    Route::group(['prefix' => 'report','as' => 'report.'], function() {
        Route::get('list', 'ReportController@list');
        Route::get('/table/list', 'ReportController@reportTable')->name('list.table');
        Route::post('store', 'ReportController@store');
        Route::get('/edit', 'ReportController@edit');
        Route::post('/update', 'ReportController@update');
        Route::post('/confirmStatus', 'ReportController@confirmStatus');
        Route::get('/details', 'ReportController@details');
        Route::get('delete','ReportController@delete');
    });
    // Transfer Report
    Route::group(['prefix' => 'paymentTransfer', 'as' => 'paymentTransfer.'], function() {
        Route::get('list', 'PaymentTransferController@list')->name('list');
        Route::get('/table/list', 'PaymentTransferController@paymentTransferTable')->name('list.table');
        Route::post('store', 'PaymentTransferController@store')->name('store');
        Route::get('/edit', 'PaymentTransferController@edit')->name('edit');
        Route::get('/details', 'PaymentTransferController@details')->name('details');
        Route::post('/update', 'PaymentTransferController@update')->name('update');
        Route::get('delete', 'PaymentTransferController@delete')->name('delete');
    });
});

// Login page
Route::get('login','Auth\LoginController@index')->name('login');
Route::post('login', 'Auth\LoginController@store');
