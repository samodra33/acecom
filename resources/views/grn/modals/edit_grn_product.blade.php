<div class="modal fade" id="edit_product" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="scrollmodalLabel">Edit Product</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="card">
					<div class="card-header">
						<strong class="card-title">Product</strong>
					</div>
					<div class="card-body">

						<div class="col-md-12">
							{{ Form::hidden("edt_grn_product_id", null) }}
							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Product SKU</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_product_sku", null, array("class"=>"form-control", "placeholder"=>"Product Name", "readonly"=>"true")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Product Name</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_product_name", null, array("class"=>"form-control", "placeholder"=>"Product Name", "readonly"=>"true")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Product UPC</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_product_upc", null, array("class"=>"form-control", "placeholder"=>"Product UPC", "readonly"=>"true")) }}
								</div>
							</div>


							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Brand</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_product_brand", null, array("class"=>"form-control", "placeholder"=>"Brand", "readonly"=>"true")) }}
								</div>
							</div>


							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Qty<span style="color:red;">*</span></label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_product_qty", null, array("class"=>"form-control", "placeholder"=>"Qty")) }}

									{{ Form::hidden("edt_product_unreceived_qty", null, array("class"=>"form-control", "placeholder"=>"edt_product_unreceived_qty")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Unit</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_product_unit", null, array("class"=>"form-control", "placeholder"=>"Unit", "readonly"=>"true")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Supplier</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_product_supplier", null, array("class"=>"form-control", "placeholder"=>"Supplier", "readonly"=>"true")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Price</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_product_price", null, array("class"=>"form-control", "placeholder"=>"Price", "readonly"=>"true")) }}
								</div>
							</div>

						</div>

						<div class="mt-3 col-md-12">

							<input type="hidden" id="edt_save_hidden_product_id" value="">
							<input type="hidden" id="edt_save_product_btn_id" value="">
							<button type="button" class="btn btn-primary" id="btn_save_edit_product">
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

    $(document).ready(function(){
    	     
    });

    @if(request()->route()->named("grn.create"))

    	//get Product

    	function getProduct(tableIndex, element)
    	{

    		$('input[name="edt_product_sku"]').val( $( "#product_sku".concat(tableIndex) ).val() );
    		$('input[name="edt_product_name"]').val( $( "#product_name".concat(tableIndex) ).val() );
    		$('input[name="edt_product_upc"]').val( $( "#product_upc".concat(tableIndex) ).val() );
    		$('input[name="edt_product_brand"]').val( $( "#brand_name".concat(tableIndex) ).val() );
    		$('input[name="edt_product_qty"]').val( $( "#product_qty".concat(tableIndex) ).val() );
    		$('input[name="edt_product_unit"]').val( $( "#prchsunit".concat(tableIndex) ).val() );
    		$('input[name="edt_product_supplier"]').val( $( "#supplier_name".concat(tableIndex) ).val() );
    		$('input[name="edt_product_price"]').val( $( "#product_price".concat(tableIndex) ).val() );

    		$('input[name="edt_product_unreceived_qty"]').val( $( "#unreceived_qty".concat(tableIndex) ).val() );


    		$("#edt_save_hidden_product_id").val(tableIndex);
			$("#edt_save_product_btn_id").val($(element).parent().parent()[0]._DT_CellIndex.row);
    	}

    	//btn action

		$("#btn_save_edit_product").on("click", function(){

			var product_qty = $('input[name="edt_product_qty"]').val();
			var product_unreceived_qty = $('input[name="edt_product_unreceived_qty"]').val();


    		if (product_qty == '') 
    		{
    			alert("Check Your Input.");

    			return false;

    		}

    		if ( isNaN(product_qty) ) {

    			alert("Qty must be number.");

    			return false;
    		}

    		if ( parseInt(product_qty) > parseInt(product_unreceived_qty)) {

    			alert("the qty cannot be bigger than unreceived qty");

    			$('input[name="edt_product_qty"]').val(product_unreceived_qty);

    			return false;
    		}


    		updateProductTable($("#edt_save_product_btn_id").val(), $("#edt_save_hidden_product_id").val());


		})	

		//save product

		function updateProductTable(hiddenId, tableIndex)
		{

			var product_qty = $("#product_qty".concat(tableIndex) ).val( $("input[name='edt_product_qty']").val() );


			if(tableIndex!=''){

				row = window.add_product_table.row(hiddenId);

				row.data([
					row.data()[0],
					row.data()[1],
					row.data()[2],
					row.data()[3],
					row.data()[4],
					row.data()[5],
					row.data()[6],
					product_qty.val(),
					row.data()[8],
					row.data()[9]
					]);

				window.add_product_table.draw();
				alert("Update successfully.");
			}else{
				alert("Something wrong.");
			}
		}

		//delete record

		function removeProduct(tableIndex, element)
		{
			var tt = $(element).parent().parent()[0]._DT_CellIndex.row;
	        var row = window.add_product_table.row(tt).remove().draw();

			$("#tableIndex".concat(tableIndex) ).remove();
			$("#product".concat(tableIndex) ).remove();
			$("#product_id".concat(tableIndex) ).remove();
			$("#product_name".concat(tableIndex) ).remove();
			$("#product_sku".concat(tableIndex) ).remove();
			$("#product_upc".concat(tableIndex) ).remove();
			$("#product_category".concat(tableIndex) ).remove();
			$("#product_brand".concat(tableIndex) ).remove();
			$("#product_qty".concat(tableIndex) ).remove();
			$("#product_unit".concat(tableIndex) ).remove();
			$("#product_supplier".concat(tableIndex) ).remove();
			$("#product_moq".concat(tableIndex) ).remove();
			$("#product_lead_time".concat(tableIndex) ).remove();
			$("#product_moqprice".concat(tableIndex) ).remove();
			$("#supplier_id".concat(tableIndex) ).remove();
			$("#product_unit_id".concat(tableIndex) ).remove();
			$("#product_gst".concat(tableIndex) ).remove();
			$("#product_currency".concat(tableIndex) ).remove();
		}

    @endif

    @if(!request()->route()->named("grn.create"))

    	function getProduct(id)
    	{

    		if(id){

    			var urls = '{{route("grnProd.service.find_product_id", ":param")}}';
	        	urls = urls.replace(':param', id);

	        	var ajax = getDataWithAjax(urls, 'GET');

	        	ajax.done(function(datas){

	        		//console.log(datas);

	        		$('input[name="edt_grn_product_id"]').val( datas.grn_product_id );

		    		$('input[name="edt_product_sku"]').val( datas.product_sku );
		    		$('input[name="edt_product_name"]').val( datas.product_name );
		    		$('input[name="edt_product_upc"]').val( datas.product_upc );
		    		$('input[name="edt_product_brand"]').val( datas.brand_name );
		    		$('input[name="edt_product_qty"]').val( datas.product_qty );
		    		$('input[name="edt_product_unit"]').val( datas.prchsunit );
		    		$('input[name="edt_product_supplier"]').val( datas.supplier_name );
		    		$('input[name="edt_product_price"]').val( datas.product_price );


		        })
		        ajax.fail(function(error){

	        		$('input[name="edt_grn_product_id"]').val("");

		    		$('input[name="edt_product_sku"]').val("");
		    		$('input[name="edt_product_name"]').val("");
		    		$('input[name="edt_product_upc"]').val("");
		    		$('input[name="edt_product_brand"]').val("");
		    		$('input[name="edt_product_qty"]').val("");
		    		$('input[name="edt_product_unit"]').val("");
		    		$('input[name="edt_product_supplier"]').val("");
		    		$('input[name="edt_product_price"]').val("");

		            alert("Something Wrong (product).!")
		        })

    		} else{

    		}

    	}

    	$("#btn_save_edit_product").on("click", function(){

    		var grn_product_id = $('input[name="edt_grn_product_id"]').val();

    		var product_qty = $('input[name="edt_product_qty"]').val();

    		if (grn_product_id != '') {

    			var urls = '{{route("grnProd.service/update_grn_product")}}';

	    		if (product_qty == '') 
	    		{
	    			alert("Check Your Input.");

	    			return false;

	    		}

	    		if ( isNaN(product_qty) ) {

	    			alert("Qty must be number.");

	    			return false;
	    		}

	       		//set Data

    			var data = {
    						"grn_product_id": grn_product_id,
	            			"product_qty" : product_qty

	       		};

	       		//ajax

	       		var ajax = getDataWithAjax(urls, 'POST', data);

		        ajax.done(function(response){
		        	alert(response);
		        	var table = $('#grn-product-table').DataTable();
					table.ajax.reload();

					var table2 = $('#add_product_table').DataTable();
            		table2.ajax.reload();
		        })
		        ajax.fail(function(error){
		            alert("Something Wrong (controller).!")
		        })


    		}else{
    			alert("Something Wrong (grn_id input)")
    		}

    	})	

    @endif

</script>

@endpush