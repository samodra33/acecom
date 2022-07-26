<?php


//Stock
Route::resource('stock', 'Stock\StockController');

Route::GET('stock/index/serial_number'
	,'Stock\StockController@indexSerialNumber')->name('stock.index.serial_number');