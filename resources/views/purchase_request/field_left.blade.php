<div class="col-md-6">
	<div class="row form-group">
		<div class="col-md-4">
			<label class="control-label">PR No.</label>
		</div>
		<div class="col-md-8">
			{{ Form::text("pr_no", null, array( 
			"class"=>"form-control", "placeholder"=>"--- Generate by system ---",
			"readonly"=>"readonly",
			"style"=>"text-align: center;")
			) }}
		</div>
	</div>

	<div class="row form-group">
		<div class="col-md-4">
			<label class="control-label">Remark</label>
		</div>
		<div class="col-md-8">
			{{ Form::textarea('pr_remarks', $pr->pr_remarks ?? null, array("class"=>"form-control", "placeholder"=>"Remarks", "rows"=>"5")) }}
		</div>
	</div>

</div>
