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
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestProduct;

use App\DataTables\PurchaseRequestDataTable;
use App\DataTables\PurchaseRequestProductDataTable;

class PurchaseRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $selectionList = array(
        "permissions_lists" =>array(),
        "product_list" => array()
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

    //store product
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return "SHOW";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PurchaseRequestProductDataTable $datatable, $id)
    {
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

            $pr->pr_date                = $request->pr_date;
            $pr->pr_remarks             = $request->pr_remarks;
            $pr->pr_remarks_supplier    = $request->pr_remarks_to_supplier;
            $pr->updated_by             = Auth::user()->id;

            $pr->save();

            \Session::flash('message', 'Product updated successfully');  
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
}
