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

use App\DataTables\GrnDataTable;

class GrnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $documentType = "good_receive_note";

    public function grnSelectionList()
    {
        $role = Role::find(Auth::user()->role_id);
        $permissions = Role::findByName($role->name)->permissions;
        foreach ($permissions as $permission)
            $all_permission[] = $permission->name;
        if(empty($all_permission))
            $all_permission[] = 'dummy text';


        $this->selectionList["permissions_lists"] = $all_permission;
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

        \Session::flash('message', 'Product created successfully');  

        return back();
        //return redirect(route('grn.edit', $grn->grn_id));
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
