<?php

namespace App\Http\Controllers\Grn;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use Keygen;
use Auth;
use DNS1D;
use DB;

use App\Models\Grn;
use App\Models\GrnProduct;
use App\Models\MasterProduct;
use App\Models\MasterProductSupplier;
use App\Models\PurchaseOrderProductWarehouse;
use App\User;
use App\PosSetting;
use App\Warehouse;
use App\Account;
use App\Supplier;
use App\Brand;
use App\Category;
use App\Unit;

use App\DataTables\GrnDataTable;
use App\DataTables\AddProductbyPoDataTable;
use App\DataTables\GrnProductDataTable;

class GrnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $documentType = "good_receive_note";

    private $selectionList = array(
        "permissions_lists" =>array(),
        "warehouse_lists" => array(),
    );

    public function grnSelectionList()
    {
        $role = Role::find(Auth::user()->role_id);
        $permissions = Role::findByName($role->name)->permissions;
        foreach ($permissions as $permission)
            $all_permission[] = $permission->name;
        if(empty($all_permission))
            $all_permission[] = 'dummy text';


        $this->selectionList["permissions_lists"] = $all_permission;
        $this->selectionList["warehouse_lists"] = app("App\Http\Controllers\WarehouseController")->selectList();
    }

    public function getProductfromPo(Request $request, AddProductbyPoDataTable $dataTable)
    {
        return $dataTable->render('grn.modals.add_po_product');
    }


    public function index(GrnDataTable $datatable)
    {

        $role = Role::find(Auth::user()->role_id);

        if($role->hasPermissionTo('grn-index')) {

            $this->grnSelectionList();
            return $datatable->render("grn.index",  $this->selectionList);
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
        \View::share("name_form", "Create");

        $this->grnSelectionList();
        return view("grn.create")->with($this->selectionList);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //get GRN no

        $getDocumentNumber = app("App\Http\Controllers\DocumentNumber\DocumentNumberController")->generate($this->documentType);

        if (empty($getDocumentNumber)) {

            \Session::flash('not_permitted', 'Something wrong. Please try again a moment.');  
            return redirect()->back();
        }
        $grn_no = $getDocumentNumber["document_no"];
        $sequenceID = $getDocumentNumber["sequence_id"];
        $nextSequence = $getDocumentNumber["next_sequence"];

        //store GRN

        $grn = new Grn();

        $grn->grn_no                  = $grn_no;
        $grn->supplier_do_no                = $request->supplier_do_no;
        $grn->grn_date                = $request->grn_date;
        $grn->grn_remark             = $request->grn_remark;
        $grn->created_by             = Auth::user()->id;
        $grn->updated_by             = Auth::user()->id;

        $grn->save();

        if (!isset($grn->grn_id)) {

            \Session::flash('not_permitted', 'Failed !');  
            return redirect()->back();

        }

        $getUpdateSequence = app("App\Http\Controllers\DocumentNumber\DocumentNumberController")
                                ->updateSequence($sequenceID, $nextSequence);

        if (empty($getUpdateSequence)) {

            \Session::flash('not_permitted', 'Error while updating PR number!');  
            return redirect()->back();
        }

        if ($request->po_warehouse_id) {
            $grnProduct = $this->storeGrnProduct($grn->grn_id,$request);
        }

        \Session::flash('message', 'Product created successfully');  

        //return back();
        return redirect(route('grn.edit', $grn->grn_id));
    }

    //store product create Page
    public function storeGrnProduct($grn_id, $request)
    {

        $productData = array();
        $seq_num = 1;

        foreach ($request->po_warehouse_id as $key => $value) {

            //get Product Data

            try {

                if ($request->has("po_warehouse_id")) {
                    if (array_key_exists($key, $request->po_warehouse_id)) {
                        $po_warehouse_id = (int) $request->po_warehouse_id[$key];
                    }
                }

                if ($request->has("warehouse_id")) {
                    if (array_key_exists($key, $request->warehouse_id)) {
                        $warehouse_id = (int) $request->warehouse_id[$key];
                    }
                }

                if ($request->has("po_product_id")) {
                    if (array_key_exists($key, $request->po_product_id)) {
                        $po_product_id = (int) $request->po_product_id[$key];
                    }
                }

                if ($request->has("product_id")) {
                    if (array_key_exists($key, $request->product_id)) {
                        $product_id = (int) $request->product_id[$key];
                    }
                }

                if ($request->has("product_qty")) {
                    if (array_key_exists($key, $request->product_qty)) {
                        $product_qty = (int) $request->product_qty[$key];
                    }
                }

                if ($request->has("product_price")) {
                    if (array_key_exists($key, $request->product_price)) {
                        $product_price = (float) $request->product_price[$key];
                    }
                }

                if ($request->has("unit_id")) {
                    if (array_key_exists($key, $request->unit_id)) {
                        $unit_id = (int) $request->unit_id[$key];
                    }
                }

                if ($request->has("supplier_id")) {
                    if (array_key_exists($key, $request->supplier_id)) {
                        $supplier_id = (int) $request->supplier_id[$key];
                    }
                }

                $productData[] = array(
                    "grn_id" =>  $grn_id,
                    "po_warehouse_id" =>  $po_warehouse_id,
                    "warehouse_id" =>  $warehouse_id,
                    "po_product_id" =>  $po_product_id,
                    "product_id" =>  $product_id,
                    "product_qty" =>  $product_qty,
                    "product_price" =>  $product_price,
                    "unit_id" =>  $unit_id,
                    "supplier_id" =>  $supplier_id,
                    "seq_num" => $seq_num,
                    "is_active" =>  1,
                    "created_by" =>  Auth::user()->id,
                    "updated_by" =>  Auth::user()->id,
                );

                $seq_num++;
                
            } catch (Exception $e) {

                return 0;
                
            }

            //endforarch
        }

        //store product

        if (count($productData) > 0) {

            foreach ($productData as $key => $value) {

                try {

                    $grnProduct = GrnProduct::create($productData[$key]);

                    $poWarehouseUpdate = app("App\Http\Controllers\Purchase\PurchaseOrderController")
                                ->updatePoWarehouseStatus($productData[$key]);

                } catch (Exception $e) {

                    return 0;
                }

                //endforarch
            }

            return 1;

            //endif
        }

        
    }

    //store product Edit Page
    public function storeProduct(Request $request)
    {

        $productData = [];
        $seq_num = 1;
        $grnProd = GrnProduct::where("grn_id", $request->grn_id)
            ->orderBy("seq_num", "DESC")
            ->first();

        if(!empty($grnProd)){
            $seq_num = $grnProd->seq_num + 1;
        }

        foreach ($request->products as $key => $value) {

            $qty = $request->qty[$key] ?? null;

            //convert to Illuminate Request
            $prodRequest = new \Illuminate\Http\Request();
            $prodRequest->setMethod('POST');
            $prodRequest->request->add(['product_id' => $value]);

            $findProduct = app("App\Http\Controllers\Purchase\PurchaseOrderController")->getProductWarehousebyPoWarehouseId($prodRequest, $value, 1);

            if($findProduct->count()<=0)
            {

                return response()->json("Product Not Foundt", 400);
            }

            if($findProduct->count()>0)
            {

                $productData[] = array(
                    "grn_id" =>  $request->grn_id,
                    "po_warehouse_id" =>  $findProduct->po_warehouse_id,
                    "warehouse_id" =>  $findProduct->warehouse_id,
                    "po_product_id" =>  $findProduct->po_product_id,
                    "product_id" =>  $findProduct->product_id,
                    "product_qty" =>  $qty,
                    "product_price" =>  $findProduct->product_price,
                    "unit_id" =>  $findProduct->prchsunitId,
                    "supplier_id" =>  $findProduct->supplier_id,
                    "seq_num" => $seq_num,
                    "is_active" =>  1,
                    "created_by" =>  Auth::user()->id,
                    "updated_by" =>  Auth::user()->id,
                );

                $seq_num++;
            }
        }

        //store product

        if (count($productData) > 0) {

            foreach ($productData as $key => $value) {

                try {

                    $grnProduct = GrnProduct::create($productData[$key]);

                    $poWarehouseUpdate = app("App\Http\Controllers\Purchase\PurchaseOrderController")
                                ->updatePoWarehouseStatus($productData[$key]);

                } catch (Exception $e) {

                    return response()->json("Something Wrong (Store)");
                }

                //endforarch
            }

            //endif
        }

        return response()->json("add Product successfully");
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(GrnProductDataTable $datatable, $id)
    {
        \View::share("name_form", "Show");
        \View::share("read_only", true);

        $role = Role::find(Auth::user()->role_id);

        if($role->hasPermissionTo('grn-edit')) {

            $grn = Grn::where("grn_id", $id)->where("is_active", 1)->first();

            if(empty($grn)){

                \Session::flash('not_permitted', 'The data not found.');
                return redirect()->back();
            }

            $this->grnSelectionList();

            $this->selectionList["grn"] = $grn;
            $this->selectionList["id"] = $id;

            return $datatable->with("id", $id)->render("grn.edit",  $this->selectionList);
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(GrnProductDataTable $datatable, $id)
    {

        \View::share("name_form", "Edit");

        $role = Role::find(Auth::user()->role_id);

        if($role->hasPermissionTo('grn-edit')) {

            $grn = Grn::where("grn_id", $id)->where("is_active", 1)->first();

            if(empty($grn)){

                \Session::flash('not_permitted', 'The data not found.');
                return redirect()->back();
            }

            $this->grnSelectionList();

            $this->selectionList["grn"] = $grn;
            $this->selectionList["id"] = $id;

            return $datatable->with("id", $id)->render("grn.edit",  $this->selectionList);
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
    public function update(Request $request, $id)
    {

        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);
        if ($role->hasPermissionTo('purchases-request-edit')) {

            $grn = Grn::where("grn_id", $id)->where("is_active", 1)->first();

            if($request->has("approve_grn")){

                $grnProd = GrnProduct::where("grn_id", $id)->where("is_active", 1)->get();

                if ($grnProd->count() == 0) {

                    return redirect()->back()->with('not_permitted', 'Sorry! You need to add Product, first !');
                }

                $stockIn = app("App\Http\Controllers\Stock\StockController")->stockIn($grnProd, 'GRN');


                if ($stockIn == 1) {

                    $grn->is_approve             = 1;
                    $grn->approve_by             = Auth::user()->id;
                    $grn->approve_date           = now();
                    $grn->updated_by             = Auth::user()->id;

                    $grn->save();

                    \Session::flash('message', 'GRN Stock In and approved successfully');  
                    return redirect(route('grn.edit', $id));

                }

                return redirect()->back()->with('not_permitted', 'Something wrong(Stock In)');
            }

            $grn->supplier_do_no                = $request->supplier_do_no;
            $grn->grn_date                = $request->grn_date;
            $grn->grn_remark             = $request->grn_remark;
            $grn->updated_by             = Auth::user()->id;

            $grn->save();

            \Session::flash('message', 'PR updated successfully');  
            return redirect()->back();

        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');

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

    public function destroyGrnProduct($id)
    {
        return "DESTROY";
    }

    public function receivedQtyByPoWarehouse($id, $is_not_ajax=null)
    {
        $grnQty = GrnProduct::where("is_active", 1)
                            ->where("po_warehouse_id", $id)
                            ->sum("product_qty");

        if($is_not_ajax==1){
            return $grnQty;
        }

        return response()->json($grnQty);
    }

    //find grn product by Id
    public function getProductbyId($id, $is_not_ajax=null)
    {

        $grnProd = GrnProduct::leftjoin(Grn::getTableName()." as grn", "grn.grn_id", GrnProduct::getTableName().".grn_id")
                                ->leftjoin(MasterProduct::getTableName()." as prod", "prod.product_id", GrnProduct::getTableName().".product_id") 
                                ->leftjoin(Brand::getTableName()." as brand", "brand.id", "prod.product_brand")
                                ->leftjoin(Supplier::getTableName()." as supp", "supp.id", GrnProduct::getTableName().".supplier_id")
                                ->leftjoin(Warehouse::getTableName()." as warehouse", "warehouse.id", GrnProduct::getTableName().".warehouse_id")
                                ->leftjoin(Unit::getTableName()." as pUnit", "pUnit.id", GrnProduct::getTableName().".unit_id")
                                ->select(
                                            GrnProduct::getTableName().".grn_product_id",
                                            GrnProduct::getTableName().".product_qty",
                                            GrnProduct::getTableName().".product_price",
                                            "grn.is_approve",
                                            "prod.product_id",
                                            "prod.product_name",
                                            "prod.product_sku",
                                            "prod.product_upc",
                                            "brand.id as brand_id",
                                            "brand.title as brand_name",
                                            "supp.name as supplier_name",
                                            "supp.id as supplier_id",
                                            "warehouse.name as warehouse_name",
                                            "pUnit.unit_code as prchsunit",
                                            "pUnit.id as prchsunitId"

                                 )
                                ->where(GrnProduct::getTableName().".grn_product_id", $id)
                                ->where(GrnProduct::getTableName().".is_active", 1)
                                ->first();

        if($is_not_ajax==1){
            
            return $grnProd;
        }

        return response()->json($grnProd);
    }

    public function updateProduct(Request $request)
    {

        if (empty($request->grn_product_id)) {

            return response()->json("Something Wrong (1)");
        }
        
        $grnProd = GrnProduct::where("grn_product_id", $request->grn_product_id)->first();
        $poProdW = PurchaseOrderProductWarehouse::where("po_warehouse_id", $grnProd->po_warehouse_id)
                                                ->select("warehouse_qty")
                                                ->first();

        if (empty($grnProd)) {
            return response()->json("Something Wrong (2)");
        }

        $recievedQty = $this->receivedQtyByPoWarehouse($grnProd->po_warehouse_id, 1);

        $amountQty = $recievedQty + ($request->product_qty - $grnProd->product_qty);


        if ($amountQty > $poProdW->warehouse_qty) {

            return response()->json("The qty cannot be bigger than unreceived qty");

        }

        $grnProd->product_qty = $request->product_qty;

        $grnProd->updated_by = Auth::user()->id;

        $grnProd->save();

        $productData[] = array(

            "po_warehouse_id" =>  $grnProd->po_warehouse_id,
            "product_qty" =>  $grnProd->product_qty

        );

        $poWarehouseUpdate = app("App\Http\Controllers\Purchase\PurchaseOrderController")
                                ->updatePoWarehouseStatus($productData[0]);



        return response()->json("Update successfully");
    }
}
