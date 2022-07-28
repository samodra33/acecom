<div class="col-md-6">


	<div class="row form-group">
		<div class="col col-md-4">
			<label class="control-label">PO Date <span style="color:red;">*</span></label>
		</div>
		<div class="col-12 col-md-8">
			{{ Form::text("v_po_date", isset($po->po_date)?date("m/d/Y",strtotime($po->po_date)):null, array("class"=>"form-control date", "placeholder"=>"dd/mm/yyyy", "required"=>"required")) }}
			{{ Form::hidden("po_date", isset($po->po_date)?$po->po_date:null, array("class"=>"form-control")) }}

			<span class="validation-msg" id="prdate-error"></span>
		</div>
		
	</div>

	<div class="row form-group">
		<div class="col-md-4">
			<label class="control-label">Remark</label>
		</div>
		<div class="col-md-8">
			{{ Form::textarea('po_remarks_to_supplier', $po->po_remark ?? null, array("class"=>"form-control", "placeholder"=>"Remarks", "rows"=>"5")) }}
		</div>
	</div>
	
</div>

