<div class="col-md-6">

	<div class="row form-group">
		<div class="col col-md-4">
			<label class="control-label">Grn Date <span style="color:red;">*</span></label>
		</div>
		<div class="col-12 col-md-8">
			{{ Form::text("v_grn_date", isset($grn->grn_date)?date("m/d/Y",strtotime($grn->grn_date)):null, array("class"=>"form-control date", "placeholder"=>"dd/mm/yyyy", "required"=>"required")) }}
			{{ Form::hidden("grn_date", isset($grn->grn_date)?$grn->grn_date:null, array("class"=>"form-control")) }}

			<span class="validation-msg" id="grndate-error"></span>
		</div>
		
	</div>

	<div class="row form-group">
		<div class="col-md-4">
			<label class="control-label">Remark</label>
		</div>
		<div class="col-md-8">
			{{ Form::textarea('grn_remark', $grn->grn_remark ?? null, array("class"=>"form-control", "placeholder"=>"Remarks", "rows"=>"5")) }}
		</div>
	</div>
	
</div>

