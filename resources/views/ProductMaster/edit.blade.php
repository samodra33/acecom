@extends('layout.main')

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Update Product')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        <form id="product-form">
                            {{ Form::hidden("product_id", $id) }}


                            @include('ProductMaster.field')


                            <!-- Button Submit-->

                            <div class="form-group" style="margin-top:50px;">
                                <input type="button" value="{{trans('file.submit')}}" id="submit-btn" class="btn btn-primary">
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Update SKU and Supplier')}}</h4>
                    </div>
                    <div class="card-body">

                            <!-- Table Add-->
                            <!--
                            <div class="col-md-12" style="margin-top: 50px;">
                                <div class="form-group">
                                    <a href="#" data-toggle="modal" data-target="#add_sku" class="btn btn-primary"><i class="dripicons-plus"></i> {{__('file.add SKU')}}</a>
                                </div>
                            </div>

                            <div class="col-md-12">

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="product_sku">
                                        <thead>
                                            <tr>
                                                <th>{{trans('file.Action')}}</th>
                                                <th>{{trans('file.SKU')}}</th>
                                                <th>{{trans('file.Description')}}</th>
                                            </tr>
                                        </thead>

                                    </table>
                                </div>
                            </div>

                            -->

                            <div class="col-md-12" style="margin-top: 50px;">
                                <div class="form-group">
                                    <a href="#" data-toggle="modal" data-target="#add_supplier" class="btn btn-primary"><i class="dripicons-plus"></i> {{__('file.add Supplier')}}</a>
                                </div>
                            </div>

                            <div class="col-md-12">

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="product_supplier">
                                        <thead>
                                            <tr>
                                                <th>{{trans('file.Action')}}</th>
                                                <th>{{trans('file.MOQ')}}</th>
                                                <th>{{trans('file.Supplier')}}</th>
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

@include('ProductMaster.modals.add_sku')
@include('ProductMaster.modals.edit_sku')

@include('ProductMaster.modals.add_supplier')
@include('ProductMaster.modals.edit_supplier')
</section>


@endsection
@push('scripts')

@include("layouts.datatables_js")
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{asset('vendor/datatables/buttons.server-side.js')}}"></script>

