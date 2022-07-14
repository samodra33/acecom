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
                        <h4>{{trans('file.create GRN')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>

                        {!! Form::open(['route' => 'grn.store', 'method' => 'post', 'files' => true, 'id' => 'grn-form', "autocomplete"=>"off"]) !!}

                        <div class="row">
                        {{-- LEFT FIELD --}}
                        @include('grn.field_left')
                        {{-- */LEFT FIELD --}}

                        {{-- RIGHT FIELD --}}
                        @include('grn.field_right')
                        {{-- */RIGHT FIELD --}}
                        </div>

                        <div id="hidden_product_grn"></div>

                        <div class="mt-3 col-md-12">
                            <button type="submit" class="btn btn-primary" id="btn_save">Save</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.product_list')}}</h4>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a href="#" data-toggle="modal" data-target="#add_product" class="btn btn-primary"><i class="dripicons-plus"></i> {{__('file.add_product')}}</a>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="add-product-table">
                                    <thead>
                                        <tr>
                                            <th>{{trans('file.Action')}}</th>
                                            <th>{{trans('file.Product SKU')}}</th>
                                            <th>{{trans('file.Product UPC')}}</th>
                                            <th>{{trans('file.Product Name')}}</th>
                                            <th>{{trans('file.Brand')}}</th>
                                            <th>{{trans('file.Supplier')}}</th>
                                            <th>{{trans('file.Qty')}}</th>
                                            <th>{{trans('file.Unit')}}</th>
                                            <th>{{trans('file.Price')}}</th>
                                        </tr>
                                    </thead>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

@endsection
@push('scripts')
<script type="text/javascript">

    $(document).ready(function () {
        var validator = $("#grn-form").validate();
    });

    $("ul#grn").siblings('a').attr('aria-expanded','true');
    $("ul#grn").addClass("show");
    $("ul#grn #grn-menu").addClass("active");

    $("input[name='v_grn_date']").datepicker({
      autoclose:'true',
    }).on('changeDate',function(e){
      $("input[name='grn_date']").val(e.format("yyyy-mm-dd"));
    });

    //table
    window.add_product_table = $('#add-product-table').DataTable();

    //validator

    jQuery.validator.setDefaults({
        errorPlacement: function (error, element) {
            error.html('This field is required.');
            $(element).closest('div.form-group').find('.validation-msg').html(error.html());
        },
        highlight: function (element) {
            $(element).closest('div.form-group').removeClass('has-success').addClass('has-error');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).closest('div.form-group').removeClass('has-error').addClass('has-success');
            $(element).closest('div.form-group').find('.validation-msg').html('');
        }
    });

</script>

<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
@endpush
