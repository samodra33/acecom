<?php

// Users
Route::get('user/profile/{id}', 'UserController@profile')->name('user.profile');
Route::put('user/update_profile/{id}', 'UserController@profileUpdate')->name('user.profileUpdate');
Route::put('user/changepass/{id}', 'UserController@changePassword')->name('user.password');
Route::get('user/genpass', 'UserController@generatePassword');
Route::post('user/deletebyselection', 'UserController@deleteBySelection');
Route::post('user/{id}/2fa-reset', 'UserController@twoFAReset')->name('user.2fa.reset');
Route::resource('user', 'UserController');
