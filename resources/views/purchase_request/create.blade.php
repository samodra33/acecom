@extends('layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Add Purchase Request')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>

                        {!! Form::open(['route' => 'pr.store', 'method' => 'post', 'files' => true, 'id' => 'purchase-request-form', "autocomplete"=>"off"]) !!}

                        <div class="row">
                        {{-- LEFT FIELD --}}
                        @include('purchase_request.field_left')
                        {{-- */LEFT FIELD --}}

                        {{-- RIGHT FIELD --}}
                        @include('purchase_request.field_right')
                        {{-- */RIGHT FIELD --}}
                        </div>

                        <div class="mt-3 col-md-12">
                            <button type="submit" class="btn btn-primary" id="btn_save">Save</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@push('scripts')
<script type="text/javascript">

    $("ul#purchase").siblings('a').attr('aria-expanded','true');
    $("ul#purchase").addClass("show");
    $("ul#purchase #purchase-request-menu").addClass("active");

    $("input[name='v_pr_date']").datepicker({
      autoclose:'true',
    }).on('changeDate',function(e){
      $("input[name='pr_date']").val(e.format("yyyy-mm-dd"));
    });

</script>

<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
@endpush
