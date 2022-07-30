@extends('layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif

<section>
    <div class="container-fluid">
        @if(in_array("purchases-request-add", $permissions_lists))
        <a href="{{route('pr.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> {{trans('file.Add Purchase')}}</a>&nbsp;
        @endif
    </div>

    <div class="container-fluid">
        <div class="card">

            <div class="card-header mt-2">
                <h3 class="text-center">{{trans('file.Purchase Request')}}</h3>
            </div>

            <div class="card-body">
              <div class="row">

                <div class="col-md-4">
                  <div class="mt-3">
                    <label class="control-label">PR No.</label>
                    {{ Form::text("pr_no", null, array("class"=>"form-control", "placeholder"=>"PRXXXXXXXXXX")) }}
                  </div>
                </div>


                <div class="col-md-4">
                  <div class="mt-3">
                    <label class="control-label">Supplier Name</label>
                    {{ Form::text("supp_name", null, array("class"=>"form-control", "placeholder"=>"Supplier Name")) }}
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="mt-3">
                    <label class="control-label">Type</label>
                    {{ Form::select("pr_type", $purchase_type_list, null, array("class"=>"form-control select2_picker", "placeholder"=>"Select", "required"=>"required")) }}
                  </div>
                </div>

              </div>

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


              </div>


              <div class="row">


                <div class="col-md-4">
                  <div class="mt-3">
                    <label class="control-label">PR Start Date</label>
                    {{ Form::text("v_start_date", null, array("class"=>"form-control date", "placeholder"=>"dd/mm/yyyy", "required"=>"required")) }}

                    {{ Form::hidden("pr_start_date", null, array("class"=>"form-control")) }}
                  </div>
                </div>
                  

                <div class="col-md-4">
                  <div class="mt-3">
                    <label class="control-label">PR End Date</label>
                    {{ Form::text("v_end_date", null, array("class"=>"form-control date", "placeholder"=>"dd/mm/yyyy", "required"=>"required")) }}

                    {{ Form::hidden("pr_end_date", null, array("class"=>"form-control")) }}
                  </div>
                </div>

              </div>

              <div class="row" style="margin-top:20px;">
                <div class="col-md-12">

                  <button type="button" class="btn btn-primary btn-outline" name="search_act">Search</button>
                  
                </div>
              </div>

            </div>

        </div>
    </div>

    <div class="container-fluid">

        <a href="#" data-toggle="modal" data-target="#bulk_approve" class="btn btn-success">{{trans('file.Bulk Approve')}}</a>
        <a href="#" data-toggle="modal" data-target="#bulk_convert" class="btn btn-success">{{trans('file.Bulk Convert')}}</a>

    </div>

    <div class="container-fluid">
        <div class="mt-3">
          <div>
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <strong class="card-title">Purchase Request List</strong>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 table-responsive">
                        @include("global.datatable")
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>

</section>

@include('purchase_request.modals.bulk_approve')
@include('purchase_request.modals.bulk_convert')
@endsection

@push('scripts')
<script type="text/javascript">

    $(document).on('keypress',function(e) {
      if(e.which == 13) {
        $("button[name='search_act']").click();
      }
    });

    $("ul#purchase").siblings('a').attr('aria-expanded','true');
    $("ul#purchase").addClass("show");
    $("ul#purchase #purchase-request-menu").addClass("active");

    //start date
    $("input[name='v_start_date']").datepicker({
      autoclose:'true',
    }).on('changeDate',function(e){
      $("input[name='pr_start_date']").val(e.format("yyyy-mm-dd"));
    });

    //end date
    $("input[name='v_end_date']").datepicker({
      autoclose:'true',
    }).on('changeDate',function(e){
      $("input[name='pr_end_date']").val(e.format("yyyy-mm-dd"));
    });

    ////

    $("button[name='search_act']").on("click", function(){
      $('#pr-table')
      .on('preXhr.dt', function ( e, settings, data ) {

        if ($("input[name='v_start_date']").val() == "" ) {
          $("input[name='pr_start_date']").val("")
        }

        if ($("input[name='v_end_date']").val() == "" ) {
          $("input[name='pr_end_date']").val("")
        }

        data.pr_no = $("input[name='pr_no']").val();
        data.supp_name = $("input[name='supp_name']").val();
        data.pr_type = $("select[name='pr_type']").val();
        data.product_name = $("input[name='product_name']").val();
        data.product_sku = $("input[name='product_sku']").val();
        data.start_date = $("input[name='pr_start_date']").val();
        data.end_date = $("input[name='pr_end_date']").val();

      });
      window.LaravelDataTables["pr-table"].draw();
    });



</script>
<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
@endpush
