<div class="row">

    <div class="col-md-4">
        <div class="form-group">
            <label>{{trans('file.Product Name')}} <strong>*</strong> </label>

            {{ Form::text("product_name", isset($product->product_name)?$product->product_name:null, array("class"=>"form-control", "placeholder"=>"Product Name", "required"=>"required")) }}

            <span class="validation-msg" id="name-error"></span>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{trans('file.Product Code')}} <strong>*</strong> </label>
            <div class="input-group">
                {{ Form::text("product_code", isset($product->product_code)?$product->product_code:null, array("class"=>"form-control", "placeholder"=>"Product Code", "required"=>"required")) }}
                <div class="input-group-append">
                    <button id="genbutton" type="button" class="btn btn-sm btn-default" title="{{trans('file.Generate')}}"><i class="fa fa-refresh"></i></button>
                </div>
            </div>
            <span class="validation-msg" id="code-error"></span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>{{trans('file.Brand')}} <strong>*</strong> </label>
            <div class="input-group">

                {{ Form::select("brand_id", $brand_lists, isset($product->product_brand)?$product->product_brand:null, array("class"=>"form-control selectpicker", "title"=>"Select", "data-live-search"=>"true", "data-live-search-style"=>"begins")) }}

            </div>
            <span class="validation-msg"></span>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>{{trans('file.category')}} *</strong> </label>
            <div class="input-group">

                {{ Form::select("category_id", $category_lists, isset($product->product_category)?$product->product_category:null, array("class"=>"form-control selectpicker", "title"=>"Select", "data-live-search"=>"true", "data-live-search-style"=>"begins")) }}

            </div>
            <span class="validation-msg"></span>
        </div>
    </div>

    <div id="unit" class="col-md-12">
        <div class="row ">
            <div class="col-md-4 form-group">
                <label>{{trans('file.Product Unit')}} *</strong> </label>
                <div class="input-group">
                    {{ Form::select("unit_id", $unit_lists, isset($product->product_unit)?$product->product_unit:null, array("class"=>"form-control selectpicker", "title"=>"Select", "data-live-search"=>"true", "data-live-search-style"=>"begins")) }}
                </div>
                <span class="validation-msg"></span>
            </div>
            <div class="col-md-4">
                <label>{{trans('file.Sale Unit')}}</strong> </label>
                <div class="input-group">
                    {{ Form::select("sale_unit_id", [], isset($product->product_sale_unit)?$product->product_sale_unit:null, array("class"=>"form-control selectpicker", "title"=>"Select", "data-live-search"=>"true", "data-live-search-style"=>"begins")) }}
                </div>
                <span class="validation-msg"></span>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>{{trans('file.Purchase Unit')}}</strong> </label>
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
            <label>{{trans('file.Recommendation Selling Price')}} <strong>*</strong> </label>
            {{ Form::number("price", isset($product->product_selling_price)?$product->product_selling_price:0, array("class"=>"form-control", "step"=>"any")) }}
            <span class="validation-msg"></span>
        </div>

    </div>
    <div id="alert-qty" class="col-md-4">
        <div class="form-group">
            <label>{{trans('file.Alert Quantity')}} <strong>*</strong> </label>
            {{ Form::number("alert_quantity", isset($product->product_alert_qty)?$product->product_alert_qty:0, array("class"=>"form-control", "step"=>"any")) }}
            <span class="validation-msg"></span>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mt-3">
            <input type="checkbox" name="product_featured" value="1">&nbsp;
            <label>{{trans('file.Featured')}}</label>
            <p class="italic">{{trans('file.Featured product will be displayed in POS')}}</p>
        </div>
    </div>
    <div class="col-md-12 mt-3" id="sn-option">
        <h5><input name="is_sn" type="checkbox" id="is_sn" value="1">&nbsp; {{trans('file.This product has IMEI or Serial numbers')}}</h5>
    </div>
    <div class="col-md-12" style="margin-top: 50px;">
        <div class="form-group">
            <label>{{trans('file.Product Image')}}</strong> </label> <i class="dripicons-question" data-toggle="tooltip" title="{{trans('file.You can upload multiple image. Only .jpeg, .jpg, .png, .gif file can be uploaded. First image will be base image.')}}"></i>
            <div id="imageUpload" class="dropzone"></div>
            <span class="validation-msg" id="image-error"></span>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label>{{trans('file.Product Details')}}</label>
            <textarea name="product_details" class="form-control" rows="3"></textarea>
        </div>
    </div>


</div>