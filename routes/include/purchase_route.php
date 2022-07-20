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

Route::GET('po/service/find-po-by-warehouse/{id}', 'Purchase\PurchaseOrderController@getPobyWarehouse')
        ->name('po.service.find_po_by_warehouse');

//PO Product
Route::delete('poProd/destroyprprod/{id}', 'Purchase\PurchaseOrderController@destroyPoProduct')
        ->name("poProd.destroyprprod");

Route::GET('poProd/service/find-product-id/{id}', 'Purchase\PurchaseOrderController@getProductbyId')
        ->name('poProd.service.find_product_id');
Route::GET('poProd/service/productwarehouse-table', 'Purchase\PurchaseOrderController@getProductWarehouseTable')
        ->name('poProd.service.productwarehouse_table');
Route::PATCH('poProd/service/storeproductwarehouse', 'Purchase\PurchaseOrderController@storeproductwarehouse')
        ->name('poProd.service.storeproductwarehouse');
Route::PATCH('poProd/service/updateproductwarehouse', 'Purchase\PurchaseOrderController@updateproductwarehouse')
        ->name('poProd.service.updateproductwarehouse');
Route::delete('poProd/destroypowarehouse/{id}', 'Purchase\PurchaseOrderController@destroyPoWarehouse')
        ->name("poProd.destroypowarehouse");
Route::GET('poProd/service/add-poProd-create-ajax', 'Purchase\PurchaseOrderController@getAddPoProduct')
        ->name('poProd.service.add_poProd_create_ajax');