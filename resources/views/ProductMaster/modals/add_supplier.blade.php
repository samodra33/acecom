<div class="modal fade" id="add_supplier" tabindex="-1" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="scrollmodalLabel">Add Supplier</h5>
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

						<div class="col-md-12">

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">MOQ</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("moq", null, array("class"=>"form-control", "placeholder"=>"MOQ")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Supplier</label>
								</div>
								<div class="col-md-8">
									{{ Form::select("supplier", $supplier_lists, null, array("class"=>"form-control selectpicker", "title"=>"Select", "data-live-search"=>"true", "data-live-search-style"=>"begins")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Lead Time</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("lead_time", null, array("class"=>"form-control", "readonly"=>"true")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Price</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("moqprice", null, array("class"=>"form-control", "placeholder"=>"Price")) }}
								</div>
							</div>

						</div>

						<div class="mt-3 col-md-12">

							<button type="button" class="btn btn-primary" id="add_supplier_button">Add Supplier</button>

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

	window.tableMoqIndex = 0;

    $('select[name="supplier"]').on('change', function() {

        var id = $(this).val();
        var urls = '{{ route('supplier.getDetail',':param') }}';
        urls = urls.replace(':param', id);

        $.ajax({
            url: urls,
            type: "GET",
            dataType: "json",
            success:function(data) {

            	$("input[name='lead_time']").val(data.lead_time);
            },
        });
    });

	@if(request()->route()->named("mProduct.create"))

	$("#add_supplier_button").on("click", function(e){

		var moq = $("input[name='moq']").val();
		var supplier = $("select[name='supplier']").val();
		var moqprice = $("input[name='moqprice']").val();
		var leadTime = $("input[name='lead_time']").val();


		if ( isNaN(moq) || supplier == '' || isNaN(moqprice) ) {

			alert("Check your input.");

		}else{

			var action = ""+
			'<div class="btn-group">'+
			'<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#edit_supplier" onclick="getSupplier('+tableMoqIndex+', this)">'+
			'<i class="fa fa-pencil fa-fw"></i>'+
			'</button>'+
			'<button type="button" class="btn btn-danger btn-sm" onclick="removeSupplier('+tableMoqIndex+', this)">'+
			'<i class="fa fa-trash fa-fw"></i>'+
			'</button>'+
			'</div>'+
			"";

			var supplierText = $("select[name='supplier']").find('option:selected').map(function() {
			    return $(this).text();
			  }).get().join(',');

			product_supplier.row.add([
				action,
				moq,
				supplierText,
				moqprice
				]);

			product_supplier.draw()
			$("#add_product_supplier_table_hidden").append(
				'<input type="hidden" id="tableMoqIndex'+tableMoqIndex+'" name="tableMoqIndex[]" value="'+tableMoqIndex+'">'+
				'<input type="hidden" id="moq'+tableMoqIndex+'" name="moq[]" value="'+moq+'">'+
				'<input type="hidden" id="supplier'+tableMoqIndex+'" name="supplier[]" value="'+supplier+'">'+
				'<input type="hidden" id="leadtime'+tableMoqIndex+'" name="leadtime[]" value="'+leadTime+'">'+
				'<input type="hidden" id="moqprice'+tableMoqIndex+'" name="moqprice[]" value="'+moqprice+'">'
			);
			tableMoqIndex++;

			alert("Added.");
			$("input[name='moq']").val("");
			$("input[name='moqprice']").val("");
			$("input[name='lead_time']").val("");

			$('select[name=supplier]').val("");
            $('.selectpicker').selectpicker('refresh');
		}
	})

	@endif

	@if(request()->route()->named("mProduct.edit"))

	$("#add_supplier_button").on("click", function(e){

		var moq = $("input[name='moq']").val();
		var supplier = $("select[name='supplier']").val();
		var moqprice = $("input[name='moqprice']").val();


		if ( isNaN(moq) || supplier == '' || isNaN(moqprice) ) {

			alert("Check your input.");

			return false;

		}else{

			var urls = '{{ route("mProduct.service/add_supplier") }}';
       		var product_id = $("input[name='product_id']").val();

            var data = {
                "product_id" : product_id,
                "moq" : moq,
                "supplier" : supplier,
                "moqprice" : moqprice
            }

            $.ajax({

                type:'POST',
                url:urls,
                data: data,

	            success:function(response){

	            	alert(response)

	            	var table = $('#product_supplier').DataTable();
	                table.ajax.reload();
	                
					$("input[name='moq']").val("");
					$("input[name='moqprice']").val("");

					$('select[name=supplier]').val("");
		            $('.selectpicker').selectpicker('refresh');
	            },
	            error:function(response) {

	            	alert(response)

	            }
            });

		}
	})

	@endif

</script>

@endpush