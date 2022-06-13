<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use Keygen;
use Auth;
use DNS1D;
use DB;

use App\Models\MasterProduct;
use App\Models\MasterProductSku;
use App\Models\MasterProductSupplier;

use App\DataTables\ProductDataTable;
use App\DataTables\ProductSkuDataTable;
use App\DataTables\ProductSupplierDataTable;

class ProductMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $selectionList = array(
        "brand_lists" =>array(),
        "category_lists" =>array(),
        "unit_lists" =>array(),
        "supplier_lists" =>array(),
    );

    public function productSelectionList()
    {
        $this->selectionList["brand_lists"] = app("App\Http\Controllers\BrandController")->selectList();
        $this->selectionList["category_lists"] = app("App\Http\Controllers\CategoryController")->selectList();
        $this->selectionList["unit_lists"] = app("App\Http\Controllers\UnitController")->selectList();
        $this->selectionList["supplier_lists"] = app("App\Http\Controllers\SupplierController")->selectList();
    }

    public function index(ProductDataTable $datatable)
    {

        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('products-index')){            
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if(empty($all_permission))
                $all_permission[] = 'dummy text';


            //return view('ProductMaster.index', compact('all_permission'));

            return $datatable->render("ProductMaster.index", compact('all_permission'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->productSelectionList();
        return view("ProductMaster.create")->with($this->selectionList);;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'code' => [
                'max:255',
                    Rule::unique('master_product')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'name' => [
                'max:255',
                    Rule::unique('master_product')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ]
        ]);

        //image

        $images = $request->image;
        $image_names = [];
        if($images) {            
            foreach ($images as $key => $image) {
                $imageName = $image->getClientOriginalName();
                $image->move('public/images/product', $imageName);
                $image_names[] = $imageName;
            }
            $data['image'] = implode(",", $image_names);
        }
        else {
            $data['image'] = 'zummXD2dvAtI.png';
        }

        //store

        $prod = new MasterProduct();

        $prod->product_code = $request->product_code;
        $prod->product_name = $request->product_name;
        $prod->product_brand = $request->brand_id;
        $prod->product_category = $request->category_id;
        $prod->product_unit = $request->unit_id ?? 0;
        $prod->product_sale_unit = $request->sale_unit_id ?? 0;
        $prod->product_purchase_unit = $request->purchase_unit_id ?? 0;
        $prod->product_selling_price = $request->price ?? 0;
        $prod->product_alert_qty = $request->alert_quantity ?? 0;
        $prod->product_featured = $request->product_featured ?? 0;
        $prod->product_image = $data['image'];
        $prod->product_detail = $request->product_details;
        $prod->is_sn = $request->is_sn ?? 0;
        $prod->created_by = Auth::user()->id;
        $prod->updated_by = Auth::user()->id;

        $prod->save();


        //store SKU and Supplier Moq

        if ($request->sku) {
            $sku = $this->storeSku($prod->product_id,$request);
        }
        if ($request->supplier) {
            $supplierMoq = $this->storeSupplierMoq($prod->product_id,$request);
        }
        

        \Session::flash('create_message', 'Product created successfully');        

    }

    //Store SKU
    public function storeSku($product_id, $request)
    {
        $productSkuData = array();
        $seq_num = 1;

        foreach ($request->sku as $key => $value) {

            $sku_desc = "";

            //get SKU Data

            try {


                if ($request->has("desc")) {
                    if (array_key_exists($key, $request->desc)) {
                        $sku_desc = (int) $request->desc[$key];
                    }
                }

                $productSkuData[] = array(
                    "product_id" =>  $product_id,
                    "sku_no" =>  $value,
                    "sku_desc" =>  $sku_desc,
                    "is_active" =>  1,
                    "seq_num" => $seq_num,
                    "created_by" =>  Auth::user()->id,
                    "updated_by" =>  Auth::user()->id,
                );

                $seq_num++;

            } catch (Exception $e) {

            }

            //endforarch
        }

        
        //store SKU

        if (count($productSkuData) > 0) {
            foreach ($productSkuData as $key => $value) {

                try {
                    $productSku = MasterProductSku::create($productSkuData[$key]);
                } catch (Exception $e) {

                }


            }
        }
    }

    //store Supplier
    public function storeSupplierMoq($product_id, $request)
    {

        $productSupplierData = array();
        $seq_num = 1;

        foreach ($request->supplier as $key => $value) {

            $moq = 1;
            $moqprice = 0;
            //get SKU Data

            try {


                if ($request->has("moq")) {
                    if (array_key_exists($key, $request->moq)) {
                        $moq = (int) $request->moq[$key];
                    }
                }

                if ($request->has("moqprice")) {
                    if (array_key_exists($key, $request->moqprice)) {
                        $moqprice = (int) $request->moqprice[$key];
                    }
                }


                $productSupplierData[] = array(
                    "product_id" =>  $product_id,
                    "supplier_id" =>  $value,
                    "supplier_moq" =>  $moq,
                    "supplier_price" =>  $moqprice,
                    "is_active" =>  1,
                    "seq_num" => $seq_num,
                    "created_by" =>  Auth::user()->id,
                    "updated_by" =>  Auth::user()->id,
                );

                $seq_num++;

            } catch (Exception $e) {

            }

            //endforarch
        }

        
        //store SKU

        if (count($productSupplierData) > 0) {
            foreach ($productSupplierData as $key => $value) {

                try {
                    $productSupplier = MasterProductSupplier::create($productSupplierData[$key]);
                } catch (Exception $e) {

                }


            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);
        if ($role->hasPermissionTo('products-edit')) {

            $product = MasterProduct::where("product_id", $id)->where("is_active", 1)->first();

            if(empty($product)){

                \Session::flash('not_permitted', 'The data not found.');
            }

            $this->productSelectionList();

            $this->selectionList["product"] = $product;
            $this->selectionList["id"] = $id;

            return view("ProductMaster.edit")->with($this->selectionList);;

        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProduct(Request $request)
    {

        $this->validate($request, [
            'code' => [
                'max:255',
                    Rule::unique('master_product')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
            'name' => [
                'max:255',
                    Rule::unique('master_product')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ]
        ]);

        $prod = MasterProduct::find($request->product_id);

        //image

        $previous_images = [];

        //previous images
        if($request->prev_img) {
            foreach ($request->prev_img as $key => $prev_img) {
                if(!in_array($prev_img, $previous_images))
                    $previous_images[] = $prev_img;
            }
            $prod->product_image = implode(",", $previous_images);
            $prod->save();
        }
        else {
            $prod->product_image = null;
            $prod->save();
        }

        //new images
        if($request->image) {
            $images = $request->image;
            $image_names = [];
            $length = count(explode(",", $prod->product_image));
            foreach ($images as $key => $image) {
                $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
                /*$image = Image::make($image)->resize(512, 512);*/
                $imageName = date("Ymdhis") . ($length + $key+1) . '.' . $ext;
                $image->move('public/images/product', $imageName);
                $image_names[] = $imageName;
            }
            if($prod->product_image)
                $data['image'] = $prod->product_image. ',' . implode(",", $image_names);
            else
                $data['image'] = implode(",", $image_names);
        }
        else{
            $data['image'] = $prod->product_image;
        }


        //update
        

        $prod->product_code = $request->product_code;
        $prod->product_name = $request->product_name;
        $prod->product_brand = $request->brand_id;
        $prod->product_category = $request->category_id;
        $prod->product_unit = $request->unit_id ?? 0;
        $prod->product_sale_unit = $request->sale_unit_id ?? 0;
        $prod->product_purchase_unit = $request->purchase_unit_id ?? 0;
        $prod->product_selling_price = $request->price ?? 0;
        $prod->product_alert_qty = $request->alert_quantity ?? 0;
        $prod->product_featured = $request->product_featured ?? 0;
        $prod->product_image = $data['image'];
        $prod->product_detail = $request->product_details;
        $prod->is_sn = $request->is_sn ?? 0;
        $prod->created_by = Auth::user()->id;
        $prod->updated_by = Auth::user()->id;

        $prod->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function destroySku($id)
    {
        return $id;
    }

    public function destroyProductSupplier($id)
    {
        return $id;
    }

    public function generateCode()
    {
        $id = Keygen::numeric(8)->generate();
        return $id;
    }

    //add sku
    public function addSku(Request $request)
    {

        $prodSku = MasterProductSku::where("product_id", $request->product_id)
                                    ->select("seq_num")
                                    ->orderBy("seq_num", "DESC")
                                    ->first();

        $seq_num = 1;

        if(!empty($prodSku)){
            $seq_num = $prodSku->seq_num + 1;
        }

        $skuDb = new MasterProductSku(); 

        $skuDb->sku_no      = $request->sku;
        $skuDb->product_id  = $request->product_id;
        $skuDb->sku_desc    = $request->desc;
        $skuDb->seq_num     = $seq_num;

        $skuDb->created_by = Auth::user()->id;
        $skuDb->updated_by = Auth::user()->id;

        $skuDb->save();

        return "Added.";
    }

    //find sku
    public function getSku(Request $request, $id)
    {

        $res = array();

        if(!$request->has("sku_id")){
            return response()->json($res, 400);
        }

        $prodSku = MasterProductSku::where("sku_id", $id)->first();

        if(!empty($prodSku)){
            $res = $prodSku->toArray();
        }

        return $prodSku;
    }

    //update SKU

    public function updateSkuAjax(Request $request, $sku_id=null)
    {
        $skuDb = MasterProductSku::where("sku_id", $sku_id)->first();

        if(empty($skuDb)){
            return response()->json("The data is empty.", 201);
        }

        $skuDb->sku_no      = $request->edt_sku;
        $skuDb->sku_desc    = $request->edt_desc;

        $skuDb->updated_by = Auth::user()->id;

        $skuDb->save();

        return response()->json("Record updated successfully.", 200);
    }

    //find supplier
    public function getProductSupplier(Request $request, $id)
    {

        $res = array();

        if(!$request->has("product_supplier_id")){
            return response()->json($res, 400);
        }

        $prodSupp = MasterProductSupplier::where("product_supplier_id", $id)
                                    ->first();

        if(!empty($prodSupp)){
            $res = $prodSupp->toArray();
        }

        return $prodSupp;
    }

    //update supplier

    public function updateProductSupplierAjax(Request $request, $id=null)
    {
        $prodSupp = MasterProductSupplier::where("product_supplier_id", $id)
                                    ->first();

        if(empty($prodSupp)){
            return response()->json("The data is empty.", 201);
        }

        $prodSupp->supplier_moq      = $request->moq;
        $prodSupp->supplier_id    = $request->supplier;
        $prodSupp->supplier_price    = $request->moqprice;

        $prodSupp->updated_by = Auth::user()->id;

        $prodSupp->save();

        return response()->json("Record updated successfully.", 200);
    }

    //add Product Supplier
    public function addProductSupplier(Request $request)
    {


        $prodSupp = MasterProductSupplier::where("product_id", $request->product_id)
                                    ->select("seq_num")
                                    ->orderBy("seq_num", "DESC")
                                    ->first();

        $seq_num = 1;

        if(!empty($prodSupp)){
            $seq_num = $prodSupp->seq_num + 1;
        }

        $suppDb = new MasterProductSupplier(); 

        $suppDb->supplier_moq      = $request->moq;
        $suppDb->product_id  = $request->product_id;
        $suppDb->supplier_id    = $request->supplier;
        $suppDb->supplier_price    = $request->moqprice;
        $suppDb->seq_num     = $seq_num;

        $suppDb->created_by = Auth::user()->id;
        $suppDb->updated_by = Auth::user()->id;

        $suppDb->save();

        return "Added.";
    }

    //get SKU Table

    public function getSkuTable(Request $request, ProductSkuDataTable $dataTable)
    {
        return $dataTable
            ->with("id", $request->product_id)
            ->render('mProduct.edit');
    }

    //get SKU Suuplier

    public function getProductSupplierTable(Request $request, ProductSupplierDataTable $dataTable)
    {
        return $dataTable
            ->with("id", $request->product_id)
            ->render('mProduct.edit');
    }

    public function saleUnit($id)
    {
        return app("App\Http\Controllers\UnitController")->saleUnit($id);
    }


}
