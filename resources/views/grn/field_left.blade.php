<div class="col-md-6">
	<div class="row form-group">
		<div class="col-md-4">
			<label class="control-label">GRN No.</label>
		</div>
		<div class="col-md-8">
			{{ Form::text("grn_no", null, array( 
			"class"=>"form-control", "placeholder"=>"--- Generate by system ---",
			"readonly"=>"readonly",
			"style"=>"text-align: center;")
			) }}
		</div>
	</div>

	<div class="row form-group">
		<div class="col-md-4">
			<label class="control-label">Supplier DO No.</label>
		</div>
		<div class="col-md-8">
			{{ Form::text("supplier_do_no",  $grn->supplier_do_no ?? null, array( 
			"class"=>"form-control", "placeholder"=>"Supplier DO No")
			) }}
		</div>
	</div>

</div>
