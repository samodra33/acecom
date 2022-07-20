<?php

namespace App\Http\Controllers\Purchase;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use Keygen;
use Auth;
use DNS1D;
use DB;

use App\PosSetting;
use App\Warehouse;
use App\Account;
use App\Supplier;
use App\Brand;
use App\Category;
use App\Unit;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProduct;
use App\Models\PurchaseOrderProductWarehouse;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestProduct;
use App\Models\MasterProduct;
use App\Models\MasterProductSupplier;
use App\Models\PurchaseType;

use App\DataTables\PurchaseOrderDataTable;
use App\DataTables\PurchaseOrderProductDataTable;
use App\DataTables\PurchaseOrderProductWarehouseDataTable;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $documentType = "purchase_order";

    private $selectionList = array(
        "permissions_lists" =>array(),
        "product_list" => array(),
        "gst_list" => array(),
        "currency_list" => array(),
        "purchase_type_list" => array(),
        "supplier_lists" => array(),
        "pr_lists" => array(),
        "warehouse_lists" => array(),
    );
    
    public function poSelectionList()
    {
        $role = Role::find(Auth::user()->role_id);
        $permissions = Role::findByName($role->name)->permissions;
        foreach ($permissions as $permission)
            $all_permission[] = $permission->name;
        if(empty($all_permission))
            $all_permission[] = 'dummy text';


        $this->selectionList["permissions_lists"] = $all_permission;
        $this->selectionList["product_list"] = app("App\Http\Controllers\Product\ProductMasterController")->selectList();
        $this->selectionList["gst_list"] = app("App\Http\Controllers\TaxController")->selectList();
        $this->selectionList["currency_list"] = app("App\Http\Controllers\CurrencyController")->selectList();
        $this->selectionList["purchase_type_list"] = app("App\Http\Controllers\Purchase\PurchaseRequestController")->purchaseTypeselectList();
        $this->selectionList["supplier_lists"] = app("App\Http\Controllers\SupplierController")->selectList();
        $this->selectionList["pr_lists"] = app("App\Http\Controllers\Purchase\PurchaseRequestController")->selectList();
        $this->selectionList["warehouse_lists"] = app("App\Http\Controllers\WarehouseController")->selectList();
    }

    public function index(PurchaseOrderDataTable $datatable)
    {
        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('purchases-order-index')) {
            
            $this->poSelectionList();
            return $datatable->render("purchase_order.index", $this->selectionList);
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }


    public function convertPrtoPo($pr_id)
    {

        $poApprove = PurchaseOrder::where("pr_id", $pr_id)
                            ->where("is_active", 1)
                            ->where("is_approve", 1)
                            ->count(); 

        $prProduct = PurchaseRequestProduct::join(PurchaseRequest::getTableName()." as pr", "pr.pr_id", PurchaseRequestProduct::getTableName().".pr_id")
                                           ->where(PurchaseRequestProduct::getTableName().".pr_id", $pr_id)
                                           ->where(PurchaseRequestProduct::getTableName().".is_active", 1)
                                           ->select(
                                                    PurchaseRequestProduct::getTableName().".pr_id", 
                                                    PurchaseRequestProduct::getTableName().".supplier_id",
                                                    "pr.pr_remarks_supplier",
                                                    "pr.pr_type"
                                                )
                                           ->groupBy(
                                                    PurchaseRequestProduct::getTableName().".pr_id", 
                                                    PurchaseRequestProduct::getTableName().".supplier_id",
                                                    "pr.pr_remarks_supplier",
                                                    "pr.pr_type"
                                            );
        
        $prProduct = $prProduct->get();

        $prProductCount = $prProduct->count();

        //check approved PO

        if ($poApprove == $prProductCount) {
           
           return 400;
        }

        
        //Store PO
        if ($prProduct) {//if1

            foreach ($prProduct as $key => $pr) {//foreach

                $po = PurchaseOrder::where("pr_id", $pr->pr_id)
                                            ->where("po_supplier", $pr->supplier_id)
                                            ->where("is_active", 1)
                                            ->first(); 
                //store
                if ( empty($po) ) {//if2

                    //get PO no
                    $getDocumentNumber = app("App\Http\Controllers\DocumentNumber\DocumentNumberController")->generate($this->documentType);

                    if (empty($getDocumentNumber)) {

                        \Session::flash('not_permitted', 'Something wrong. Please try again a moment.');  
                        return redirect()->back();
                    }
                    $po_no = $getDocumentNumber["document_no"];
                    $sequenceID = $getDocumentNumber["sequence_id"];
                    $nextSequence = $getDocumentNumber["next_sequence"];

                    //store PO
                    $po = new PurchaseOrder();

                    $po->pr_id = $pr->pr_id;
                    $po->po_no = $po_no;
                    $po->po_type = $pr->pr_type;
                    $po->po_supplier = $pr->supplier_id;
                    $po->po_date = now();
                    $po->po_remark = $pr->pr_remarks_supplier;

                    $po->created_by = Auth::user()->id;
                    $po->updated_by = Auth::user()->id;

                    $po->save();

                    if (!isset($po->po_id)) {
                        return 0;
                    }

                    //update sequence
                    $getUpdateSequence = app("App\Http\Controllers\DocumentNumber\DocumentNumberController")
                                    ->updateSequence($sequenceID, $nextSequence);

                }//endif2

                else{

                    if ($po->is_approve == 0) {

                        $po->po_type = $pr->pr_type;
                        $po->po_date = now();
                        $po->po_remark = $pr->pr_remarks_supplier;

                        $po->created_by = Auth::user()->id;
                        $po->updated_by = Auth::user()->id;

                        $po->save();
                    }

                }

                if ($po->po_id && $po->is_approve == 0) {

                    $storeProductPo = app("App\Http\Controllers\Purchase\PurchaseOrderController")->convertPrProducttoPoProduct($po->pr_id, $po->po_id, $po->po_supplier);
                }


            }//endforeach

            return 200;

        }//endif1

        return 0;
    }

    public function convertPrProducttoPoProduct($pr_id, $po_id, $po_supplier)
    {

        $prProduct = PurchaseRequestProduct::where("pr_id", $pr_id)
                                           ->where("supplier_id", $po_supplier)
                                           ->get();
        $seq_num = 1;
        $seq_num_check = 1;

        foreach ($prProduct as $key => $value) {

            $poprod = PurchaseOrderProduct::where("pr_product_id", $value->pr_product_id)
                                                ->where("po_id", $po_id)
                                                ->where("is_active", 1)
                                                ->first();
            

            if (empty($poprod)) {

                $seq_num_check = 0;

                $poprod = new PurchaseOrderProduct();

                $poprod->seq_num                = $seq_num;
            }
            
            $poprod->pr_product_id          = $value->pr_product_id;
            $poprod->po_id                  = $po_id;
            $poprod->product_id             = $value->product_id;
            $poprod->supplier_id            = $value->supplier_id;
            $poprod->supplier_moq_id        = $value->supplier_moq_id;
            $poprod->product_qty            = $value->product_qty;
            $poprod->product_purchase_unit  = $value->product_purchase_unit;
            $poprod->product_price          = $value->product_price;
            $poprod->product_gst            = $value->product_gst;
            $poprod->product_currency       = $value->product_currency;
            $poprod->is_active              = $value->is_active;
            $poprod->created_by             = Auth::user()->id;
            $poprod->updated_by             = Auth::user()->id;

            $poprod->save();

            if ($seq_num_check == 1) {

                $seq_num++;
            }
            
        }
    }

    public function show(PurchaseOrderProductDataTable $datatable, $id)
    {

        \View::share("name_form", "View");

        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);
        if ($role->hasPermissionTo('purchases-order-index')) {

            $po = PurchaseOrder::where("po_id", $id)->where("is_active", 1)->first();

            if(empty($po)){

                \Session::flash('not_permitted', 'The data not found.');
                return redirect()->back();
            }

            $this->poSelectionList();

            $this->selectionList["po"] = $po;
            $this->selectionList["id"] = $id;

            return $datatable->with("id", $id)->render('purchase_order.edit', $this->selectionList);
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');

    }

    public function edit(PurchaseOrderProductDataTable $datatable, $id)
    {

        \View::share("name_form", "Edit");

        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);
        if ($role->hasPermissionTo('purchases-order-edit')) {

            $po = PurchaseOrder::where("po_id", $id)->where("is_active", 1)->first();

            if(empty($po)){

                \Session::flash('not_permitted', 'The data not found.');
                return redirect()->back();
            }

            $this->poSelectionList();

            $this->selectionList["po"] = $po;
            $this->selectionList["id"] = $id;

            return $datatable->with("id", $id)->render('purchase_order.edit', $this->selectionList);
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function update(Request $request, $id)
    {
        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);

        if ($role->hasPermissionTo('purchases-order-edit')) {

            $po = PurchaseOrder::where("po_id", $id)->where("is_active", 1)->first();

            if(empty($po)){

                \Session::flash('not_permitted', 'The data not found.');
                return redirect()->back();
            }

            if($request->has("approve_po")){
                
                $po->is_approve   = 1;
                $po->approve_by   = Auth::user()->id;
                $po->approve_date   = now();
                $po->updated_by       = Auth::user()->id;

                $po->save();

                \Session::flash('message', 'PO Approved successfully');  
                return redirect(route('po.show', $po->po_id));
            }

            $po->po_date = $request->po_date;
            $po->po_remark = $request->po_remarks_to_supplier;
            $po->updated_by       = Auth::user()->id;

            $po->save();


            \Session::flash('message', 'Product updated successfully');  
            return redirect()->back();
        }

        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function destroy($id)
    {
        return "DESTROY";
    }

    //find po product by Id
    public function getProductbyId($id, $is_not_ajax=null)
    {

        $poProd = PurchaseOrderProduct::leftjoin(MasterProduct::getTableName()." as prod", "prod.product_id", PurchaseOrderProduct::getTableName().".product_id") 
                                        ->leftjoin(Category::getTableName()." as category", "category.id", "prod.product_category")
                                        ->leftjoin(Brand::getTableName()." as brand", "brand.id", "prod.product_brand")
                                        ->leftjoin(Unit::getTableName()." as pUnit", "pUnit.id", PurchaseOrderProduct::getTableName().".product_purchase_unit")
                                        ->leftjoin(MasterProductSupplier::getTableName()." as suppProd", "suppProd.product_supplier_id", PurchaseOrderProduct::getTableName().".supplier_moq_id")
                                        ->leftjoin(Supplier::getTableName()." as supp", "supp.id", PurchaseOrderProduct::getTableName().".supplier_id")
                                        ->select(
                                                   PurchaseOrderProduct::getTableName().".po_product_id" ,
                                                   PurchaseOrderProduct::getTableName().".product_qty" ,
                                                   PurchaseOrderProduct::getTableName().".product_price" ,
                                                   PurchaseOrderProduct::getTableName().".supplier_id" ,
                                                   PurchaseOrderProduct::getTableName().".product_gst" ,
                                                   PurchaseOrderProduct::getTableName().".product_currency" ,
                                                   "prod.product_id",
                                                   "prod.product_name",
                                                   "prod.product_sku",
                                                   "prod.product_upc",
                                                   "category.id as category_id",
                                                   "category.name as category_name",
                                                   "brand.id as brand_id",
                                                   "brand.title as brand_name",
                                                   "pUnit.unit_code as prchsunit",
                                                   "pUnit.id as prchsunitId",
                                                   "suppProd.product_supplier_id",
                                                   "suppProd.supplier_moq",
                                                   "supp.lead_time"
                                        )
                                        ->where("po_product_id", $id)
                                        ->first();

        if($is_not_ajax==1){
            
            return $poProd;
        }

        return response()->json($poProd);
    }

    //get Product Warehouse

    public function getProductWarehouseTable(Request $request, PurchaseOrderProductWarehouseDataTable $dataTable)
    {
        return $dataTable
            ->with("id", $request->po_product_id)
            ->render('po.edit');
    }

    public function storeproductwarehouse(Request $request)
    {
        $pWarehouse = new PurchaseOrderProductWarehouse();


        $pWarehouse->po_product_id = $request->po_product_id;
        $pWarehouse->warehouse_id = $request->warehouse_id;
        $pWarehouse->warehouse_qty = 0;

        $pWarehouse->created_by       = Auth::user()->id;
        $pWarehouse->updated_by       = Auth::user()->id;

        $pWarehouse->save();

        return response()->json("Warehouse successfully added.");



    }

    public function updateproductwarehouse(Request $request)
    {

        $poProd = PurchaseOrderProduct::Select(PurchaseOrderProduct::getTableName().".product_qty")
                                        ->where("po_product_id", $request->po_product_id)
                                        ->first();


        $pWarehouse = PurchaseOrderProductWarehouse::where("po_warehouse_id", $request->po_warehouse_id);

        $pWarehouse = $pWarehouse->first();

        //qty check

        $sumWarehouse =  PurchaseOrderProductWarehouse::where("po_product_id", $request->po_product_id)
                        ->where("is_active", 1)->sum("warehouse_qty");

        $qtyTotal = $request->qty + ($sumWarehouse - $pWarehouse->warehouse_qty);

        if ($qtyTotal > $poProd->product_qty) {

             return response()->json("Failed :: The amount cannot be more than the purchase order qty.");
        }
        

        $pWarehouse->warehouse_qty = $request->qty;

        $pWarehouse->updated_by       = Auth::user()->id;

        $pWarehouse->save();

        return response()->json("Warehouse successfully updated.");



    }

    public function updatePoWarehouseStatus($data)
    {

        $statusId = 1;

        $pWarehouse = PurchaseOrderProductWarehouse::where("po_warehouse_id", $data["po_warehouse_id"])->first();
        $recievedQty = app("App\Http\Controllers\grn\GrnController")->receivedQtyByPoWarehouse($data["po_warehouse_id"], 1);

        if ($recievedQty < $pWarehouse->warehouse_qty) {
            $statusId = 2;
        }

        if ($recievedQty >= $pWarehouse->warehouse_qty) {
            $statusId = 3;
        }

        $pWarehouse->status = $statusId;

        $pWarehouse->updated_by       = Auth::user()->id;

        $pWarehouse->save();

        return 1;

    }

    public function destroyPoWarehouse($id)
    {

        $pWarehouse = PurchaseOrderProductWarehouse::where("po_warehouse_id", $id)->first();

        $pWarehouse->is_active = 0;

        $pWarehouse->updated_by       = Auth::user()->id;

        $pWarehouse->save();

        return response()->json("Warehouse successfully Deleted.");

    }

    //find po by warehouse
    public function getPobyWarehouse(Request $request, $id)
    {

        $res = array();

        if(!$request->has("warehouse_id")){
            return response()->json($res, 400);
        }

        $poData = PurchaseOrderProduct::join(PurchaseOrderProductWarehouse::getTableName()." as poWarehouse", "poWarehouse.po_product_id", PurchaseOrderProduct::getTableName().".po_product_id")

                                    ->join(PurchaseOrder::getTableName()." as po", "po.po_id", PurchaseOrderProduct::getTableName().".po_id")

                                    ->where(PurchaseOrderProduct::getTableName().".is_active", 1)
                                    ->where("poWarehouse.is_active", 1)
                                    ->where("po.is_approve", 1)
                                    ->where("po.is_active", 1);

        if ($request->warehouse_id != 0) {
           
           $poData = $poData->where("poWarehouse.warehouse_id", $request->warehouse_id);
        }

        $poData = $poData->select("po.po_id", "po.po_no")
                        ->pluck("po.po_no","po.po_id")
                        ->all();

        return response()->json($poData);
    }

    //find po warehouse product by po warehouse Id
    public function getProductWarehousebyPoWarehouseId(Request $request, $id, $is_not_ajax=null)
    {

        $poProd = PurchaseOrderProduct::join(PurchaseOrderProductWarehouse::getTableName()." as poWarehouse", "poWarehouse.po_product_id", PurchaseOrderProduct::getTableName().".po_product_id")

                                        ->leftjoin(Warehouse::getTableName()." as warehouse", "warehouse.id", "poWarehouse.warehouse_id")
                                        ->leftjoin(MasterProduct::getTableName()." as prod", "prod.product_id", PurchaseOrderProduct::getTableName().".product_id") 
                                        ->leftjoin(Category::getTableName()." as category", "category.id", "prod.product_category")
                                        ->leftjoin(Brand::getTableName()." as brand", "brand.id", "prod.product_brand")
                                        ->leftjoin(Unit::getTableName()." as pUnit", "pUnit.id", PurchaseOrderProduct::getTableName().".product_purchase_unit")
                                        ->leftjoin(MasterProductSupplier::getTableName()." as suppProd", "suppProd.product_supplier_id", PurchaseOrderProduct::getTableName().".supplier_moq_id")
                                        ->leftjoin(Supplier::getTableName()." as supp", "supp.id", PurchaseOrderProduct::getTableName().".supplier_id")
                                        ->select(
                                                   PurchaseOrderProduct::getTableName().".po_product_id" ,
                                                   PurchaseOrderProduct::getTableName().".po_id" ,
                                                   PurchaseOrderProduct::getTableName().".product_qty" ,
                                                   PurchaseOrderProduct::getTableName().".product_price" ,
                                                   PurchaseOrderProduct::getTableName().".supplier_id" ,
                                                   PurchaseOrderProduct::getTableName().".product_gst" ,
                                                   PurchaseOrderProduct::getTableName().".product_currency" ,
                                                   "prod.product_id",
                                                   "prod.product_name",
                                                   "prod.product_sku",
                                                   "prod.product_upc",
                                                   "category.id as category_id",
                                                   "category.name as category_name",
                                                   "brand.id as brand_id",
                                                   "brand.title as brand_name",
                                                   "pUnit.unit_code as prchsunit",
                                                   "pUnit.id as prchsunitId",
                                                   "suppProd.product_supplier_id",
                                                   "suppProd.supplier_moq",
                                                   "supp.lead_time",
                                                   "supp.id as supplier_id",
                                                   "supp.name as supplier_name",
                                                   "poWarehouse.po_warehouse_id",
                                                   "poWarehouse.warehouse_qty",
                                                   "poWarehouse.warehouse_id",
                                                   "warehouse.name as warehouse_name"
                                        )
                                        ->where("poWarehouse.po_warehouse_id", $id)
                                        ->first();

        if($is_not_ajax==1){
            
            return $poProd;
        }

        return response()->json($poProd);
    }

    public function getAddPoProduct(Request $request)
    {
        $toRowData = [];

        foreach ($request->products as $key => $value) {

            $qty = $request->qty[$key] ?? null;

            //convert to Illuminate Request
            $prodRequest = new \Illuminate\Http\Request();
            $prodRequest->setMethod('POST');
            $prodRequest->request->add(['product_id' => $value]);

            $findProduct = $this->getProductWarehousebyPoWarehouseId($prodRequest, $value, 1);

            if($findProduct->count()<=0)
            {

                return response()->json("Supplier Not Found, set Default Supplier First", 400);
            }

            if($findProduct->count()>0)
            {

                $receivedQty = app("App\Http\Controllers\Grn\GrnController")->receivedQtyByPoWarehouse($value, 1);
                $unreceivedQty = $findProduct->warehouse_qty - $receivedQty;

                $toRowData[] = array(
                    "po_warehouse_id" => $value,
                    "qty" => $qty,
                    "unreceivedQty" => $unreceivedQty,
                    "po_id"   => $findProduct->po_id,
                    "po_product_id"   => $findProduct->po_product_id,
                    "product_price"   => $findProduct->product_price,
                    "product_id"   => $findProduct->product_id,
                    "product_name"   => $findProduct->product_name,
                    "product_sku"   => $findProduct->product_sku,
                    "product_upc"   => $findProduct->product_upc,
                    "category_name"   => $findProduct->category_name,
                    "brand_name"   => $findProduct->brand_name,
                    "prchsunitId"   => $findProduct->prchsunitId,
                    "prchsunit"   => $findProduct->prchsunit,
                    "supplier_id"   => $findProduct->supplier_id,
                    "supplier_name"   => $findProduct->supplier_name,
                    "warehouse_name"   => $findProduct->warehouse_name,
                    "warehouse_id"   => $findProduct->warehouse_id,

                );
            }
        }

        return response()->json($toRowData);
    }


}
