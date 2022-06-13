<?php 
$plMsg = "Are you sure ?";

?>

{!! Form::open(['route' => ['mProduct.destroyprodsupp', $product_supplier_id], 'method' => 'delete']) !!}
<div class='btn-group'>

	<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#edit_supplier" onclick="getSupplierDetail({{ $product_supplier_id }})"><i class="fa fa-pencil fa-fw"></i></button>

	{!! Form::button('<i class="fa fa-trash fa-fw"></i>', [
	'type' => 'submit',
	'class' => 'btn btn-danger btn-sm',
	'onclick' => "return confirm('".$plMsg."')"
	]) !!}

</div>
{!! Form::close() !!}