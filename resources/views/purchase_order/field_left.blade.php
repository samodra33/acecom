<div class="col-md-6">
	<div class="row form-group">
		<div class="col-md-4">
			<label class="control-label">PO No.</label>
		</div>
		<div class="col-md-8">
			{{ Form::text("po_no", null, array( 
			"class"=>"form-control", "placeholder"=>"--- Generate by system ---",
			"readonly"=>"readonly",
			"style"=>"text-align: center;")
			) }}
		</div>
	</div>

	<div class="row form-group">
		<div class="col-md-4">
			<label class="control-label">PR No.</label>
		</div>
		<div class="col-md-8">
			{{ Form::select("pr_no", $pr_lists, isset($po->pr_id)?$po->pr_id:null, array("class"=>"form-control select2_picker", "placeholder"=>"Select", "disabled"=>"true")) }}
		</div>
	</div>

	<div class="row form-group">
		<div class="col-md-4">
			<label class="control-label">Supplier</label>
		</div>
		<div class="col-md-8">
			{{ Form::select("po_supplier", $supplier_lists, isset($po->po_supplier)?$po->po_supplier:null, array("class"=>"form-control select2_picker", "placeholder"=>"Select", "disabled"=>"true")) }}
		</div>
	</div>

</div>
