<!-- Create Modal -->
<div id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        {!! Form::open(['route' => 'category.store', 'method' => 'post', 'files' => true]) !!}
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Add Category')}}</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
          <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>{{trans('file.name')}} *</label>
                    {{Form::text('name',null,array('required' => 'required', 'class' => 'form-control', 'placeholder' => 'Type category name...'))}}
                </div>
                <div class="col-md-6 form-group">
                    <label>{{trans('file.Image')}}</label>
                    <input type="file" name="image" class="form-control">
                </div>
                <!--<div class="col-md-6 form-group">
                    <label>{{trans('file.Parent Category')}}</label>
                    {{Form::select('parent_id', $lims_categories, null, ['class' => 'form-control','placeholder' => 'No Parent Category'])}}
                </div>-->
            </div>

            <div class="form-group">
              <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
            </div>
        </div>
        {{ Form::close() }}
      </div>
    </div>
</div>
