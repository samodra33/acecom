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

Auth::routes();

Route::post('/2fa', function () {
    return redirect('/home');
})->name('2fa')->middleware('2fa');

Route::get('/2fa', 'Auth\TwoFAController@show')->name('auth.2fa.show');
Route::put('/2fa-complete', 'Auth\TwoFAController@complete')->name('auth.2fa.complete');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', 'HomeController@dashboard');
});

Route::group(['middleware' => ['auth', 'active', 'auth.timeout', '2fa']], function () {

    //////////////////////////////////////////////////////////////
    // SalesPro Module :

    require ('salespro/salespro_route.php');

    ////////////////////////////////////////////////////////////

    Route::get('/', 'HomeController@index');

});
