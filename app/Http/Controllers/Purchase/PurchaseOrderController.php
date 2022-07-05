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

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProduct;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestProduct;
use App\Models\MasterProduct;
use App\Models\MasterProductSupplier;
use App\Models\PurchaseType;

use App\DataTables\PurchaseOrderDataTable;

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
        "purchase_type_list" => array()
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

        $prProductCount = $prProduct->count();

        //check approved PO

        if ($poApprove == $prProductCount) {
           
           return 400;
        }

        $prProduct = $prProduct->get();

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

    public function show($id)
    {

        return "SHOW";

    }

    public function edit($id)
    {
        return "EDIT";
    }

    public function update(Request $request, $id)
    {
        return "UPDATE";
    }

    public function destroy($id)
    {
        return "DESTROY";
    }
}
