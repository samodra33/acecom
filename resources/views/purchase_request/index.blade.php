@extends('layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif

<section>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header mt-2">
                <h3 class="text-center">{{trans('file.Purchase Request')}}</h3>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 offset-md-2 mt-3">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong>{{trans('file.Choose Your Date')}}</strong> &nbsp;</label>
                        <div class="d-tc">
                            <div class="input-group">
                                <input type="text" name="date" class="daterangepicker-field form-control" value="" required />
                                <input type="hidden" name="starting_date" value="" />
                                <input type="hidden" name="ending_date" value="" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-3 @if(\Auth::user()->role_id > 2){{'d-none'}}@endif">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong>{{trans('file.Choose Warehouse')}}</strong> &nbsp;</label>
                        <div class="d-tc">
                            <select id="warehouse_id" name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" >
                                <option value="">{{trans('file.All Warehouse')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mt-3">
                    <div class="form-group">
                        <button class="btn btn-primary" id="filter-btn" type="submit">{{trans('file.Search')}}</button>
                    </div>
                </div>
            </div>
        </div>
        @if(in_array("purchases-request-add", $permissions_lists))
            <a href="{{route('pr.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> {{trans('file.Add Purchase')}}</a>&nbsp;
        @endif
    </div>

    <div class="table-responsive">

    </div>

</section>


@endsection

@push('scripts')
<script type="text/javascript">

  $(function() {
    $(".daterangepicker-field").daterangepicker( "option", "disabled", true );
  });

  $(".daterangepicker-field").daterangepicker({
    callback: function(startDate, endDate, period){
      var starting_date = startDate.format('YYYY-MM-DD');
      var ending_date = endDate.format('YYYY-MM-DD');
      var title = starting_date + ' To ' + ending_date;
      $('input[name="date"]').val(title);
      $('input[name="starting_date"]').val(starting_date);
      $('input[name="ending_date"]').val(ending_date);
    }
  });

  $("ul#purchase").siblings('a').attr('aria-expanded','true');
  $("ul#purchase").addClass("show");
  $("ul#purchase #purchase-request-menu").addClass("active");


</script>
<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
@endpush