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

// Route::post('/2fa', function () {
//     return redirect('/home');
// })->name('2fa')->middleware('2fa');

// Route::get('/2fa', 'Auth\TwoFAController@show')->name('auth.2fa.show');
// Route::put('/2fa-complete', 'Auth\TwoFAController@complete')->name('auth.2fa.complete');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', 'HomeController@dashboard');
});

Route::group(['middleware' => ['auth', 'active', 'auth.timeout']], function () {

    //////////////////////////////////////////////////////////////
    // SalesPro Module :
    require ('salespro/salespro_route.php');
    //purchase Module
    require ('include/purchase_route.php');
    //grn Module
    require ('include/grn_route.php');

    // for dev purpose only
    require ('salespro/pajar_route.php');

    ////////////////////////////////////////////////////////////

    Route::get('/', 'HomeController@index');

    //product

    Route::resource('mProduct', 'Product\ProductMasterController');
    Route::get("mProduct/service/gencode", 'Product\ProductMasterController@generateCode');
    Route::get('mProduct/saleunit/{id}', 'Product\ProductMasterController@saleUnit');
    Route::post('mProduct/updateProduct', 'Product\ProductMasterController@updateProduct')->name('mProduct.updateProduct');
    Route::GET('mProduct/service/find-product/{id}', 'Product\ProductMasterController@getProductDetail')
        ->name('mProduct.service.find_product');

    //product SKU
    Route::GET('mProduct/service/sku-table', 'Product\ProductMasterController@getSkuTable')
        ->name('mProduct.service.sku_table');
    Route::post('mProduct/service/add-sku', 'Product\ProductMasterController@addSku')
        ->name('mProduct.service/add_sku');
    Route::delete('mProduct/destroysku/{id}', 'Product\ProductMasterController@destroySku')
        ->name("mProduct.destroySku");
    Route::GET('mProduct/service/find-sku/{id}', 'Product\ProductMasterController@getSku')
        ->name('mProduct.service.find_sku');

    Route::PATCH('mProduct/service/{sku_id}/edit-sku', 'Product\ProductMasterController@updateSkuAjax')
        ->name('mProduct.service.edit_sku');

    //product Supplier
    Route::GET('mProduct/service/supplierProd-table', 'Product\ProductMasterController@getProductSupplierTable')->name('mProduct.service.supplierProd_table');
    Route::post('mProduct/service/add-supplier', 'Product\ProductMasterController@addProductSupplier')
        ->name('mProduct.service/add_supplier');
    Route::delete('mProduct/destroyprodsupp/{id}', 'Product\ProductMasterController@destroyProductSupplier')
        ->name("mProduct.destroyprodsupp");
    Route::GET('mProduct/service/find-supplier/{id}', 'Product\ProductMasterController@getProductSupplier')
        ->name('mProduct.service.find_supplier');

    Route::GET('mProduct/service/find-supplier-by-product/{id}', 'Product\ProductMasterController@getListSupplierbyProductJson')
        ->name('mProduct.service.find_supplier_by_product');

    Route::PATCH('mProduct/service/{id}/edit-prod-supplier', 'Product\ProductMasterController@updateProductSupplierAjax')
        ->name('mProduct.service.edit_prod_supplier');

    //supplier

    Route::get('supplier/getDetail/{id}', 'SupplierController@getSupplier')
        ->name('supplier.getDetail');

});
