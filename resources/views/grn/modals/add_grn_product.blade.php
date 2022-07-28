<div class="modal fade" id="add_pr_product" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
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

						<div class="row">

							<div class="col-md-4">
								<div class="mt-3">
									<label class="control-label">Product Name</label>
									{{ Form::text("product_name", null, array("class"=>"form-control", "placeholder"=>"Product Name")) }}
								</div>
							</div>

							<div class="col-md-4">
								<div class="mt-3">
									<label class="control-label">Product SKU</label>
									{{ Form::text("product_sku", null, array("class"=>"form-control", "placeholder"=>"Product SKU")) }}
								</div>
							</div>

							<div class="col-md-4">
								<div class="mt-3">
									<label class="control-label">PO No.</label>
									{{ Form::select("po_no", [], null, array("class"=>"form-control select2_picker", "placeholder"=>"Select")) }}
								</div>
							</div>

							<div class="col-md-4">
								<div class="mt-3">
									<label class="control-label">Warehouse</label>
									{{ Form::select("warehouse", $warehouse_lists, null, array("class"=>"form-control select2_picker", "placeholder"=>"Select")) }}
								</div>
							</div>

						</div>


		                <div class="col-md-12">
		                    <div style="margin-top: 40px;">
		                        <div class="form-group">
		                        	<button type="button" class="btn btn-primary" id="find_product">Find</button>
		                        	<button type="button" class="btn btn-primary" id="selected_add_btn">Select Product</button>
		                        </div>
		                    </div>
		                    <div class="table-responsive mt-3">
		                        <table class="table table-hover" id="add_product_table" style="width: 100%">
		                            <thead>
		                                <tr>
		                                    <th>#</th>
		                                    <th>Po No.</th>
		                                    <th>Product SKU</th>
		                                    <th>Warehouse</th>
		                                    <th>Unreceived</th>
		                                    <th>Qty</th>
		                                    <th>Unit</th>
		                                    <th>status</th>
		                                    <th>Product Name</th>
		                                    <th>Brand</th>
		                                </tr>
		                            </thead>
		                            <tbody>
		                                {{-- Autosync --}}
		                            </tbody>
		                        </table>
		                    </div>
		                </div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>


