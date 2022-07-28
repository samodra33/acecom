<?php


//grn
Route::resource('grn', 'Grn\GrnController');

//grn Product
Route::GET('grn-prod/service/grn-prod-from-po'
	,'Grn\GrnController@getProductfromPo')->name('grn_prod.service.prod_from_po');

Route::delete('grnProd/destroyprprod/{id}', 'Grn\GrnController@destroyGrnProduct')
        ->name("grnProd.destroyprprod");

Route::GET('grnProd/service/find-product-id/{id}', 'Grn\GrnController@getProductbyId')
        ->name('grnProd.service.find_product_id');

Route::post('grnProd/service/update-grn-product', 'Grn\GrnController@updateProduct')
        ->name('grnProd.service/update_grn_product');

Route::post('grnProd/service/store-grn-product', 'Grn\GrnController@storeProduct')
        ->name('grnProd.service/store_grn_product');