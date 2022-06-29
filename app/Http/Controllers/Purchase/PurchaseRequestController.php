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

class PurchaseRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $selectionList = array(
        "permissions_lists" =>array()
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
    }

    public function index()
    {

        $role = Role::find(Auth::user()->role_id);
        if($role->hasPermissionTo('purchases-request-index')) {
            
            $this->prSelectionList();
            return view('purchase_request.index')->with($this->selectionList);;
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
            return redirect()->back()->with('not_permitted', 'Something wrong. Please try again a moment.');
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

        $getUpdateSequence = app("App\Http\Controllers\DocumentNumber\DocumentNumberController")
                                ->updateSequence($sequenceID, $nextSequence);

        if (empty($getUpdateSequence)) {
            return redirect()->back()->with('not_permitted', 'Error while updating PR number!');
        }

        \Session::flash('message', 'Product created successfully');  
        return redirect()->back();


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
        //
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
        //
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
}
