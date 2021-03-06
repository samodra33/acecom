<div class="row">

    <div class="col-md-4">
        <div class="form-group">
            <label>{{trans('file.Product Name')}} <strong><span style="color:red;">*</span></strong> </label>

            {{ Form::text("product_name", isset($product->product_name)?$product->product_name:null, array("class"=>"form-control", "placeholder"=>"Product Name", "required"=>"required")) }}

            <span class="validation-msg" id="name-error"></span>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{trans('file.Product SKU')}} <strong><span style="color:red;">*</span></strong> </label>
            <div class="input-group">
                {{ Form::text("product_sku", isset($product->product_sku)?$product->product_sku:null, array("class"=>"form-control", "placeholder"=>"Product SKU", "required"=>"required")) }}
                <div class="input-group-append">
                    <button id="genSkubutton" type="button" class="btn btn-sm btn-default" title="{{trans('file.Generate')}}"><i class="fa fa-refresh"></i></button>
                </div>
            </div>
            <span class="validation-msg" id="sku-error"></span>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{trans('file.Product UPC')}} <strong><span style="color:red;">*</span></strong> </label>
            <div class="input-group">
                {{ Form::text("product_upc", isset($product->product_upc)?$product->product_upc:null, array("class"=>"form-control", "placeholder"=>"Product UPC", "required"=>"required")) }}
                <div class="input-group-append">
                    <button id="genUpcbutton" type="button" class="btn btn-sm btn-default" title="{{trans('file.Generate')}}"><i class="fa fa-refresh"></i></button>
                </div>
            </div>
            <span class="validation-msg" id="upc-error"></span>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{trans('file.Brand')}} <strong><span style="color:red;">*</span></strong> </label>
            <div class="input-group">

                {{ Form::select("brand_id", $brand_lists, isset($product->product_brand)?$product->product_brand:null, array("class"=>"form-control selectpicker", "title"=>"Select", "data-live-search"=>"true", "data-live-search-style"=>"begins", "required"=>"required")) }}

            </div>
            <span class="validation-msg" id="brand-error"></span>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{trans('file.category')}} <strong><span style="color:red;">*</span></strong> </label>
            <div class="input-group">

                {{ Form::select("category_id", $category_lists, isset($product->product_category)?$product->product_category:null, array("class"=>"form-control selectpicker", "title"=>"Select", "data-live-search"=>"true", "data-live-search-style"=>"begins", "required"=>"required")) }}

            </div>
            <span class="validation-msg" id="category-error"></span>
        </div>
    </div>

    <div id="unit" class="col-md-12">
        <div class="row ">
            <div class="col-md-4 form-group">
                <label>{{trans('file.Product Unit')}} <strong><span style="color:red;">*</span></strong> </label>
                <div class="input-group">
                    {{ Form::select("unit_id", $unit_lists, isset($product->product_unit)?$product->product_unit:null, array("class"=>"form-control selectpicker", "title"=>"Select", "data-live-search"=>"true", "data-live-search-style"=>"begins", "required"=>"required")) }}
                </div>
                <span class="validation-msg" id="unit-error"></span>
            </div>
            <div class="col-md-4">
                <label>{{trans('file.Sale Unit')}}</label>
                <div class="input-group">
                    {{ Form::select("sale_unit_id", [], isset($product->product_sale_unit)?$product->product_sale_unit:null, array("class"=>"form-control selectpicker", "title"=>"Select", "data-live-search"=>"true", "data-live-search-style"=>"begins")) }}
                </div>
                <span class="validation-msg"></span>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{trans('file.Purchase Unit')}} </label>
                    <div class="input-group">
                        {{ Form::select("purchase_unit_id", [], isset($product->product_purchase_unit)?$product->product_purchase_unit:null, array("class"=>"form-control selectpicker", "title"=>"Select", "data-live-search"=>"true", "data-live-search-style"=>"begins")) }}
                    </div>
                    <span class="validation-msg"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{trans('file.Suggested Selling Price')}} <strong><span style="color:red;">*</span></strong> </label>
            {{ Form::number("product_suggested_price", isset($product->product_suggested_price)?$product->product_suggested_price:0, array("class"=>"form-control", "step"=>"any", "required"=>"required")) }}
            <span class="validation-msg" id="suggested-error"></span>
        </div>

    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{trans('file.Min Selling Price')}} <strong><span style="color:red;">*</span></strong> </label>
            {{ Form::number("product_min_price", isset($product->product_min_price)?$product->product_min_price:0, array("class"=>"form-control", "step"=>"any", "required"=>"required")) }}
            <span class="validation-msg" id="min-error"></span>
        </div>

    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{trans('file.Product Cost')}} <strong><span style="color:red;">*</span></strong> </label>
            {{ Form::number("product_cost", isset($product->product_cost)?$product->product_cost:0, array("class"=>"form-control", "step"=>"any", "required"=>"required")) }}
            <span class="validation-msg" id="cost-error"></span>
        </div>

    </div>

    <div id="alert-qty" class="col-md-4">
        <div class="form-group">
            <label>{{trans('file.Alert Quantity')}} <strong><span style="color:red;">*</span></strong> </label>
            {{ Form::number("product_alert_qty", isset($product->product_alert_qty)?$product->product_alert_qty:0, array("class"=>"form-control", "step"=>"any", "required"=>"required")) }}
            <span class="validation-msg" id="alert-error"></span>
        </div>
    </div>
    <div class="col-md-12 mt-3" id="sn-option">
        <h5>
            <input name="is_sn" type="checkbox" id="is_sn" value="1"

            <?php if (isset($product->is_sn)) { if ($product->is_sn == 1) { echo 'checked'; } } ?> >

            &nbsp; {{trans('file.This product have Serial numbers')}}

        </h5>
    </div>
    <div class="col-md-12 mt-3" id="sn-option">
        <h5>
            <input name="sn_input_type" type="radio" id="sn_input_type" value="0"

            <?php if (isset($product->sn_input_type)) { if ($product->sn_input_type == 0) { echo 'checked'; } } ?> >

            &nbsp; {{trans('file.Auto Generate by System')}}

        </h5>
    </div>

    <div class="col-md-12 mt-3" id="sn-option">
        <h5>
            <input name="sn_input_type" type="radio" id="sn_input_type" value="1"

            <?php if (isset($product->sn_input_type)) { if ($product->sn_input_type == 1) { echo 'checked'; } } ?> >

            &nbsp; {{trans('file.Manual Input')}}

        </h5>
    </div>

    <div class="col-md-12" style="margin-top: 50px;">
        <div class="form-group">
            <label>{{trans('file.Product Image')}}</strong> </label> <i class="dripicons-question" data-toggle="tooltip" title="{{trans('file.You can upload multiple image. Only .jpeg, .jpg, .png, .gif file can be uploaded. First image will be base image.')}}"></i>
            <div id="imageUpload" class="dropzone"></div>
            <span class="validation-msg" id="image-error"></span>
        </div>
    </div>

    @if(request()->route()->named("mProduct.edit"))
    <div class="col-md-12">
        <div class="form-group">
            <table class="table table-hover" id="product_image">
                <thead>
                    <tr>
                        <th><button type="button" class="btn btn-sm"><i class="fa fa-list"></i></button></th>
                        <th>Image</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $images = explode(",", $product->product_image)?>
                    @foreach($images as $key => $image)

                    @if(!empty($image))
                    <tr>
                        <td><button type="button" class="btn btn-sm"><i class="fa fa-list"></i></button></i></td>
                        <td>
                            <img src="{{url('public/images/product', $image)}}" sizes="50%">
                            <input type="hidden" name="prev_img[]" value="{{$image}}">
                        </td>
                        <td><button type="button" class="btn btn-sm btn-danger remove-img">X</button></td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="col-md-12">
        <div class="form-group">
            <label>{{trans('file.Product Details')}}</label>
            {{ Form::textarea("product_details", isset($product->product_detail)?$product->product_detail:null, array("class"=>"form-control")) }}
        </div>
    </div>


</div>