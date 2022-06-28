@extends('layout.main')

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.add_product')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        <form id="product-form">


                            @include('ProductMaster.field')

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

                            <!--<div id="add_product_sku_table_hidden"></div>-->
                            <div id="add_product_supplier_table_hidden"></div>

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

    $("ul#product").siblings('a').attr('aria-expanded','true');
    $("ul#product").addClass("show");
    $("ul#product #product-create-menu").addClass("active");

    $('[data-toggle="tooltip"]').tooltip();

    $('#genSkubutton').on("click", function(){
      $.get('service/gencode', function(data){
        $("input[name='product_sku']").val(data);
      });
    });

    $('#genUpcbutton').on("click", function(){
      $.get('service/gencode', function(data){
        $("input[name='product_upc']").val(data);
      });
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //table
    window.product_sku = $('#product_sku').DataTable();
    window.product_supplier = $('#product_supplier').DataTable();

    //unit

    $('select[name="unit_id"]').on('change', function() {

        unitID = $(this).val();
        if(unitID) {
            populate_unit(unitID);
        }else{
            $('select[name="sale_unit_id"]').empty();
            $('select[name="purchase_unit_id"]').empty();
        }
    });

    function populate_unit(unitID){
        $.ajax({
            url: 'saleunit/'+unitID,
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

                  $('select[name=sale_unit_id]').val(unitID);
                  $('select[name=purchase_unit_id]').val(unitID);
                  $('.selectpicker').selectpicker('refresh');
            },
        });
    }

    //radio button SN

    $("input[name=sn_input_type][value=0]").prop("checked",true);
    $("input[name=sn_input_type]").prop("disabled",true);

    $("input[name='is_sn']").on("change", function () {
        if ($(this).is(':checked')) {
            $("input[name=sn_input_type]").prop("disabled",false);
        }else{

            $("input[name=sn_input_type]").prop("disabled",true);
        }
    });


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

    //get store

    myDropzone = new Dropzone('div#imageUpload', {
        addRemoveLinks: true,
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: 100,
        maxFilesize: 12,
        paramName: 'image',
        clickable: true,
        method: 'POST',
        url: '{{route('mProduct.store')}}',
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
                e.preventDefault();
                if ( $("#product-form").valid() ) {
                    tinyMCE.triggerSave();
                    if(myDropzone.getAcceptedFiles().length) {
                        myDropzone.processQueue();
                    }
                    else {
                        $.ajax({
                            type:'POST',
                            url:'{{route('mProduct.store')}}',
                            data: $("#product-form").serialize(),
                            success:function(response){
                                //console.log(response);
                                location.href = '../mProduct';
                            },
                            error:function(response) {
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
            if(response.errors.name) {
              $("#name-error").text(response.errors.name);
              this.removeAllFiles(true);
            }
            else if(response.errors.code) {
              $("#code-error").text(response.errors.code);
              this.removeAllFiles(true);
            }
            else {
              try {
                  var res = JSON.parse(response);
                  if (typeof res.message !== 'undefined' && !$modal.hasClass('in')) {
                      $("#success-icon").attr("class", "fas fa-thumbs-down");
                      $("#success-text").html(res.message);
                      $modal.modal("show");
                  } else {
                      if ($.type(response) === "string")
                          var message = response; //dropzone sends it's own error messages in string
                      else
                          var message = response.message;
                      file.previewElement.classList.add("dz-error");
                      _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
                      _results = [];
                      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                          node = _ref[_i];
                          _results.push(node.textContent = message);
                      }
                      return _results;
                  }
              } catch (error) {
                  console.log(error);
              }
            }
        },
        successmultiple: function (file, response) {
            location.href = '../mProduct';
            //console.log(response);
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
