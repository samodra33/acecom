<?php 
$plMsg = "Are you sure ?";


//get role
$role = DB::table('roles')->find(Auth::user()->role_id);

//view role
$view_permission = DB::table('permissions')->where('name', 'products-index')->first();
$view_permission_active = DB::table('role_has_permissions')->where([
	['permission_id', $view_permission->id],
	['role_id', $role->id]
])->first();

//edit role
$edit_permission = DB::table('permissions')->where('name', 'products-edit')->first();
$edit_permission_active = DB::table('role_has_permissions')->where([
	['permission_id', $edit_permission->id],
	['role_id', $role->id]
])->first();


//delete role
$delete_permission = DB::table('permissions')->where('name', 'products-delete')->first();
$delete_permission_active = DB::table('role_has_permissions')->where([
	['permission_id', $delete_permission->id],
	['role_id', $role->id]
])->first();

?>

{!! Form::open(['route' => ['mProduct.destroy', $product_id], 'method' => 'delete']) !!}
<div class='btn-group'>

@if($view_permission_active)

	<a href="#" data-toggle="modal" data-target="#product-details" class="btn btn-warning" onclick="getProductDetail({{$product_id}})">
		<i class="fa fa-eye fa-fw"></i>
	</a>

@endif

@if($edit_permission_active)
	<a href="{{ route('mProduct.edit', $product_id) }}" class='btn btn-primary btn-sm'>
		<i class="fa fa-pencil fa-fw"></i>
	</a>
@endif

@if($delete_permission_active)

	{!! Form::button('<i class="fa fa-trash fa-fw"></i>', [
	'type' => 'submit',
	'class' => 'btn btn-danger btn-sm',
	'onclick' => "return confirm('".$plMsg."')"
	]) !!}
@endif
</div>
{!! Form::close() !!}