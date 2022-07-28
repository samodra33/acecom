<div class="modal fade" id="add_product" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="scrollmodalLabel">Add Product</h5>
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

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Product<span style="color:red;">*</span></label>
								</div>
								<div class="col-md-8">
									{{ Form::select("product", $product_list, null, array("class"=>"form-control select2_picker", "placeholder"=>"Select")) }}

									{{ Form::hidden("product_id", null) }}
									{{ Form::hidden("product_name", null) }}
									{{ Form::hidden("product_sku", null) }}
									{{ Form::hidden("product_upc", null) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Category</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("product_category", null, array("class"=>"form-control", "placeholder"=>"Category", "readonly"=>"true")) }}
								</div>
							</div>


							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Brand</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("product_brand", null, array("class"=>"form-control", "placeholder"=>"Brand", "readonly"=>"true")) }}
								</div>
							</div>


							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Qty<span style="color:red;">*</span></label>
								</div>
								<div class="col-md-8">
									{{ Form::text("product_qty", null, array("class"=>"form-control", "placeholder"=>"Qty")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Unit</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("product_unit", null, array("class"=>"form-control", "placeholder"=>"Unit", "readonly"=>"true")) }}

									{{ Form::hidden("unit_id", null) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Supplier<span style="color:red;">*</span></label>
								</div>
								<div class="col-md-8">
									{{ Form::select("product_supplier", [], null, array("class"=>"form-control select2_picker", "placeholder"=>"Select")) }}

									{{ Form::hidden("supplier_id", null) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">GST<span style="color:red;">*</span></label>
								</div>
								<div class="col-md-8">
									{{ Form::select("product_gst", $gst_list, null, array("class"=>"form-control select2_picker", "placeholder"=>"Select")) }}
								</div>
							</div>


							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">MOQ</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("product_moq", null, array("class"=>"form-control", "placeholder"=>"MOQ", "readonly"=>"true")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Lead Time</label>
								</div>
								<div class="col-md-8">
									{{ Form::text("product_lead_time", null, array("class"=>"form-control", "readonly"=>"true")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Currency<span style="color:red;">*</span></label>
								</div>
								<div class="col-md-8">
									{{ Form::select("product_currency", $currency_list, null, array("class"=>"form-control select2_picker", "placeholder"=>"Select", "disabled"=>"true")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Price<span style="color:red;">*</span></label>
								</div>
								<div class="col-md-8">
									{{ Form::text("product_moqprice", null, array("class"=>"form-control", "placeholder"=>"Price")) }}
								</div>
							</div>

						</div>

						<div class="mt-3 col-md-12">

							<button type="button" class="btn btn-primary" id="add_product_button">Add Product</button>

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

    //se;ect Product Detail

    $('select[name="product"]').on('change', function() {

        var id = $(this).val();

        if(id){

	        var urls = '{{route("mProduct.service.find_product", ":param")}}';
	        urls = urls.replace(':param', id);

	        var data = {
	            "product_id" : id
	        }

	        var ajax = getDataWithAjax(urls, 'GET', data);

	        ajax.done(function(data){
	        	$('input[name="product_id"]').val(data.product_id);
	        	$('input[name="product_name"]').val(data.product_name);
	        	$('input[name="product_sku"]').val(data.product_sku);
	        	$('input[name="product_upc"]').val(data.product_upc);
	        	$('input[name="product_category"]').val(data.Category);
	        	$('input[name="product_brand"]').val(data.Brand);
	        	$('input[name="product_unit"]').val(data.prchsunit);
	        	$('input[name="unit_id"]').val(data.prchsunitId);

				$('input[name="product_moq"]').val('');
	        	$('input[name="product_lead_time"]').val('');
	        	$('input[name="product_moqprice"]').val('');
	        	$('input[name="supplier_id"]').val('');
	        	$('select[name="product_gst"]').val('').trigger("change");
				$('select[name="product_currency"]').val('').trigger("change");

	        	getSupplierList(data.product_id);
	        })
	        ajax.fail(function(error){
	            alert("Something Wrong (product).!")
	        })

    	}else{

	        	$('input[name="product_id"]').val('');
	        	$('input[name="product_name"]').val('');
	        	$('input[name="product_sku"]').val('');
	        	$('input[name="product_upc"]').val('');
	        	$('input[name="product_category"]').val('');
	        	$('input[name="product_brand"]').val('');
	        	$('input[name="product_unit"]').val('');
				$('input[name="product_moq"]').val('');
	        	$('input[name="product_lead_time"]').val('');
	        	$('input[name="product_moqprice"]').val('');
	        	$('input[name="supplier_id"]').val('');
	        	$('input[name="unit_id"]').val('');
	        	$('select[name="product_supplier"]').val('').trigger("change");
	        	$('select[name="product_gst"]').val('').trigger("change");
				$('select[name="product_currency"]').val('').trigger("change");
    	}
    });

    //get Supplier List

    function getSupplierList(id)
    {

    	var urls = '{{route("mProduct.service.find_supplier_by_product", ":param")}}';
        urls = urls.replace(':param', id);

        var data = {
            "product_id" : id
        }

        var ajax = getDataWithAjax(urls, 'GET', data);

        ajax.done(function(datas){

        	var element = $("select[name='product_supplier']");
        	setSelectOption(element, null, datas);

        })
        ajax.fail(function(error){
            alert("Something Wrong (supplier).!")
        })
    }

    //supplier list get detail
    $('select[name="product_supplier"]').on('change', function() {

    	var id = $(this).val();

    	if (id) {

	        var urls = '{{route("mProduct.service.find_supplier", ":param")}}';
	        urls = urls.replace(':param', id);

	        var data = {
	            "product_supplier_id" : id
	        }

	        var ajax = getDataWithAjax(urls, 'GET', data);

	        ajax.done(function(data){
	        	$('input[name="supplier_id"]').val(data.supplier_id);
	        	$('input[name="product_moq"]').val(data.supplier_moq);
	        	$('input[name="product_lead_time"]').val(data.lead_time);
	        	$('input[name="product_moqprice"]').val(data.supplier_price);

	        	$('select[name="product_gst"]').val(data.gst_id).trigger("change");
	        	$('select[name="product_currency"]').val(data.currency_id).trigger("change");
	        })
	        ajax.fail(function(error){
	            alert("Something Wrong (product).!")
	        })

    	}else{

    			$('input[name="product_moq"]').val('');
	        	$('input[name="product_lead_time"]').val('');
	        	$('input[name="product_moqprice"]').val('');
	        	$('input[name="edt_supplier_id"]').val('');
	        	$('select[name="product_gst"]').val('').trigger("change");
				$('select[name="product_currency"]').val('').trigger("change");

    	}
    });

    @if(request()->route()->named("pr.create"))

    	window.tableIndex = 0;

    	$("#add_product_button").on("click", function(e){

    		var product = $('select[name="product"]').val();
    		var product_id = $('input[name="product_id"]').val();
	        var product_name = $('input[name="product_name"]').val();
	        var product_sku = $('input[name="product_sku"]').val();
	        var product_upc = $('input[name="product_upc"]').val();
    		var product_category = $('input[name="product_category"]').val();
    		var product_brand = $('input[name="product_brand"]').val();
    		var product_qty = $('input[name="product_qty"]').val();
    		var product_unit = $('input[name="product_unit"]').val();
    		var product_unit_id = $('input[name="unit_id"]').val();

    		var product_supplier = $('select[name="product_supplier"]').val();
    		var supplier_id = $('input[name="supplier_id"]').val();
    		var product_moq = $('input[name="product_moq"]').val();
    		var product_lead_time = $('input[name="product_lead_time"]').val();
    		var product_moqprice = $('input[name="product_moqprice"]').val();
    		var product_gst = $('select[name="product_gst"]').val();
    		var product_currency = $('select[name="product_currency"]').val();


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

			var action = ""+
			'<div class="btn-group">'+
			'<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#edit_product" onclick="getProduct('+tableIndex+', this)">'+
			'<i class="fa fa-pencil fa-fw"></i>'+
			'</button>'+
			'<button type="button" class="btn btn-danger btn-sm" onclick="removeProduct('+tableIndex+', this)">'+
			'<i class="fa fa-trash fa-fw"></i>'+
			'</button>'+
			'</div>'+
			"";

			var supplierName = $('select[name="product_supplier"] option:selected').text();

			add_product_table.row.add([
				action,
				product_sku,
				product_upc,
				product_name,
				product_brand,
				supplierName,
				product_qty,
				product_unit,
				product_moqprice
			]);

			add_product_table.draw();

			$("#hidden_product_purchase").append(
				'<input type="hidden" id="tableIndex'+tableIndex+'" name="tableIndex[]" value="'+tableIndex+'">'+
				'<input type="hidden" id="product'+tableIndex+'" name="product[]" value="'+product_id+'">'+
				'<input type="hidden" id="product_id'+tableIndex+'" name="product_id[]" value="'+product_id+'">'+
				'<input type="hidden" id="product_name'+tableIndex+'" name="product_name[]" value="'+product_name+'">'+
				'<input type="hidden" id="product_sku'+tableIndex+'" name="product_sku[]" value="'+product_sku+'">'+
				'<input type="hidden" id="product_upc'+tableIndex+'" name="product_upc[]" value="'+product_upc+'">'+
				'<input type="hidden" id="product_category'+tableIndex+'" name="product_category[]" value="'+product_category+'">'+
				'<input type="hidden" id="product_brand'+tableIndex+'" name="product_brand[]" value="'+product_brand+'">'+
				'<input type="hidden" id="product_qty'+tableIndex+'" name="product_qty[]" value="'+product_qty+'">'+
				'<input type="hidden" id="product_unit'+tableIndex+'" name="product_unit[]" value="'+product_unit+'">'+
				'<input type="hidden" id="product_unit_id'+tableIndex+'" name="product_unit_id[]" value="'+product_unit_id+'">'+
				'<input type="hidden" id="product_supplier'+tableIndex+'" name="product_supplier[]" value="'+product_supplier+'">'+
				'<input type="hidden" id="supplier_id'+tableIndex+'" name="supplier_id[]" value="'+supplier_id+'">'+
				'<input type="hidden" id="product_moq'+tableIndex+'" name="product_moq[]" value="'+product_moq+'">'+
				'<input type="hidden" id="product_lead_time'+tableIndex+'" name="product_lead_time[]" value="'+product_lead_time+'">'+
				'<input type="hidden" id="product_moqprice'+tableIndex+'" name="product_moqprice[]" value="'+product_moqprice+'">'+
				'<input type="hidden" id="product_gst'+tableIndex+'" name="product_gst[]" value="'+product_gst+'">'+
				'<input type="hidden" id="product_currency'+tableIndex+'" name="product_currency[]" value="'+product_currency+'">'
			);

			tableIndex++;

			alert("Added.");

			$('select[name="product"]').val('').trigger("change");
			$('select[name="product_supplier"]').val('').trigger("change");
			$('select[name="product_gst"]').val('').trigger("change");
			$('select[name="product_currency"]').val('').trigger("change");

			$('input[name="supplier_id"]').val('');
			$('input[name="product_id"]').val('');
			$('input[name="product_name"]').val('');
			$('input[name="product_sku"]').val('');
			$('input[name="product_upc"]').val('');
			$('input[name="product_category"]').val('');
			$('input[name="product_brand"]').val('');
			$('input[name="product_unit"]').val('');
			$('input[name="product_moq"]').val('');
			$('input[name="product_lead_time"]').val('');
			$('input[name="product_moqprice"]').val('');
			$('input[name="product_qty"]').val('');

    	})

    @endif

    @if(request()->route()->named("pr.edit"))
    	$("#add_product_button").on("click", function(e){

    		var pr_id = $('input[name="pr_id"]').val();

    		//product
    		var product = $('select[name="product"]').val();
    		var product_id = $('input[name="product_id"]').val();
	        var product_name = $('input[name="product_name"]').val();
	        var product_sku = $('input[name="product_sku"]').val();
	        var product_upc = $('input[name="product_upc"]').val();
    		var product_category = $('input[name="product_category"]').val();
    		var product_brand = $('input[name="product_brand"]').val();
    		var product_qty = $('input[name="product_qty"]').val();
    		var product_unit = $('input[name="product_unit"]').val();
    		var product_unit_id = $('input[name="unit_id"]').val();

    		var product_supplier = $('select[name="product_supplier"]').val();
    		var supplier_id = $('input[name="supplier_id"]').val();
    		var product_moq = $('input[name="product_moq"]').val();
    		var product_lead_time = $('input[name="product_lead_time"]').val();
    		var product_moqprice = $('input[name="product_moqprice"]').val();
    		var product_gst = $('select[name="product_gst"]').val();
    		var product_currency = $('select[name="product_currency"]').val();


    		if (pr_id) {

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
    						"func_type" : "store",
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

			$('select[name="product"]').val('').trigger("change");
			$('select[name="product_supplier"]').val('').trigger("change");
			$('select[name="product_gst"]').val('').trigger("change");
			$('select[name="product_currency"]').val('').trigger("change");

			$('input[name="supplier_id"]').val('');
			$('input[name="product_id"]').val('');
			$('input[name="product_name"]').val('');
			$('input[name="product_sku"]').val('');
			$('input[name="product_upc"]').val('');
			$('input[name="product_category"]').val('');
			$('input[name="product_brand"]').val('');
			$('input[name="product_unit"]').val('');
			$('input[name="product_moq"]').val('');
			$('input[name="product_lead_time"]').val('');
			$('input[name="product_moqprice"]').val('');
			$('input[name="product_qty"]').val('');
    		
    	})
    @endif

</script>

@endpush