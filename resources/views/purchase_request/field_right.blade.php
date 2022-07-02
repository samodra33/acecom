<div class="col-md-6">

	<div class="row form-group">
		<div class="col col-md-4">
			<label class="control-label">PR Date <span style="color:red;">*</span></label>
		</div>
		<div class="col-12 col-md-8">
			{{ Form::text("v_pr_date", isset($pr->pr_date)?date("m/d/Y",strtotime($pr->pr_date)):null, array("class"=>"form-control date", "placeholder"=>"dd/mm/yyyy", "required"=>"required")) }}
			{{ Form::hidden("pr_date", isset($pr->pr_date)?$pr->pr_date:null, array("class"=>"form-control")) }}
		</div>
	</div>

	<div class="row form-group">
		<div class="col-md-4">
			<label class="control-label">Remark to Supplier</label>
		</div>
		<div class="col-md-8">
			{{ Form::textarea('pr_remarks_to_supplier', $pr->pr_remarks_supplier ?? null, array("class"=>"form-control", "placeholder"=>"Remarks to Supplier", "rows"=>"5")) }}
		</div>
	</div>
	
</div>

