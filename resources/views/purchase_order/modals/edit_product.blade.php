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
							{{ Form::hidden("edt_po_product_id", null) }}
							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Product<span style="color:red;">*</span></label>
								</div>
								<div class="col-md-8">
									{{ Form::select("edt_product", $product_list, null, array("class"=>"form-control select2_picker", "placeholder"=>"Select", "disabled"=>"true")) }}

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
									{{ Form::text("edt_product_qty", null, array("class"=>"form-control", "placeholder"=>"Qty", "readonly"=>"true")) }}
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
									{{ Form::select("edt_product_supplier", [], null, array("class"=>"form-control select2_picker", "placeholder"=>"Select", "disabled"=>"true")) }}

									{{ Form::hidden("edt_supplier_id", null) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">GST<span style="color:red;">*</span></label>
								</div>
								<div class="col-md-8">
									{{ Form::select("edt_product_gst", $gst_list, null, array("class"=>"form-control select2_picker", "placeholder"=>"Select", "disabled"=>"true")) }}
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
									{{ Form::text("edt_product_moqprice", null, array("class"=>"form-control", "placeholder"=>"Price", "readonly"=>"true")) }}
								</div>
							</div>

						</div>

						<div class="col-md-12">

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label"><span style="font-size: 20px; font-weight:bold;"> Assign Warehouse </span></label>
								</div>
								<div class="col-md-8">

								</div>
							</div>

						</div>

						<div class="col-md-12">
							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Warehouse</label>
								</div>
								<div class="col-md-8">
									{{ Form::select("edt_product_warehouse", $warehouse_lists, null, array("class"=>"form-control select2_picker", "placeholder"=>"Select")) }}
								</div>
							</div>
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

    @if(request()->route()->named("po.create"))


    @endif

    @if(!request()->route()->named("po.create"))

	//get Product

    	function getProduct(id)
    	{
    		if(id){

    			var urls = '{{route("poProd.service.find_product_id", ":param")}}';
	        	urls = urls.replace(':param', id);

	        	var ajax = getDataWithAjax(urls, 'GET');

	        	ajax.done(function(datas){
	        		//console.log(datas)

	        		$('input[name="edt_po_product_id"]').val( datas.po_product_id );

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
    	
    @endif


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

</script>

@endpush