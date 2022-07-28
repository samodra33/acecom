<div class="modal fade" id="import_serial_number" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scrollmodalLabel">Serial Number</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    {{ Form::hidden("import_grn_product_id", null) }}
                    <div class="card-header">
                        <strong class="card-title">Import Serial Number</strong>
                    </div>
                    <div class="card-body">

                        <div class="col-md-12">

                            <div class="row form-group">
                                <div class="col-md-4">
                                    <label class="control-label">File <span style="color:red;">*</span> </label>
                                </div>
                                <div class="col-md-8">
                                     <input type="file" name="fileupload" id="fileupload" class="form-control" />
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col-md-4">
                                </div>
                                <div class="col-md-8">
                                   <a href="{{ url(route('importsn.formatexcel')) }}" class="btn btn-outline-secondary">
                                        Download Excel Format
                                    </a>
                                </div>
                            </div>

                        </div>

                        <div class="mt-3 col-md-12">

                            <button type="button" class="btn btn-primary" id="btn_import_sn">
                                Import
                            </button>

                        </div>
                    </div>
                </div>
                <div class="form-group">

                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script type="text/javascript">

    $(document).ready(function(){
             
    });


    @if(!request()->route()->named("grn.create"))

        function getGrnProductId(id)
        {
            $('input[name="import_grn_product_id"]').val( id );
        }

        $("#btn_import_sn").click(function(){

            event.preventDefault();

            var urls = '{{ route("stock.service.import_sn_product") }}';

            var grn_product_id = $('input[name="import_grn_product_id"]').val();
            const fileupload = $('#fileupload').prop('files')[0];

            //////////////////////////////////validation file

            var exten = $("#fileupload").val().split('.').pop().toLowerCase();

            if (jQuery.inArray(exten, ['csv']) == -1) {
                alert("Please upload a CSV file");
                $("#fileupload").val(null);
                return false;
            }

            /////////////////////////////////////ajax

            if (grn_product_id!="" && fileupload!="" && fileupload!=undefined) {

                let formData = new FormData();
                formData.append('fileupload', fileupload);
                formData.append('type_reff', grn_product_id);
                formData.append('typeDoc', "GRN");

                $.ajax({
                    type: 'POST',
                    url: urls,
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        
                        alert(response);
                        $("#fileupload").val(null);
                        $('#import_serial_number').modal('toggle');
                    },
                    error: function () {
                        alert("Something Wrong (controller) !");
                        $("#fileupload").val(null);
                    }
                });

            }
            else{

                alert("Check your input !");
                $("#fileupload").val(null);
            }
        });

    @endif

</script>

@endpush