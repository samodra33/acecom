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
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestProduct;
use App\Models\MasterProduct;
use App\Models\MasterProductSupplier;
use App\Models\PurchaseType;

use App\DataTables\PurchaseRequestDataTable;
use App\DataTables\PurchaseRequestProductDataTable;
use App\DataTables\MultipleFunctionPRDataTable;

class PurchaseRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $selectionList = array(
        "permissions_lists" =>array(),
        "product_list" => array(),
        "gst_list" => array(),
        "currency_list" => array(),
        "purchase_type_list" => array()
    );

    private $documentType = "purchase_request";

    public function prSelectionList()
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

    public function purchaseTypeselectList()
    {
        return PurchaseType::select("type_id", "type_name")
            ->where("is_active", 1)
            ->pluck('type_name','type_id'); 
    }

    public function selectList()
    {
        return PurchaseRequest::select("pr_id", "pr_no")
            ->where("is_active", 1)
            ->pluck('pr_no','pr_id'); 
    }

    //get multi pr table

    public function gerMultiPrTable(Request $request, MultipleFunctionPRDataTable $dataTable)
    {
        return $dataTable
            ->with([
            "unaprove" => $request->unaprove,
            "convert" => $request->convert
            ])
            ->render('purchase_request.edit');
    }

    public function index(PurchaseRequestDataTable $datatable)
    {

        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('purchases-request-index')) {
            
            $this->prSelectionList();
            return $datatable->render("purchase_request.index", $this->selectionList);
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
        $this->prSelectionList();
        return view("purchase_request.create")->with($this->selectionList);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //get PR no

        $getDocumentNumber = app("App\Http\Controllers\DocumentNumber\DocumentNumberController")->generate($this->documentType);

        if (empty($getDocumentNumber)) {

            \Session::flash('not_permitted', 'Something wrong. Please try again a moment.');  
            return redirect()->back();
        }
        $pr_no = $getDocumentNumber["document_no"];
        $sequenceID = $getDocumentNumber["sequence_id"];
        $nextSequence = $getDocumentNumber["next_sequence"];

        //store PR

        $pr = new PurchaseRequest();

        $pr->pr_no                  = $pr_no;
        $pr->pr_date                = $request->pr_date;
        $pr->pr_type                = $request->pr_type;
        $pr->pr_remarks             = $request->pr_remarks;
        $pr->pr_remarks_supplier    = $request->pr_remarks_to_supplier;
        $pr->created_by             = Auth::user()->id;
        $pr->updated_by             = Auth::user()->id;

        $pr->save();

        if (!isset($pr->pr_id)) {

            \Session::flash('not_permitted', 'Failed !');  
            return redirect()->back();

        }

        $getUpdateSequence = app("App\Http\Controllers\DocumentNumber\DocumentNumberController")
                                ->updateSequence($sequenceID, $nextSequence);

        if (empty($getUpdateSequence)) {

            \Session::flash('not_permitted', 'Error while updating PR number!');  
            return redirect()->back();
        }

        if ($request->product) {
            $prProduct = $this->storePrProduct($pr->pr_id,$request);
        }

        \Session::flash('message', 'Product created successfully');  
        return redirect(route('pr.edit', $pr->pr_id));


    }

    //store product create Page
    public function storePrProduct($pr_id, $request)
    {
        $productData = array();
        $seq_num = 1;

        foreach ($request->product as $key => $value) {

            $product_price = 0;
            $product_qty = 0;
            $product_unit_id = 1;

            //get Product Data

            try {

                if ($request->has("product_id")) {
                    if (array_key_exists($key, $request->product_id)) {
                        $product_id = (int) $request->product_id[$key];
                    }
                }

                if ($request->has("supplier_id")) {
                    if (array_key_exists($key, $request->supplier_id)) {
                        $supplier_id = (int) $request->supplier_id[$key];
                    }
                }

                if ($request->has("product_supplier")) {
                    if (array_key_exists($key, $request->product_supplier)) {
                        $product_supplier_moq = (int) $request->product_supplier[$key];
                    }
                }

                if ($request->has("product_unit_id")) {
                    if (array_key_exists($key, $request->product_supplier)) {
                        $product_unit_id = (int) $request->product_unit_id[$key];
                    }
                }

                if ($request->has("product_qty")) {
                    if (array_key_exists($key, $request->product_qty)) {
                        $product_qty = (int) $request->product_qty[$key];
                    }
                }

                if ($request->has("product_currency")) {
                    if (array_key_exists($key, $request->product_currency)) {
                        $product_currency = (int) $request->product_currency[$key];
                    }
                }

                if ($request->has("product_gst")) {
                    if (array_key_exists($key, $request->product_gst)) {
                        $product_gst = (int) $request->product_gst[$key];
                    }
                }

                if ($request->has("product_moqprice")) {
                    if (array_key_exists($key, $request->product_moqprice)) {
                        $product_price = (float) $request->product_moqprice[$key];
                    }
                }

                $productData[] = array(
                    "product_id" =>  $product_id,
                    "pr_id" =>  $pr_id,
                    "supplier_id" =>  $supplier_id,
                    "supplier_moq_id" =>  $product_supplier_moq,
                    "product_qty" =>  $product_qty,
                    "product_purchase_unit" =>  $product_unit_id,
                    "product_price" =>  $product_price,
                    "product_currency" =>  $product_currency,
                    "product_gst" =>  $product_gst,
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

                    $productPr = PurchaseRequestProduct::create($productData[$key]);

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

        if ($request->func_type == "store") {

            $seq_num = 1;

            $seqCheck = PurchaseRequestProduct::where("pr_id", $request->pr_id)
                                                ->orderBy("seq_num", "desc")
                                                ->first();
            if (!empty($seqCheck)) {

                $seq_num = $seq_num + $seqCheck->seq_num;
            }

            $prprod = new PurchaseRequestProduct();

            $prprod->pr_id                  = $request->pr_id;
            $prprod->product_id             = $request->product_id;
            $prprod->supplier_id            = $request->supplier_id;
            $prprod->supplier_moq_id        = $request->supplier_moq_id;
            $prprod->product_qty            = $request->product_qty;
            $prprod->product_purchase_unit  = $request->product_purchase_unit;
            $prprod->product_price          = $request->product_price;
            $prprod->product_gst            = $request->product_gst;
            $prprod->product_currency       = $request->product_currency;
            $prprod->seq_num                = $seq_num;
            $prprod->created_by             = Auth::user()->id;
            $prprod->updated_by             = Auth::user()->id;

            $prprod->save();

            return response()->json("Record Added successfully.", 200);
        }

        if ($request->func_type == "update") {

            $prprod = PurchaseRequestProduct::where("pr_product_id", $request->pr_product_id)
                                                ->first();
            $prprod->product_id             = $request->product_id;
            $prprod->supplier_id            = $request->supplier_id;
            $prprod->supplier_moq_id        = $request->supplier_moq_id;
            $prprod->product_qty            = $request->product_qty;
            $prprod->product_purchase_unit  = $request->product_purchase_unit;
            $prprod->product_price          = $request->product_price;
            $prprod->product_gst            = $request->product_gst;
            $prprod->product_currency       = $request->product_currency;
            $prprod->updated_by             = Auth::user()->id;

            $prprod->save();

            return response()->json("Record updated successfully.", 200);
        }

        return response()->json("Something Wrong (controller method type)");


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseRequestProductDataTable $datatable, $id)
    {
        \View::share("name_form", "Show");
        \View::share("read_only", true);

        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);
        if ($role->hasPermissionTo('purchases-request-index')) {

            $pr = PurchaseRequest::where("pr_id", $id)->where("is_active", 1)->first();

            if(empty($pr)){

                \Session::flash('not_permitted', 'The data not found.');
                return redirect()->back();
            }

            $this->prSelectionList();

            $this->selectionList["pr"] = $pr;
            $this->selectionList["id"] = $id;

            return $datatable->with("id", $id)->render('purchase_request.edit', $this->selectionList);
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PurchaseRequestProductDataTable $datatable, $id)
    {

        \View::share("name_form", "Edit");

        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);
        if ($role->hasPermissionTo('purchases-request-edit')) {

            $pr = PurchaseRequest::where("pr_id", $id)->where("is_active", 1)->first();

            if(empty($pr)){

                \Session::flash('not_permitted', 'The data not found.');
                return redirect()->back();
            }

            $this->prSelectionList();

            $this->selectionList["pr"] = $pr;
            $this->selectionList["id"] = $id;

            return $datatable->with("id", $id)->render('purchase_request.edit', $this->selectionList);
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

            $pr = PurchaseRequest::where("pr_id", $id)->where("is_active", 1)->first();

            if(empty($pr)){

                \Session::flash('not_permitted', 'The data not found.');
                return redirect()->back();
            }

            if($request->has("approve_pr")){
                
                $pr->is_approve   = 1;
                $pr->pr_approved_by   = Auth::user()->id;
                $pr->pr_approved_dt   = now();
                $pr->updated_by       = Auth::user()->id;

                $pr->save();

                \Session::flash('message', 'PR Approved successfully');  
                return redirect(route('pr.show', $pr->pr_id));
            }

            if($request->has("convert_to_po")){

                $convert = app("App\Http\Controllers\Purchase\PurchaseOrderController")->convertPrtoPo($id);

                if ($convert == 200) {
                    \Session::flash('message', 'PR Converted to PO successfully');  
                    return redirect(route('po.index'));
                }

                if ($convert == 400) {
                    \Session::flash('not_permitted', 'FAILED :: This PR has been converted into a PO and approved as a PO');  
                    return redirect(route('pr.show', $pr->pr_id));
                }

                \Session::flash('not_permitted', 'PR Converted Fail');  
                return redirect(route('pr.show', $pr->pr_id));

                
            }

            $pr->pr_date                = $request->pr_date;
            $pr->pr_remarks             = $request->pr_remarks;
            $pr->pr_remarks_supplier    = $request->pr_remarks_to_supplier;
            $pr->updated_by             = Auth::user()->id;

            $pr->save();

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
        return "DESTROY";
    }

    public function destroyPrProduct($id)
    {
        return "DESTROY";
    }

    //find product by Id
    public function getProductbyId($id, $is_not_ajax=null)
    {

        $prProd = PurchaseRequestProduct::leftjoin(MasterProduct::getTableName()." as prod", "prod.product_id", PurchaseRequestProduct::getTableName().".product_id") 
                                        ->leftjoin(Category::getTableName()." as category", "category.id", "prod.product_category")
                                        ->leftjoin(Brand::getTableName()." as brand", "brand.id", "prod.product_brand")
                                        ->leftjoin(Unit::getTableName()." as pUnit", "pUnit.id", PurchaseRequestProduct::getTableName().".product_purchase_unit")
                                        ->leftjoin(MasterProductSupplier::getTableName()." as suppProd", "suppProd.product_supplier_id", PurchaseRequestProduct::getTableName().".supplier_moq_id")
                                        ->leftjoin(Supplier::getTableName()." as supp", "supp.id", PurchaseRequestProduct::getTableName().".supplier_id")
                                        ->select(
                                                   PurchaseRequestProduct::getTableName().".pr_product_id" ,
                                                   PurchaseRequestProduct::getTableName().".product_qty" ,
                                                   PurchaseRequestProduct::getTableName().".product_price" ,
                                                   PurchaseRequestProduct::getTableName().".supplier_id" ,
                                                   PurchaseRequestProduct::getTableName().".product_gst" ,
                                                   PurchaseRequestProduct::getTableName().".product_currency" ,
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
                                        ->where("pr_product_id", $id)
                                        ->first();

        if($is_not_ajax==1){
            return $prProd;
        }

        return response()->json($prProd);
    }

    ////bulk approve

    public function bulkPrApprove(Request $request)
    {

        $pr = PurchaseRequest::whereIn("pr_id", $request->pr_id)
        ->update([
            "is_approve" => 1,
            "pr_approved_dt" => now(),
            "pr_approved_by" => Auth::user()->id,
            "updated_by" => Auth::user()->id,
        ]);

        if ($pr) {

            return response()->json("Record approved successfully.", 200);
        }

        return response()->json("Something Wrong (controller)");
    }

    ////bulk approve

    public function bulkConverttoPo(Request $request)
    {

        try {
            
            foreach ($request->pr_id as $key => $value) {

                app("App\Http\Controllers\Purchase\PurchaseOrderController")->convertPrtoPo($value);
            }

        } catch (Exception $e) {

            return response()->json("Something Wrong (controller)");

        }

        return response()->json("Record converted successfully.", 200);
    }

}
