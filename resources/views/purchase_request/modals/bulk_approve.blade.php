<div class="modal fade" id="bulk_approve" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="scrollmodalLabel">Bulk Approve</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="card">
					<div class="card-header">
						<strong class="card-title">Purchase Request List</strong>
					</div>
					<div class="card-body">

						<div class="row">

							<div class="col-md-4">
								<div class="mt-3">
									<label class="control-label">PR No.</label>
									{{ Form::text("approve_pr_no", null, array("class"=>"form-control", "placeholder"=>"PRXXXXXXXXXX")) }}
								</div>
							</div>

							<div class="col-md-4">
								<div class="mt-3">
									<label class="control-label">Type</label>
									{{ Form::select("approve_pr_type", $purchase_type_list, null, array("class"=>"form-control select2_picker", "placeholder"=>"Select", "required"=>"required")) }}
								</div>
							</div>


							<div class="col-md-4">
								<div class="mt-3">
									<label class="control-label">PR Start Date</label>
									{{ Form::text("approve_v_start_date", null, array("class"=>"form-control date", "placeholder"=>"dd/mm/yyyy", "required"=>"required")) }}

									{{ Form::hidden("approve_pr_start_date", null, array("class"=>"form-control")) }}
								</div>
							</div>


							<div class="col-md-4">
								<div class="mt-3">
									<label class="control-label">PR End Date</label>
									{{ Form::text("approve_v_end_date", null, array("class"=>"form-control date", "placeholder"=>"dd/mm/yyyy", "required"=>"required")) }}

									{{ Form::hidden("approve_pr_end_date", null, array("class"=>"form-control")) }}
								</div>
							</div>

						</div>


		                <div class="col-md-12">
		                    <div style="margin-top: 40px;">
		                        <div class="form-group">
		                        	<button type="button" class="btn btn-primary" id="find_unapprove_pr">Find</button>
		                        	<button type="button" class="btn btn-primary" id="selected_approve_btn">Approve</button>
		                        </div>
		                    </div>
		                    <div class="table-responsive mt-3">
		                        <table class="table table-hover" id="pr_unapprove_table" style="width: 100%">
		                            <thead>
		                                <tr>
		                                    <th>#</th>
		                                    <th>PR No.</th>
		                                    <th>PR Date</th>
		                                    <th>Type</th>
		                                    <th>Modified Date</th>
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
				<div class="form-group">

				</div>
			</div>
		</div>
	</div>
</div>


@push('scripts')
<script type="text/javascript">

    $(document).ready(function(){

    	startAddPrUnapproveDataTable();
    	     
    });
    function startAddPrUnapproveDataTable() 
    {

	    $('#pr_unapprove_table').DataTable({

	        processing: true,
	        serverSide: true,
	        bInfo: true,
	        bPaginate: true,
	        bAutoWidth: false, 
	        searching: false, 
	        ajax: {
	          url : '{{ route("pr.service.get_multi_pr_table") }}',
	          data : {
	            unaprove : 1,
	            convert : 0
	          }
	        },
	        columns: [
	        {data: 'approve_box', name: 'approve_box'},
	        {data: 'pr_no', name: 'pr_no'},
	        {data: 'PR Date', name: 'pr_date'},
	        {data: 'type', name: 'type.type_name'},
	        {data: 'Modified Date', name: 'updated_at'}
	        ],
	        order: [[4, 'desc']]
	    });

	}

    //start date
    $("input[name='approve_v_start_date']").datepicker({
      autoclose:'true',
    }).on('changeDate',function(e){
      $("input[name='approve_pr_start_date']").val(e.format("yyyy-mm-dd"));
    });

    //end date
    $("input[name='approve_v_end_date']").datepicker({
      autoclose:'true',
    }).on('changeDate',function(e){
      $("input[name='approve_pr_end_date']").val(e.format("yyyy-mm-dd"));
    });

    ////

	//search

    $("button[id='find_unapprove_pr']").on("click", function(){
      $('#pr_unapprove_table')
      .on('preXhr.dt', function ( e, settings, data ) {

        if ($("input[name='approve_v_start_date']").val() == "" ) {
          $("input[name='approve_pr_start_date']").val("")
        }

        if ($("input[name='approve_v_end_date']").val() == "" ) {
          $("input[name='approve_pr_end_date']").val("")
        }

        data.pr_no = $("input[name='approve_pr_no']").val();
        data.pr_type = $("select[name='approve_pr_type']").val();
        data.start_date = $("input[name='approve_pr_start_date']").val();
        data.end_date = $("input[name='approve_pr_end_date']").val();

      });
      var tableUnapprove = $('#pr_unapprove_table').DataTable();
      tableUnapprove.ajax.reload();

    });

    //approve

    $("#selected_approve_btn").on("click", function(e){

    	var pr_approve = $("input[name='selected_approve_pr[]']:checked");
    	var urls = '{{ route("pr.service.bulk_pr_approve_ajax") }}';

    	if(pr_approve.is(":checked")){

    		$("#js-sprinner-loading").show();

    		var pr_approve_list = new Array();

            pr_approve.map(function(_, el) {

                pr_approve_list.push($(el).val());

            }).get();

            var data = {
                "pr_id" : pr_approve_list
            }

            var ajax = getDataWithAjax(urls, 'GET', data);
            ajax.done(function(response){
            	alert(response);

            	var tableUnapprove = $('#pr_unapprove_table').DataTable();
      				tableUnapprove.ajax.reload();

      				var tableConvert = $('#pr_convert_table').DataTable();
      				tableConvert.ajax.reload();

      				window.LaravelDataTables["pr-table"].draw();

      				$("#js-sprinner-loading").hide();
            })
            ajax.fail(function(error){
            	alert("Something Wrong (js).!")
            })

    	}else{
            alert("Please select PR first.");
        }
    })

</script>

@endpush