@push('scripts')
<script type="text/javascript">

    $(document).ready(function(){

		startAddproductDataTable();

		var warehouse_id = 0;
		getPoList(warehouse_id);

    });

    function startAddproductDataTable() {

    	$('#add_product_table').DataTable({
    		dom: 'rtip',
    		processing: true,
    		responsive: true,
    		autoWidth: false,
    		language: {
    			processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '},
    			serverSide: false,
    			order: [[0, 'desc']],
    			ajax: {
    				url : '{{route("grn_prod.service.prod_from_po")}}'
    			},
    			columns: [
    			{data: '#'},
    			{data: 'po_no', name: 'po.po_no'},
    			{data: 'product_sku', name: 'product_sku'},
    			{data: 'warehouse_name', name: 'warehouse.name'},
    			{data: 'unreceived_qty', name: 'poWarehouse.warehouse_qty'},
    			{data: 'qty_input', name: 'qty_input'},
    			{data: 'unit_code', name: 'unit.unit_code'},
    			{data: 'status_prod', name: 'poWarehouse.status'},
    			{data: 'product_name', name: 'product_name'},
    			{data: 'title', name: 'brand.title'},
    			],
    			order: [[1, 'asc']]
    		});


    }

    $("button[id='find_product']").on("click", function(){
        $('#add_product_table')
            .on('preXhr.dt', function ( e, settings, data ) {
            	data.product_name = $("input[name='product_name']").val();
                data.product_sku = $("input[name='product_sku']").val();
                data.po_no = $("select[name='po_no']").val();
                data.warehouse_id = $("select[name='warehouse']").val();
            });

            var table = $('#add_product_table').DataTable();
            table.ajax.reload();
    });

    //get PO List

    function getPoList(id)
    {

    	var urls = '{{route("po.service.find_po_by_warehouse", ":param")}}';
        urls = urls.replace(':param', id);

        var data = {
            "warehouse_id" : id
        }

        var ajax = getDataWithAjax(urls, 'GET', data);

        ajax.done(function(datas){

        	var element = $("select[name='po_no']");
        	setSelectOption(element, null, datas);

        })
        ajax.fail(function(error){
            alert("Something Wrong (PO Select).!")
        })
    }

    /////addItem

    function checkQty(e)
    {
    	var po_warehouse_id = $(e).data('po_warehouse_id');
    	var unreceived = $(e).data('unreceived');

    	var strQty= "add_part_qty_".concat(po_warehouse_id);

    	var qty = $("input[name='"+strQty+"']").val();

    	if (isNaN(qty)) {

    		alert("Qty must be number");
    		$("input[name='"+strQty+"']").val(unreceived);

    		return false;

    	}

    	if (qty > unreceived) {

    		alert("the qty cannot be bigger than unreceived qty");
    		$("input[name='"+strQty+"']").val(unreceived);

    		return false;
    	}

    }


    @if(request()->route()->named("grn.create"))

    var tableIndex = 0;

    $("#selected_add_btn").on("click", function(e){

    	var product = $("input[name='selected_product[]']:checked");
    	var urls = '{{ route("poProd.service.add_poProd_create_ajax") }}';

    	if(product.is(":checked")){

    		var product_list = new Array();
            var qty_list = new Array();

            product.map(function(_, el) {

                var strQty= "add_part_qty_".concat($(el).val());

                qty_list[$(el).val()] = $("input[name='"+strQty+"']").val();
                product_list.push($(el).val());

            }).get();

            qty_list = qty_list.filter(function () { return true });

            var data = {
                "products" : product_list,
                "qty" : qty_list
            }

            var ajax = getDataWithAjax(urls, 'GET', data);

            //////////////////////////////////////////////ajax


            ajax.done(function(datas){

            	$("input[name='selected_product[]']").prop('checked', false);

            	$.each(datas, function(key, val){


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

                    add_product_table.row.add([
                        action,
                        val.product_sku,
                        val.product_upc,
                        val.product_name,
                        val.brand_name,
                        val.supplier_name,
                        val.warehouse_name,
                        val.qty,
                        val.prchsunit,
                        val.product_price
                    ])
                    add_product_table.draw();

                    $("#hidden_product_grn").append(
                    	'<input type="hidden" id="tableIndex'+tableIndex+'" name="tableIndex[]" value="'+tableIndex+'">'+
                    	'<input type="hidden" id="po_warehouse_id'+tableIndex+'" name="po_warehouse_id[]" value="'+val.po_warehouse_id+'">'+
                    	'<input type="hidden" id="warehouse_id'+tableIndex+'" name="warehouse_id[]" value="'+val.warehouse_id+'">'+
                    	'<input type="hidden" id="po_product_id'+tableIndex+'" name="po_product_id[]" value="'+val.po_product_id+'">'+
                    	'<input type="hidden" id="product_id'+tableIndex+'" name="product_id[]" value="'+val.product_id+'">'+
                    	'<input type="hidden" id="product_qty'+tableIndex+'" name="product_qty[]" value="'+val.qty+'">'+
                    	'<input type="hidden" id="unreceived_qty'+tableIndex+'" name="unreceived_qty[]" value="'+val.unreceivedQty+'">'+
                    	'<input type="hidden" id="product_price'+tableIndex+'" name="product_price[]" value="'+val.product_price+'">'+
                    	'<input type="hidden" id="unit_id'+tableIndex+'" name="unit_id[]" value="'+val.prchsunitId+'">'+
                    	'<input type="hidden" id="supplier_id'+tableIndex+'" name="supplier_id[]" value="'+val.supplier_id+'">'+

                    	'<input type="hidden" id="product_sku'+tableIndex+'" name="product_sku[]" value="'+val.product_sku+'">'+
                    	'<input type="hidden" id="product_upc'+tableIndex+'" name="product_upc[]" value="'+val.product_upc+'">'+
                    	'<input type="hidden" id="product_name'+tableIndex+'" name="product_name[]" value="'+val.product_name+'">'+
                    	'<input type="hidden" id="brand_name'+tableIndex+'" name="brand_name[]" value="'+val.brand_name+'">'+
                    	'<input type="hidden" id="supplier_name'+tableIndex+'" name="supplier_name[]" value="'+val.supplier_name+'">'+
                    	'<input type="hidden" id="warehouse_name'+tableIndex+'" name="warehouse_name[]" value="'+val.warehouse_name+'">'+
                    	'<input type="hidden" id="prchsunit'+tableIndex+'" name="prchsunit[]" value="'+val.prchsunit+'">'
                    );

                    tableIndex++;

            	})
            	

            	alert("Add product successfully.");
            })
            ajax.fail(function(error){
            	alert("Something Wrong (js).!")
            })

            //////////////////////////////////////////////

    	}else{
            alert("Please select the product first.");
        }
    })

    @endif

    @if(request()->route()->named("grn.edit"))

    $("#selected_add_btn").on("click", function(e){

    	var product = $("input[name='selected_product[]']:checked");
    	var grn_id = $("input[name='grn_id']").val();
    	var urls = '{{ route("grnProd.service/store_grn_product") }}';

    	if(product.is(":checked")){

    		var product_list = new Array();
            var qty_list = new Array();

            product.map(function(_, el) {

                var strQty= "add_part_qty_".concat($(el).val());

                qty_list[$(el).val()] = $("input[name='"+strQty+"']").val();
                product_list.push($(el).val());

            }).get();

            qty_list = qty_list.filter(function () { return true });

            var data = {
                "products" : product_list,
                "grn_id" : grn_id,
                "qty" : qty_list
            }

            var ajax = getDataWithAjax(urls, 'POST', data);

            //////////////////////////////////////////////ajax

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
            alert("Please select the product first.");
        }
    })

    @endif

</script>

@endpush