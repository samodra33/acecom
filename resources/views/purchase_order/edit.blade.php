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
                        <h4>{{$name_form}} Purchase Order</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>

                        {!! Form::model($po, ['route' => ['po.update', $po->po_id] , 'method' => 'patch', 'files' => true, 'id' => 'purchase-order-form', "autocomplete"=>"off"]) !!}

                        {{ Form::hidden("po_id", isset($po->po_id)?$po->po_id:null ) }}
                        <div class="row">
                        {{-- LEFT FIELD --}}
                        @include('purchase_order.field_left')
                        {{-- */LEFT FIELD --}}

                        {{-- RIGHT FIELD --}}
                        @include('purchase_order.field_right')
                        {{-- */RIGHT FIELD --}}
                        </div>

                        <div class="mt-3 col-md-12">
                            <a href="{{ route('po.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="btn_save">Save</button>
                            
                            @if(isset($po->is_approve))
                            
                            @if($po->is_approve != 1)

                            <button type="submit" class="btn btn-warning" name="approve_po" id="approve_po" value="confirm" onclick="if (confirm('Are you sure to approve this PO ?')) commentDelete(1); return false">Approve</button>

                            @endif

                            @endif
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
                                @if(!request()->route()->named("po.show"))
                                @if(isset($po->is_approve))
                                @if($po->is_approve != 1)

                                <!--<a href="#" data-toggle="modal" data-target="#add_product" class="btn btn-primary"><i class="dripicons-plus" ></i> {{__('file.add_product')}}</a>-->

                                @endif
                                @endif
                                @endif

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                @include("global.datatable")
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

@include('purchase_order.modals.edit_product')

@endsection
@push('scripts')
<script type="text/javascript">

    $(document).ready(function () {
        var validator = $("#purchase-order-form").validate();
    });

    $("ul#purchase").siblings('a').attr('aria-expanded','true');
    $("ul#purchase").addClass("show");
    $("ul#purchase #purchase-order-menu").addClass("active");

    $("input[name='v_po_date']").datepicker({
      autoclose:'true',
    }).on('changeDate',function(e){
          $("input[name='po_date']").val(e.format("yyyy-mm-dd"));
    });

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

    //disable

    @if($po->is_approve == 1 || isset($read_only))
    $('#btn_save').remove();
    $('#btn_save_edit_product').remove();
    $('#add_product_button').remove();
    $('#approve_po').remove();
    $('#add_product').remove();
    $('input').attr("readOnly", true);
    $('select').attr("disabled", "disabled");
    $('textarea').attr("disabled", "disabled");
    @endif

</script>

<script type="text/javascript" src="https://js.stripe.com/v3/"></script>
@endpush
