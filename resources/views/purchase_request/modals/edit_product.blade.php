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
							{{ Form::hidden("edt_pr_product_id", null) }}
							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Product<span style="color:red;">*</span></label>
								</div>
								<div class="col-md-8">
									{{ Form::select("edt_product", $product_list, null, array("class"=>"form-control select2_picker", "placeholder"=>"Select")) }}

									{{ Form::hidden("edt_product_id", null) }}
									{{ Form::hidden("edt_product_name", null) }}
									{{ Form::hidden("edt_product_sku", null) }}
									{{ Form::hidden("edt_product_upc", null) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Category</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_product_category", null, array("class"=>"form-control", "placeholder"=>"Category", "readonly"=>"true")) }}
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
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Unit</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_product_unit", null, array("class"=>"form-control", "placeholder"=>"Unit", "readonly"=>"true")) }}

									{{ Form::hidden("edt_unit_id", null) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Supplier<span style="color:red;">*</span></label>
								</div>
								<div class="col-md-8">
									{{ Form::select("edt_product_supplier", [], null, array("class"=>"form-control select2_picker", "placeholder"=>"Select")) }}

									{{ Form::hidden("edt_supplier_id", null) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">GST<span style="color:red;">*</span></label>
								</div>
								<div class="col-md-8">
									{{ Form::select("edt_product_gst", $gst_list, null, array("class"=>"form-control select2_picker", "placeholder"=>"Select")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">MOQ</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_product_moq", null, array("class"=>"form-control", "placeholder"=>"MOQ", "readonly"=>"true")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Lead Time</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_product_lead_time", null, array("class"=>"form-control", "readonly"=>"true")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Currency<span style="color:red;">*</span></label>
								</div>
								<div class="col-md-8">
									{{ Form::select("edt_product_currency", $currency_list, null, array("class"=>"form-control select2_picker", "placeholder"=>"Select", "disabled"=>"true")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Price<span style="color:red;">*</span></label>
								</div>
								<div class="col-md-8">
									{{ Form::text("edt_product_moqprice", null, array("class"=>"form-control", "placeholder"=>"Price")) }}
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

    @if(request()->route()->named("pr.create"))

    	//get Product

    	function getProduct(tableIndex, element)
    	{

    		var id = $( "#product_id".concat(tableIndex) ).val();
    		var supplierSelected = $( "#product_supplier".concat(tableIndex) ).val()

    		$('input[name="edt_product_id"]').val( $( "#product_id".concat(tableIndex) ).val() );
    		$('input[name="edt_product_name"]').val( $( "#product_name".concat(tableIndex) ).val() );
    		$('input[name="edt_product_sku"]').val( $( "#product_sku".concat(tableIndex) ).val() );
    		$('input[name="edt_product_upc"]').val( $( "#product_upc".concat(tableIndex) ).val() );
    		$('input[name="edt_product_category"]').val( $( "#product_category".concat(tableIndex) ).val() );
    		$('input[name="edt_product_brand"]').val( $( "#product_brand".concat(tableIndex) ).val() );
    		$('input[name="edt_product_qty"]').val( $( "#product_qty".concat(tableIndex) ).val() );
    		$('input[name="edt_product_unit"]').val( $( "#product_unit".concat(tableIndex) ).val() );

    		$('input[name="edt_unit_id"]').val( $( "#product_unit_id".concat(tableIndex) ).val() );

    		$('input[name="edt_supplier_id"]').val( $( "#supplier_id".concat(tableIndex) ).val() );
    		$('input[name="edt_product_moq"]').val( $( "#product_moq".concat(tableIndex) ).val() );
	        $('input[name="edt_product_lead_time"]').val( $( "#product_lead_time".concat(tableIndex) ).val() );
	       	$('input[name="edt_product_moqprice"]').val( $( "#product_moqprice".concat(tableIndex) ).val() );

    		$('select[name="edt_product"]').val( $( "#product".concat(tableIndex) ).val() ).trigger("change");

    		$('select[name="edt_product_gst"]').val( $( "#product_gst".concat(tableIndex) ).val() ).trigger("change");

    		$('select[name="edt_product_currency"]').val( $( "#product_currency".concat(tableIndex) ).val() ).trigger("change");

    		getEditSupplierList(id, supplierSelected);

    		$("#edt_save_hidden_product_id").val(tableIndex);
			$("#edt_save_product_btn_id").val($(element).parent().parent()[0]._DT_CellIndex.row);
    	}

    	//btn action

		$("#btn_save_edit_product").on("click", function(){

			var product_id = $('input[name="edt_product_id"]').val();
			var product_qty = $('input[name="edt_product_qty"]').val();
			var product_supplier = $('select[name="edt_product_supplier"]').val();
			var product_moqprice = $('input[name="edt_product_moqprice"]').val();
			var product_gst = $('select[name="edt_product_gst"]').val();
    		var product_currency = $('select[name="edt_product_currency"]').val();

    		if (product_id == '' || product_qty == '' || product_supplier == '' || product_moqprice == '' || product_gst == '') 
    		{
    			alert("Check Your Input.");

    			return false;

    		}

    		if ( isNaN(product_qty) ) {

    			alert("Qty must be number.");

    			return false;
    		}

    		if ( isNaN(product_moqprice) ) {

    			alert("Price must be number.");

    			return false;
    		}

    		updateProductTable($("#edt_save_product_btn_id").val(), $("#edt_save_hidden_product_id").val());

		})	

		//save product

		function updateProductTable(hiddenId, tableIndex)
		{
			var product = $("#product".concat(tableIndex) ).val( $("select[name='edt_product']").val() );
			var product_id = $("#product_id".concat(tableIndex) ).val( $("input[name='edt_product_id']").val() );
			var product_name = $("#product_name".concat(tableIndex) ).val( $("input[name='edt_product_id']").val() );
			var product_sku = $("#product_sku".concat(tableIndex) ).val( $("input[name='edt_product_sku']").val() );
			var product_upc = $("#product_upc".concat(tableIndex) ).val( $("input[name='edt_product_upc']").val() );
			var product_category = $("#product_category".concat(tableIndex) ).val( $("input[name='edt_product_category']").val() );
			var product_brand = $("#product_brand".concat(tableIndex) ).val( $("input[name='edt_product_brand']").val() );
			var product_qty = $("#product_qty".concat(tableIndex) ).val( $("input[name='edt_product_qty']").val() );
			var product_unit = $("#product_unit".concat(tableIndex) ).val( $("input[name='edt_product_unit']").val() );
			var product_unit_id = $("#product_unit_id".concat(tableIndex) ).val( $("input[name='edt_unit_id']").val() );
			var product_supplier = $("#product_supplier".concat(tableIndex) ).val( $("select[name='edt_product_supplier']").val() );
			var product_moq = $("#product_moq".concat(tableIndex) ).val( $("input[name='edt_product_moq']").val() );
			var product_lead_time = $("#product_lead_time".concat(tableIndex) ).val( $("input[name='edt_product_lead_time']").val() );
			var product_moqprice = $("#product_moqprice".concat(tableIndex) ).val( $("input[name='edt_product_moqprice']").val() );
			var supplier_id = $("#supplier_id".concat(tableIndex) ).val( $("input[name='edt_supplier_id']").val() );
			var product_gst = $("#product_gst".concat(tableIndex) ).val( $("select[name='edt_product_gst']").val() );
			var product_currency = $("#product_currency".concat(tableIndex) ).val( $("select[name='edt_product_currency']").val() );


			if(tableIndex!=''){

				var supplierName = $('select[name="edt_product_supplier"] option:selected').text();


				row = window.add_product_table.row(hiddenId);

				row.data([
					row.data()[0],
					product_sku.val(),
					product_upc.val(),
					product_name.val(),
					product_brand.val(),
					supplierName,
					product_qty.val(),
					product_unit.val(),
					product_moqprice.val(),
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

    @if(!request()->route()->named("pr.create"))

	//get Product

    	function getProduct(id)
    	{
    		if(id){

    			var urls = '{{route("prProd.service.find_product_id", ":param")}}';
	        	urls = urls.replace(':param', id);

	        	var ajax = getDataWithAjax(urls, 'GET');

	        	ajax.done(function(datas){
	        		//console.log(datas)

	        		$('input[name="edt_pr_product_id"]').val( datas.pr_product_id );

		    		$('input[name="edt_product_id"]').val( datas.product_id );
		    		$('input[name="edt_product_name"]').val( datas.product_name);
		    		$('input[name="edt_product_sku"]').val( datas.product_sku );
		    		$('input[name="edt_product_upc"]').val( datas.product_upc );
		    		$('input[name="edt_product_category"]').val( datas.category_name );
		    		$('input[name="edt_product_brand"]').val( datas.brand_name );
		    		$('input[name="edt_product_qty"]').val( datas.product_qty );
		    		$('input[name="edt_product_unit"]').val( datas.prchsunit );

		    		$('input[name="edt_unit_id"]').val( datas.prchsunitId );

		    		$('input[name="edt_supplier_id"]').val( datas.supplier_id );
		    		$('input[name="edt_product_moq"]').val( datas.supplier_moq );
			        $('input[name="edt_product_lead_time"]').val( datas.lead_time );
			       	$('input[name="edt_product_moqprice"]').val(datas.product_price  );

		    		$('select[name="edt_product"]').val( datas.product_id ).trigger("change");
		    		$('select[name="edt_product_gst"]').val( datas.product_gst ).trigger("change");
					$('select[name="edt_product_currency"]').val( datas.product_currency ).trigger("change");

		    		getEditSupplierList(datas.product_id, datas.product_supplier_id);
		        })
		        ajax.fail(function(error){
		            alert("Something Wrong (product).!")
		        })

    		}
    		else{

	        	$('input[name="edt_product_id"]').val('');
	        	$('input[name="edt_product_name"]').val('');
	        	$('input[name="edt_product_sku"]').val('');
	        	$('input[name="edt_product_upc"]').val('');
	        	$('input[name="edt_product_category"]').val('');
	        	$('input[name="edt_product_brand"]').val('');
	        	$('input[name="edt_product_unit"]').val('');
				$('input[name="edt_product_moq"]').val('');
	        	$('input[name="edt_product_lead_time"]').val('');
	        	$('input[name="edt_product_moqprice"]').val('');
	        	$('input[name="edt_supplier_id"]').val('');
	        	$('input[name="edt_unit_id"]').val('');
	        	$('select[name="edt_product_supplier"]').val('').trigger("change");
	        	$('select[name="edt_product_gst"]').val('').trigger("change");
				$('select[name="edt_product_currency"]').val('').trigger("change");

    			alert("Something Wrong");
    		}
    	}


    	//btn action

		$("#btn_save_edit_product").on("click", function(){

			var pr_product_id = $('input[name="edt_pr_product_id"]').val();
			var pr_id = $('input[name="pr_id"]').val();

    		//product
    		var product = $('select[name="edt_product"]').val();
    		var product_id = $('input[name="edt_product_id"]').val();
	        var product_name = $('input[name="edt_product_name"]').val();
	        var product_sku = $('input[name="edt_product_sku"]').val();
	        var product_upc = $('input[name="edt_product_upc"]').val();
    		var product_category = $('input[name="edt_product_category"]').val();
    		var product_brand = $('input[name="edt_product_brand"]').val();
    		var product_qty = $('input[name="edt_product_qty"]').val();
    		var product_unit = $('input[name="edt_product_unit"]').val();
    		var product_unit_id = $('input[name="edt_unit_id"]').val();

    		var product_supplier = $('select[name="edt_product_supplier"]').val();
    		var supplier_id = $('input[name="edt_supplier_id"]').val();
    		var product_moq = $('input[name="edt_product_moq"]').val();
    		var product_lead_time = $('input[name="edt_product_lead_time"]').val();
    		var product_moqprice = $('input[name="edt_product_moqprice"]').val();
    		var product_gst = $('select[name="edt_product_gst"]').val();
    		var product_currency = $('select[name="edt_product_currency"]').val();


    		if (pr_product_id) {

    			var urls = '{{route("prProd.service/store_pr_product")}}';

	       		if (product_id == '' || product_qty == '' || product_supplier == '' || product_moqprice == '' || product_gst == '') 
	       		{
	       			alert("Check Your Input.");

	       			return false;

	       		}

	       		if ( isNaN(product_qty) ) {

	       			alert("Qty must be number.");

	       			return false;
	       		}

	       		if ( isNaN(product_moqprice) ) {

	       			alert("Price must be number.");

	       			return false;
	       		}

	       		//set Data

    			var data = {
    						"func_type" : "update",
    						"pr_product_id": pr_product_id,
	            			"pr_id" : pr_id,
	            			"product_gst" : product_gst,
	            			"product_currency" : product_currency,
	            			"product_id" : product_id,
	            			"supplier_id" : supplier_id,
	            			"supplier_moq_id" : product_supplier,
	            			"product_qty" : product_qty,
	            			"product_purchase_unit" : product_unit_id,
	            			"product_price" : product_moqprice

	       		};

	       		//ajax


	       		var ajax = getDataWithAjax(urls, 'POST', data);

		        ajax.done(function(response){
		        	alert(response);
		        	var table = $('#pr-product-table').DataTable();
					table.ajax.reload();
		        })
		        ajax.fail(function(error){
		            alert("Something Wrong (controller).!")
		        })

    		}else{
    			alert("Something Wrong (pr_id input)")
    		}

		})	

    @endif

    //se;ect Product Detail

    $('select[name="edt_product"]').on('select2:close', function() {

        var id = $(this).val();

        if(id){

	        var urls = '{{route("mProduct.service.find_product", ":param")}}';
	        urls = urls.replace(':param', id);

	        var data = {
	            "product_id" : id
	        }

	        var ajax = getDataWithAjax(urls, 'GET', data);

	        ajax.done(function(data){
	        	$('input[name="edt_product_id"]').val(data.product_id);
	        	$('input[name="edt_product_name"]').val(data.product_name);
	        	$('input[name="edt_product_sku"]').val(data.product_sku);
	        	$('input[name="edt_product_upc"]').val(data.product_upc);
	        	$('input[name="edt_product_category"]').val(data.Category);
	        	$('input[name="edt_product_brand"]').val(data.Brand);
	        	$('input[name="edt_product_unit"]').val(data.prchsunit);
	        	$('input[name="edt_unit_id"]').val(data.prchsunitId);

				$('input[name="edt_product_moq"]').val('');
	        	$('input[name="edt_product_lead_time"]').val('');
	        	$('input[name="edt_product_moqprice"]').val('');
	        	$('input[name="edt_supplier_id"]').val('');
	        	$('select[name="edt_product_gst"]').val('').trigger("change");
				$('select[name="edt_product_currency"]').val('').trigger("change");

	        	getEditSupplierList(data.product_id);
	        })
	        ajax.fail(function(error){
	            alert("Something Wrong (product).!")
	        })

    	}else{

	        	$('input[name="edt_product_id"]').val('');
	        	$('input[name="edt_product_name"]').val('');
	        	$('input[name="edt_product_sku"]').val('');
	        	$('input[name="edt_product_upc"]').val('');
	        	$('input[name="edt_product_category"]').val('');
	        	$('input[name="edt_product_brand"]').val('');
	        	$('input[name="edt_product_unit"]').val('');
				$('input[name="edt_product_moq"]').val('');
	        	$('input[name="edt_product_lead_time"]').val('');
	        	$('input[name="edt_product_moqprice"]').val('');
	        	$('input[name="edt_supplier_id"]').val('');
	        	$('input[name="edt_unit_id"]').val('');
	        	$('select[name="edt_product_supplier"]').val('').trigger("change");
	        	$('select[name="edt_product_gst"]').val('').trigger("change");
				$('select[name="edt_product_currency"]').val('').trigger("change");
    	}
    });

    //get Supplier List

    function getEditSupplierList(id, selected)
    {

    	var urls = '{{route("mProduct.service.find_supplier_by_product", ":param")}}';
        urls = urls.replace(':param', id);

        var data = {
            "product_id" : id
        }

        var ajax = getDataWithAjax(urls, 'GET', data);

        ajax.done(function(datas){

        	var element = $("select[name='edt_product_supplier']");
        	setSelectOption(element, selected, datas);

        })
        ajax.fail(function(error){
            alert("Something Wrong (supplier).!")
        })
    }

    //supplier list get detail
    $('select[name="edt_product_supplier"]').on('select2:close', function() {

    	var id = $(this).val();

    	if (id) {

	        var urls = '{{route("mProduct.service.find_supplier", ":param")}}';
	        urls = urls.replace(':param', id);

	        var data = {
	            "product_supplier_id" : id
	        }

	        var ajax = getDataWithAjax(urls, 'GET', data);

	        ajax.done(function(data){
	        	$('input[name="edt_supplier_id"]').val(data.supplier_id);
	        	$('input[name="edt_product_moq"]').val(data.supplier_moq);
	        	$('input[name="edt_product_lead_time"]').val(data.lead_time);
	        	$('input[name="edt_product_moqprice"]').val(data.supplier_price);

	        	$('select[name="edt_product_gst"]').val(data.gst_id).trigger("change");
	        	$('select[name="edt_product_currency"]').val(data.currency_id).trigger("change");
	        })
	        ajax.fail(function(error){
	            alert("Something Wrong (product).!")
	        })

    	}else{
    			$('input[name="edt_supplier_id"]').val('');
    			$('input[name="edt_product_moq"]').val('');
	        	$('input[name="edt_product_lead_time"]').val('');
	        	$('input[name="edt_product_moqprice"]').val('');
	        	$('select[name="edt_product_gst"]').val('').trigger("change");
				$('select[name="edt_product_currency"]').val('').trigger("change");

    	}
    });

</script>

@endpush