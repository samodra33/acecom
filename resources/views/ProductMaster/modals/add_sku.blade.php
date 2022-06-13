<div class="modal fade" id="add_sku" tabindex="-1" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="scrollmodalLabel">Add SKU</h5>
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
									{{ Form::text("sku", null, array("class"=>"form-control", "placeholder"=>"SKU")) }}
								</div>
							</div>

							<div class="row form-group">
								<div class="col-md-4">
									<label class="control-label">Description</label>
								</div>
								<div class="col-md-8">
									{{ Form::textarea("desc", null, array("class"=>"form-control", "placeholder"=>"Description")) }}
								</div>
							</div>

						</div>

						<div class="mt-3 col-md-12">

							<button type="button" class="btn btn-primary" id="add_sku_button">Add SKU</button>

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

	window.tableIndex = 0;

	@if(request()->route()->named("mProduct.create"))

	$("#add_sku_button").on("click", function(e){

		var sku = $("input[name='sku']").val();
		var desc = $("textarea[name='desc']").val();

		var sku_list = new Array();
		var desc_list = new Array();

		if (sku == '') {

			alert("Check your input.");

		}else{

			sku_list.push(sku);
			desc_list.push(desc);


			var data = {
				"sku" : sku_list,
				"desc" : desc_list,

			}

			var action = ""+
			'<div class="btn-group">'+
			'<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#edit_sku" onclick="getSku('+tableIndex+', this)">'+
			'<i class="fa fa-pencil fa-fw"></i>'+
			'</button>'+
			'<button type="button" class="btn btn-danger btn-sm" onclick="removeSku('+tableIndex+', this)">'+
			'<i class="fa fa-trash fa-fw"></i>'+
			'</button>'+
			'</div>'+
			"";

			product_sku.row.add([
				action,
				sku,
				desc
				]);

			product_sku.draw()
			$("#add_product_sku_table_hidden").append(
				'<input type="hidden" id="tableIndex'+tableIndex+'" name="tableIndex[]" value="'+tableIndex+'">'+
				'<input type="hidden" id="sku'+tableIndex+'" name="sku[]" value="'+sku+'">'+
				'<input type="hidden" id="desc'+tableIndex+'" name="desc[]" value="'+desc+'">'
			);
			tableIndex++;

			alert("Added.");
			$("input[name='sku']").val("");
			$("textarea[name='desc']").val("");
		}
	})

	@endif

	@if(request()->route()->named("mProduct.edit"))

	$("#add_sku_button").on("click", function(e){

		var sku = $("input[name='sku']").val();
		var desc = $("textarea[name='desc']").val();

		var sku_list = new Array();
		var desc_list = new Array();

		if (sku == '') {

			alert("Check your input.");

			return false;

		}else{

			var urls = '{{ route("mProduct.service/add_sku") }}';
       		var product_id = $("input[name='product_id']").val();

            var data = {
                "product_id" : product_id,
                "sku" : sku,
                "desc" : desc
            }

            $.ajax({

                type:'POST',
                url:urls,
                data: data,

	            success:function(response){

	            	alert(response)

	            	var table = $('#product_sku').DataTable();
	                table.ajax.reload();
	                $("input[name='sku']").val("");
					$("textarea[name='desc']").val("");
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