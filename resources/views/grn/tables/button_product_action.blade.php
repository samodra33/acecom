<?php 
$plMsg = "Are you sure ?";
$btn = 'primary';
$btn_fa = 'pencil';

$showDelButton = true;
if ($is_approve == 1 || request()->route()->getName() == 'grn.show') {

	$btn = 'warning';
	$btn_fa = 'eye';
	$showDelButton = false;
}

//get role
$role = DB::table('roles')->find(Auth::user()->role_id);

//edit role
$edit_permission = DB::table('permissions')->where('name', 'grn-edit')->first();
$edit_permission_active = DB::table('role_has_permissions')->where([
	['permission_id', $edit_permission->id],
	['role_id', $role->id]
])->first();


//delete role
$delete_permission = DB::table('permissions')->where('name', 'grn-delete')->first();
$delete_permission_active = DB::table('role_has_permissions')->where([
	['permission_id', $delete_permission->id],
	['role_id', $role->id]
])->first();

?>

{!! Form::open(['route' => ['grnProd.destroyprprod', $grn_product_id], 'method' => 'delete']) !!}
<div class='btn-group'>
	
	<a href="#" class='btn btn-{{$btn}} btn-sm' data-toggle="modal" data-target="#edit_product" onclick="getProduct( {{ $grn_product_id }} )">
		<i class="fa fa-{{$btn_fa}} fa-fw"></i>
	</a>

@if($delete_permission_active && $showDelButton)

	{!! Form::button('<i class="fa fa-trash fa-fw"></i>', [
	'type' => 'submit',
	'class' => 'btn btn-danger btn-sm',
	'onclick' => "return confirm('".$plMsg."')"
	]) !!}
@endif
</div>
{!! Form::close() !!}