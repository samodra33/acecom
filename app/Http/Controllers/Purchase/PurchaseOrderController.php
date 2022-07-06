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
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestProduct;
use App\Models\MasterProduct;
use App\Models\MasterProductSupplier;
use App\Models\PurchaseType;

use App\DataTables\PurchaseOrderDataTable;
use App\DataTables\PurchaseOrderProductDataTable;

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

    //find product by Id
    public function getProductbyId($id)
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

        if(!empty($poProd)){
            $res = $poProd->toArray();
        }

        return response()->json($poProd);
    }
}
