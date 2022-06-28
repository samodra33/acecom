<div class="modal fade" id="product-details" tabindex="-1" role="dialog" aria-labelledby="scrollmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scrollmodalLabel">{{trans('Product Details')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header">
                        <span id="product_name"><strong class="card-title"></strong></span>
                    </div>
                    <div class="card-body">

                        <div class="col-md-12">

                            <div id="slide-img" style="margin-bottom: 100px;">

                            </div>

                            <div class="row form-group">
                                <div class="col-md-4">
                                    <label class="control-label">SKU</label>
                                </div>
                                <div class="col-md-8">
                                    <span id="product_sku"></span>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col-md-4">
                                    <label class="control-label">UPC</label>
                                </div>
                                <div class="col-md-8">
                                    <span id="product_upc"></span>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col-md-4">
                                    <label class="control-label">Brand</label>
                                </div>
                                <div class="col-md-8">
                                    <span id="product_brand"></span>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col-md-4">
                                    <label class="control-label">Category</label>
                                </div>
                                <div class="col-md-8">
                                    <span id="product_category"></span>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col-md-4">
                                    <label class="control-label">Suggested Selling Price</label>
                                </div>
                                <div class="col-md-8">
                                    <span id="product_suggested_price"></span>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col-md-4">
                                    <label class="control-label">Min Selling Price</label>
                                </div>
                                <div class="col-md-8">
                                    <span id="product_min_price"></span>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col-md-4">
                                    <label class="control-label">Cost</label>
                                </div>
                                <div class="col-md-8">
                                    <span id="product_cost"></span>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col-md-4">
                                    <label class="control-label">Alert Qty</label>
                                </div>
                                <div class="col-md-8">
                                    <span id="product_alert"></span>
                                </div>
                            </div>

                            <div class="row form-group">
                                <div class="col-md-4">
                                    <label class="control-label">Detail</label>
                                </div>
                                <div class="col-md-8">
                                    <span id="product_detail"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="product_supplier">
                                    <thead>
                                        <tr>
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
                <div class="form-group">

                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    var currentIndex = 1;

    function getProductDetail(id)
    {   
        var table = $('#product_supplier').DataTable();
        table.destroy();
        $( ".images-slideshow" ).remove();

        var urls = '{{route("mProduct.service.find_product", ":param")}}';
        urls = urls.replace(':param', id);

        var data = {
            "product_id" : id
        }

            $.ajax({

                type:'GET',
                url:urls,
                data: data,

                success:function(response){

                    const product_image = response.product_image.split(",");
                    var length = product_image.length;
                    var img = ''; 
                    var dot = '';   

                    $("#product_name").text(response.product_name);
                    $("#product_sku").text(response.product_sku);
                    $("#product_upc").text(response.product_upc);
                    $("#product_brand").text(response.Brand);
                    $("#product_category").text(response.Category);
                    $("#product_suggested_price").text(response.suggested_price);
                    $("#product_min_price").text(response.min_price);
                    $("#product_cost").text(response.cost);
                    $("#product_alert").text(response.product_alert_qty);
                    $("#product_detail").text(response.product_detail);

                    //image

                    for (var i = 0; i < length; i++) {

                        img =   img+'<div class="imageSlides">'+
                                        '<img src="{{url("/images/product")}}/'+product_image[i]+'" style="width:20%; margin: auto; display: block;">'+
                                    '</div>';
                                    
                    }

                    $("#slide-img").append(

                        '<div class="images-slideshow">'+
                        img+
                        '<a class="slider-btn previous" onclick="setSlides(-1)">❮</a>'+
                        '<a class="slider-btn next" onclick="setSlides(1)">❯</a>'+
                        '</div>'
                    );

                    displaySlides(currentIndex);
                    supplierTable(response.product_id);
                    

                },
                error:function(response) {

                    alert(response)

                }
            });
    }

    //supplier table
    function supplierTable(id) {

        $('#product_supplier').DataTable({

            processing: true,
            serverSide: true,
            bInfo: false,
            bPaginate: true,
            bAutoWidth: false, 
            ajax: {
              url : '{{ route("mProduct.service.supplierProd_table") }}',
              data : {
                product_id : id
              }
            },
            columns: [
            {data: 'supplier_moq', name: 'supplier_moq'},
            {data: 'supplier_name', name: 'supplier_name'},
            {data: 'supplier_price', name: 'supplier_price'}
            ],
            order: [[2, 'desc']]
        });

    }

    //slide img

    function setSlides(num) {
        displaySlides(currentIndex += num);
    }

    function displaySlides(num) {
        var x;
        var slides = document.getElementsByClassName("imageSlides");
        if (num > slides.length) { currentIndex = 1 }
        if (num < 1) { currentIndex = slides.length }
        for (x = 0; x < slides.length; x++) {
            slides[x].style.display = "none";
        }
        slides[currentIndex - 1].style.display = "block";
    }

</script>