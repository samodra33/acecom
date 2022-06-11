<div class="modal fade" id="edit_sku" tabindex="-1" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="scrollmodalLabel">Edit SKU</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="card">
					<div class="card-header">
						<strong class="card-title">SKU</strong>
					</div>
					<div class="card-body">

						<div class="col-md-12">

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">SKU</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_sku", null, array("class"=>"form-control", "placeholder"=>"SKU")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Description</label>
								</div>
								<div class="col-md-8">
									{{ Form::textarea("edt_desc", null, array("class"=>"form-control", "placeholder"=>"Description")) }}
								</div>
							</div>

						</div>

						<div class="mt-3 col-md-12">

							<input type="hidden" id="edt_save_hidden_sku_id" value="">
							<input type="hidden" id="edt_save_btn_id" value="">
							<button type="button" class="btn btn-primary" id="btn_save_edit_sku">
								Save
							</button>

						</div>
					</div>
				</div>
				<div class="form-group">

				</div>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script type="text/javascript">

	@if(request()->route()->named("mProduct.create"))

	function getSku(tableIndex, element)
	{
		$("input[name='edt_sku']").val( $( "#sku".concat(tableIndex) ).val())
		$("textarea[name='edt_desc']").val( $( "#desc".concat(tableIndex) ).val())
		$("#edt_save_hidden_sku_id").val(tableIndex);
		$("#edt_save_btn_id").val($(element).parent().parent()[0]._DT_CellIndex.row);
	}

	$("#btn_save_edit_sku").on("click", function(){

		var sku = $("input[name='edt_sku']").val();
		var desc = $("textarea[name='edt_desc']").val();

		if (sku == '' ) {
			alert("Check your input.");
		}else{
			updateSkuTable($("#edt_save_btn_id").val(), $("#edt_save_hidden_sku_id").val());
		}

	})	

	function updateSkuTable(hiddenSku, tableIndexButton)
	{
		var sku = $("#sku".concat(tableIndexButton) ).val( $("input[name='edt_sku']").val() );
		var desc = $("#desc".concat(tableIndexButton) ).val( $("textarea[name='edt_desc']").val() );

		if(tableIndexButton!=''){

			row = window.product_sku.row(hiddenSku);
			row.data([
				row.data()[0],
				sku.val(),
				desc.val(),
				]);

			window.product_sku.draw();
			alert("Update successfully.");
		}else{
			alert("Something wrong.");
		}
	}

	function removeSku(tableIndex, element)
	{
		var tt = $(element).parent().parent()[0]._DT_CellIndex.row;
        var row = window.product_sku.row(tt).remove().draw();

		$("#tableIndex".concat(tableIndex) ).remove();
		$("#sku".concat(tableIndex) ).remove();
		$("#desc".concat(tableIndex) ).remove();
	}

	@endif

</script>



@endpush