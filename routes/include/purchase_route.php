<?php


//purchase Request
Route::resource('pr', 'Purchase\PurchaseRequestController');
Route::delete('prProd/destroyprprod/{id}', 'Purchase\PurchaseRequestController@destroyPrProduct')
        ->name("prProd.destroyprprod");

//Purchase Order
Route::resource('po', 'Purchase\PurchaseOrderController');