@extends('layout.main')
@section('content')

@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
      <div class="row">
        <div class="container-fluid">
          <div class="col-md-12">
            <div class="brand-text float-left mt-4">
                <h3>{{trans('file.welcome')}} <span>{{Auth::user()->name}}</span> </h3>
            </div>

            @if(in_array("revenue_profit_summary", $all_permission))
            <div class="filter-toggle btn-group">
              <!-- toggle content-->
            </div>
            @endif

          </div>
        </div>
      </div>
      <!-- Counts Section -->
      <section class="dashboard-counts">
        <div class="container-fluid">
          <div class="row">
            @if(in_array("revenue_profit_summary", $all_permission))
            <div class="col-md-12 form-group">
              <div class="row">
                <!-- widget row -->
              </div>
            </div>
            @endif
            @if(in_array("cash_flow", $all_permission))
            <div class="col-md-7 mt-4">
              <!-- chart -->
            </div>
            @endif
            @if(in_array("monthly_summary", $all_permission))
            <div class="col-md-5 mt-4">
              <!-- chart -->
            </div>
            @endif
          </div>
        </div>

        <div class="container-fluid">
          <div class="row">
            @if(in_array("yearly_report", $all_permission))
            <div class="col-md-12">
              <!-- chart -->
            </div>
            @endif
            <div class="col-md-7">
              <!-- table transaction -->
            </div>
            <div class="col-md-5">
              <!-- Best Seller -->
            </div>
            <div class="col-md-6">
              <!-- best seller qty this year -->
            </div>
            <div class="col-md-6">
              <!-- best seller price this year -->
            </div>
          </div>
        </div>
      </section>


@endsection

@push('scripts')
<script type="text/javascript">

</script>
@endpush
