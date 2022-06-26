<div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog">
    <div class="modal-content">
        {{ Form::open(['route' => ['board-product.update', 1], 'method' => 'PUT', 'files' => true] ) }}
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title"> Update Product</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="board_product_id">
        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
        <div class="form-group">
          <label>Product Name *</label>
          {{Form::text('name',null, array('required' => 'required', 'class' => 'form-control'))}}
        </div>
        <div class="form-group">
          <label>SKU *</label>
          {{Form::number('sku',null, array('required' => 'required', 'class' => 'form-control'))}}
        </div>
        <div class="form-group">
          <label>Product Price *</label>
          {{Form::number('price',null, array('required' => 'required', 'class' => 'form-control'))}}
        </div>
        <div class="form-group">
            <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
          </div>
        </div>
      {{ Form::close() }}
    </div>
  </div>
</div>