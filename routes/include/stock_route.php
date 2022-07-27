<?php


//Stock
Route::resource('stock', 'Stock\StockController');

Route::GET('stock/index/serial_number'
	,'Stock\StockController@indexSerialNumber')->name('stock.index.serial_number');

Route::post('stock/service/import-sn-product', 'Stock\StockController@importSerialNumber')
        ->name('stock.service.import_sn_product');

Route::get('importsn-formatexcel', 'Stock\StockController@downloadImporSnFormatCsv')
	->name('importsn.formatexcel');