<script type="text/javascript">

    $(document).ready(function(){

        var unitSelected = {{ isset($product->product_unit)?$product->product_unit:null }};
        var unitSelectedSele = {{ isset($product->product_sale_unit)?$product->product_sale_unit:null }};
        var unitSelectedPurchase = {{ isset($product->product_purchase_unit)?$product->product_purchase_unit:null }};

        populate_unit(unitSelected, unitSelectedSele, unitSelectedPurchase);

    })

    $("ul#product").siblings('a').attr('aria-expanded','true');
    $("ul#product").addClass("show");
    $("ul#product #product-create-menu").addClass("active");

    $('[data-toggle="tooltip"]').tooltip();

    $('#genbutton').on("click", function(){
      $.get('../service/gencode', function(data){
        $("input[name='product_code']").val(data);
      });
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //remove image

    $(".remove-img").on("click", function () {
        $(this).closest("tr").remove();
    });

    //table
    window.product_image = $('#product_image').DataTable({searching: false, paging: false, info: false});

    $('#product_sku').DataTable({

        processing: true,
        serverSide: true,
        bInfo: false,
        bPaginate: true,
        bAutoWidth: false, 
        ajax: {
          url : '{{ route("mProduct.service.sku_table") }}',
          data : {
            product_id : $('input[name="product_id"]').val()
          }
        },
        columns: [
        {data: 'action', name: 'action'},
        {data: 'sku_no', name: 'sku_no'},
        {data: 'sku_desc', name: 'sku_desc'}
        ],
        order: [[2, 'desc']]
    });

    $('#product_supplier').DataTable({

        processing: true,
        serverSide: true,
        bInfo: false,
        bPaginate: true,
        bAutoWidth: false, 
        ajax: {
          url : '{{ route("mProduct.service.supplierProd_table") }}',
          data : {
            product_id : $('input[name="product_id"]').val()
          }
        },
        columns: [
        {data: 'action', name: 'action'},
        {data: 'supplier_moq', name: 'supplier_moq'},
        {data: 'supplier_name', name: 'supplier_name'},
        {data: 'supplier_price', name: 'supplier_price'}
        ],
        order: [[2, 'desc']]
    });

    //unit

    $('select[name="unit_id"]').on('change', function() {

        unitID = $(this).val();
        if(unitID) {
            populate_unit(unitID, unitID, unitID);
        }else{
            $('select[name="sale_unit_id"]').empty();
            $('select[name="purchase_unit_id"]').empty();
        }
    });

    function populate_unit(unitID, saleId, purchaseId){
        $.ajax({
            url: '../saleunit/'+unitID,
            type: "GET",
            dataType: "json",
            success:function(data) {
                  $('select[name="sale_unit_id"]').empty();
                  $('select[name="purchase_unit_id"]').empty();
                  $.each(data, function(key, value) {
                      $('select[name="sale_unit_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                      $('select[name="purchase_unit_id"]').append('<option value="'+ key +'">'+ value +'</option>');
                  });

                  $('.selectpicker').selectpicker('refresh');

                  $('select[name=sale_unit_id]').val(saleId);
                  $('select[name=purchase_unit_id]').val(purchaseId);
                  $('.selectpicker').selectpicker('refresh');
            },
        });
    }


    //dropzone portion
    Dropzone.autoDiscover = false;

    //image form

    $(".dropzone").sortable({
        items:'.dz-preview',
        cursor: 'grab',
        opacity: 0.5,
        containment: '.dropzone',
        distance: 20,
        tolerance: 'pointer',
        stop: function () {
          var queue = myDropzone.getAcceptedFiles();
          newQueue = [];
          $('#imageUpload .dz-preview .dz-filename [data-dz-name]').each(function (count, el) {
                var name = el.innerHTML;
                queue.forEach(function(file) {
                    if (file.name === name) {
                        newQueue.push(file);
                    }
                });
          });
          myDropzone.files = newQueue;
        }
    });

    //get update

    //var updateurls = '{{route("mProduct.update", ":param")}}';
    //updateurls = updateurls.replace(':param', {{$id}});
    var updateurls = '{{route("mProduct.updateProduct")}}';

    myDropzone = new Dropzone('div#imageUpload', {
        addRemoveLinks: true,
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: 100,
        maxFilesize: 12,
        paramName: 'image',
        clickable: true,
        method: 'POST',
        url:updateurls,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        renameFile: function(file) {
            var dt = new Date();
            var time = dt.getTime();
            return time + file.name;
        },
        acceptedFiles: ".jpeg,.jpg,.png,.gif",
        init: function () {
            var myDropzone = this;
            $('#submit-btn').on("click", function (e) {
                console.log($("#product-form").serialize());
                e.preventDefault();
                if ( $("#product-form").valid() ) {
                    tinyMCE.triggerSave();
                    if(myDropzone.getAcceptedFiles().length) {
                        myDropzone.processQueue();
                    }
                    else {
                        $.ajax({
                            type:'POST',
                            url:updateurls,
                            data: $("#product-form").serialize(),
                            success:function(response){
                                //console.log(response);
                                location.reload();
                            },
                            error:function(response) {
                                //console.log(response);
                              if(response.responseJSON.errors.name) {
                                  $("#name-error").text(response.responseJSON.errors.name);
                              }
                              else if(response.responseJSON.errors.code) {
                                  $("#code-error").text(response.responseJSON.errors.code);
                              }
                            },
                        });
                    }
                }
            });

            this.on('sending', function (file, xhr, formData) {
                // Append all form inputs to the formData Dropzone will POST
                var data = $("#product-form").serializeArray();
                $.each(data, function (key, el) {
                    formData.append(el.name, el.value);
                });
            });
        },
        error: function (file, response) {
            console.log(response);
        },
        successmultiple: function (file, response) {
            location.reload();           
            //console.log('sss: '+ response);
        },
        completemultiple: function (file, response) {
            console.log(file, response, "completemultiple");
        },
        reset: function () {
            console.log("resetFiles");
            this.removeAllFiles(true);
        }
    });

    //validator

    jQuery.validator.setDefaults({
        errorPlacement: function (error, element) {
            if(error.html() == 'Select Category...')
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
@endpush
