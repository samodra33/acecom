@extends('layout.main') 

@section('content')
@if(session()->has('create_message'))
    <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('create_message') }}</div>
@endif
@if(session()->has('edit_message'))
    <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('edit_message') }}</div>
@endif
@if(session()->has('import_message'))
    <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('import_message') }}</div>
@endif
@if(session()->has('not_permitted'))
    <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
@if(session()->has('message'))
    <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif


<section>
	
    <div class="container-fluid">
        @if(in_array("products-add", $all_permission))
            <a href="{{route('mProduct.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> {{__('file.add_product')}}</a>
            <!--<a href="#" data-toggle="modal" data-target="#importProduct" class="btn btn-primary"><i class="dripicons-copy"></i> {{__('file.import_product')}}</a>-->
        @endif
    </div>

    <div class="container-fluid">
        <div class="card">

            <div class="card-header mt-2">
                <h3 class="text-center">{{trans('file.Product Search')}}</h3>
            </div>

            <div class="card-body">
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

                <div class="col-md-4">
                  <div class="mt-3">
                    <label class="control-label">Product UPC</label>
                    {{ Form::text("product_upc", null, array("class"=>"form-control", "placeholder"=>"Product UPC")) }}
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="mt-3">
                    <label class="control-label">Brand</label>
                    {{ Form::select("brand", $brand_lists, null, array("class"=>"form-control select2_picker", "placeholder"=>"Select")) }}
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="mt-3">
                    <label class="control-label">Category</label>
                    {{ Form::select("category", $category_lists, null, array("class"=>"form-control select2_picker", "placeholder"=>"Select")) }}
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
        <div class="mt-3">
          <div>
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <strong class="card-title">Product List</strong>
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

@include('ProductMaster.modals.view_product')

@endsection

@push('scripts')
<script>
    $("ul#product").siblings('a').attr('aria-expanded','true');
    $("ul#product").addClass("show");
    $("ul#product #product-list-menu").addClass("active");

    $(document).on('keypress',function(e) {
      if(e.which == 13) {
        $("button[name='search_act']").click();
      }
    });

    $("button[name='search_act']").on("click", function(){
      $('#product-table')
      .on('preXhr.dt', function ( e, settings, data ) {
        data.product_name = $("input[name='product_name']").val();
        data.product_sku = $("input[name='product_sku']").val();
        data.product_upc = $("input[name='product_upc']").val();
        data.brand = $("select[name='brand']").val();
        data.category = $("select[name='category']").val();
      });
      window.LaravelDataTables["product-table"].draw();
    });

</script>
@endpush