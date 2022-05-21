<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


define('PAGINATION_COUNT',10);
Route::group(['prefix' => 'admin' , 'middleware' => 'auth:admin'], function () {
    Route::get('/', 'Admin\DashboardController@index')->name('admin.dashboard');
    // Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    ################################### Start Route Languages ################################################
    Route::group(['prefix' => 'languages', 'namespace' => 'Admin'], function () {
        Route::get('/', 'LanguagesController@index')->name('admin.languages');
        Route::get('/create', 'LanguagesController@create')->name('admin.languages,create');
        Route::post('/store', 'LanguagesController@store')->name('admin.languages,store');

        Route::get('edit/{id}', 'LanguagesController@edit')->name('admin.languages.edit');
        Route::post('update/{id}','LanguagesController@update') ->name('admin.languages.update');

        Route::get('delete/{id}','LanguagesController@delete') ->name('admin.languages.delete');

    });
    ################################### End Route Languages   ################################################

    ################################### Start Categries Route ################################################
    Route::group(['prefix' => 'main_categries', 'namespace' => 'Admin'], function () {
        Route::get('/', 'MainCategriesController@index')->name('admin.categories');
        Route::get('/create', 'MainCategriesController@create')->name('admin.categories.create');
        Route::post('/store', 'MainCategriesController@store')->name('admin.categories.store');

        Route::get('edit/{mainCat_id}', 'MainCategriesController@edit')->name('admin.categories.edit');
        Route::post('update/{mainCat_id}','MainCategriesController@update') ->name('admin.categories.update');

        Route::get('delete/{mainCat_id}','MainCategriesController@delete') ->name('admin.categories.delete');
        Route::get('changeStatus/{id}','MainCategriesController@changeStatus') -> name('admin.categories.status');


    });
    ################################### End  Categries Route   ################################################

    ################################### Start Vendors Route ################################################
    Route::group(['prefix' => 'vendors', 'namespace' => 'Admin'], function () {
        Route::get('/', 'VendorsController@index')->name('admin.vendors');
        Route::get('/create', 'VendorsController@create')->name('admin.vendors.create');
        Route::post('/store', 'VendorsController@store')->name('admin.vendors.store');

        Route::get('edit/{id}', 'VendorsController@edit')->name('admin.vendors.edit');
        Route::post('update/{id}','VendorsController@update') ->name('admin.vendors.update');

        Route::get('delete/{id}','VendorsController@delete') ->name('admin.vendors.delete');

    });
    ################################### End  Vendors Route   ################################################

});


Route::get('test' , function(){
  return show_name();
});


Route::group(['middleware' => 'guest:admin'] , function(){
  Route::get('login' , [App\Http\Controllers\Admin\LoginController::class, 'getLogin'])->name('get.admin.login');
  Route::post('login' , [App\Http\Controllers\Admin\LoginController::class, 'login'])->name('admin.login');

});



// Route::group(['namespace' => 'Admin', 'middleware' => 'guest:admin'], function () {
//     Route::get('login', 'LoginController@getLogin')->name('get.admin.login');
//     Route::post('login', 'LoginController@login')->name('admin.login');
// });
