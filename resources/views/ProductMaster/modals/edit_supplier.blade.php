<div class="modal fade" id="edit_supplier" tabindex="-1" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="scrollmodalLabel">Edit Supplier</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="card">
					<div class="card-header">
						<strong class="card-title">Supplier</strong>
					</div>
					<div class="card-body">

					<div class="card-body">

						<div class="col-md-12">
							{{ Form::hidden("edt_hidden_product_supplier_id", null) }}
							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">MOQ</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_moq", null, array("class"=>"form-control", "placeholder"=>"MOQ")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Supplier</label>
								</div>
								<div class="col-md-8">
									{{ Form::select("edt_supplier", $supplier_lists, null, array("class"=>"form-control selectpicker", "title"=>"Select", "data-live-search"=>"true", "data-live-search-style"=>"begins")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Lead Time</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_lead_time", null, array("class"=>"form-control", "readonly"=>"true")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Price</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_moqprice", null, array("class"=>"form-control", "placeholder"=>"Price")) }}
								</div>
							</div>

						</div>

						<div class="mt-3 col-md-12">

							<input type="hidden" id="edt_save_hidden_moq_id" value="">
							<input type="hidden" id="edt_save_moq_btn_id" value="">
							<button type="button" class="btn btn-primary" id="btn_save_edit_moq">
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

	function getSupplier(tableMoqIndex, element)
	{
		$("input[name='edt_moq']").val( $( "#moq".concat(tableMoqIndex) ).val())
		$("select[name='edt_supplier']").val( $( "#supplier".concat(tableMoqIndex) ).val())
		$('.selectpicker').selectpicker('refresh');
		$("input[name='edt_lead_time']").val( $( "#leadtime".concat(tableMoqIndex) ).val())
		$("input[name='edt_moqprice']").val( $( "#moqprice".concat(tableMoqIndex) ).val())

		$("#edt_save_hidden_moq_id").val(tableMoqIndex);
		$("#edt_save_moq_btn_id").val($(element).parent().parent()[0]._DT_CellIndex.row);
	}

	$("#btn_save_edit_moq").on("click", function(){

		var moq = $("input[name='edt_moq']").val();
		var supplier = $("select[name='edt_supplier']").val();
		var moqprice = $("input[name='edt_moqprice']").val();

		if ( isNaN(moq) || supplier == '' || isNaN(moqprice) ) {
			alert("Check your input.");
		}else{
			updateMoqTable($("#edt_save_moq_btn_id").val(), $("#edt_save_hidden_moq_id").val());
		}

	})	

	function updateMoqTable(hiddenSku, tableMoqIndexButton)
	{
		var moq = $("#moq".concat(tableMoqIndexButton) ).val( $("input[name='edt_moq']").val() );
		var supplier = $("#supplier".concat(tableMoqIndexButton) ).val( $("select[name='edt_supplier']").val() );
		var moqprice = $("#moqprice".concat(tableMoqIndexButton) ).val( $("input[name='edt_moqprice']").val() );

		if(tableMoqIndexButton!=''){

			var supplierText = $("select[name='edt_supplier']").find('option:selected').map(function() {
			    return $(this).text();
			  }).get().join(',');


			row = window.product_supplier.row(hiddenSku);
			row.data([
				row.data()[0],
				moq.val(),
				supplierText,
				moqprice.val(),
				]);

			window.product_supplier.draw();
			alert("Update successfully.");
		}else{
			alert("Something wrong.");
		}
	}

	function removeSupplier(tableMoqIndex, element)
	{
		var tt = $(element).parent().parent()[0]._DT_CellIndex.row;
        var row = window.product_supplier.row(tt).remove().draw();

		$("#tableMoqIndex".concat(tableMoqIndex) ).remove();
		$("#moq".concat(tableMoqIndex) ).remove();
		$("#supplier".concat(tableMoqIndex) ).remove();
		$("#moqprice".concat(tableMoqIndex) ).remove();
		$("#leadtime".concat(tableMoqIndex) ).remove();
	}

	@endif

	@if(request()->route()->named("mProduct.edit"))

	function getSupplierDetail(id)
    {

    	var urls = '{{route("mProduct.service.find_supplier", ":param")}}';
        urls = urls.replace(':param', id);

        var data = {
        	"product_supplier_id" : id
        }

            $.ajax({

                type:'GET',
                url:urls,
                data: data,

	            success:function(response){

	            	$("input[name='edt_hidden_product_supplier_id']").val(response.product_supplier_id);
	            	$("input[name='edt_moq']").val(response.supplier_moq)
					$("select[name='edt_supplier']").val(response.supplier_id)
					$('.selectpicker').selectpicker('refresh');
					$("input[name='edt_moqprice']").val(response.supplier_price)
					$("input[name='edt_lead_time']").val(response.lead_time)

	            },
	            error:function(response) {

	            	alert(response)

	            }
            });
    }

    $("button[id='btn_save_edit_moq']").click( function(e){

    	var url = "{{ route('mProduct.service.edit_prod_supplier',":param")}}";
            url = url.replace(':param', $("input[name='edt_hidden_product_supplier_id']").val());

            var moq = $("input[name='edt_moq']").val();
            var supplier = $("select[name='edt_supplier']").val();
            var moqprice = $("input[name='edt_moqprice']").val();

            if ( isNaN(moq) || supplier == '' || isNaN(moqprice) ) {
            	alert("Check your input.");

            	return false;
            }

            var data = {
            	"edt_hidden_product_supplier_id"   : $("input[name='edt_hidden_product_supplier_id']").val(),
            	"moq"          : moq,
            	"supplier"     : supplier,
            	"moqprice"		: moqprice
            }

            $.ajax({

                type:'PATCH',
                url:url,
                data: data,

	            success:function(response){

					if(response.status == 200){
	                    console.log(response.responseJSON);
	                }else{
	                    alert(response);
	                }

	            	var table = $('#product_supplier').DataTable();
	                table.ajax.reload();

	            },
	            error:function(response) {

	            	alert(response)

	            }
            });
    });


	@endif

</script>



@endpush