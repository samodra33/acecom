<?php


//purchase Request
Route::resource('pr', 'Purchase\PurchaseRequestController');

//PR Product
Route::delete('prProd/destroyprprod/{id}', 'Purchase\PurchaseRequestController@destroyPrProduct')
        ->name("prProd.destroyprprod");
Route::post('prProd/service/store-pr-product', 'Purchase\PurchaseRequestController@storeProduct')
        ->name('prProd.service/store_pr_product');
Route::GET('prProd/service/find-product-id/{id}', 'Purchase\PurchaseRequestController@getProductbyId')
        ->name('prProd.service.find_product_id');

//Purchase Order
Route::resource('po', 'Purchase\PurchaseOrderController');

//PO Product
Route::delete('poProd/destroyprprod/{id}', 'Purchase\PurchaseOrderController@destroyPoProduct')
        ->name("poProd.destroyprprod");

Route::GET('poProd/service/find-product-id/{id}', 'Purchase\PurchaseOrderController@getProductbyId')
        ->name('poProd.service.find_product_id');