@extends('layout.main')
@section('content')
@if($errors->has('name'))
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('name') }}</div>
@endif
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif

<section>
    <div class="container-fluid">
        <a href="#" data-toggle="modal" data-target="#createModal" class="btn btn-info"><i class="dripicons-plus"></i> {{trans('file.Add Warehouse')}}</a>
        <!--<a href="#" data-toggle="modal" data-target="#importWarehouse" class="btn btn-primary"><i class="dripicons-copy"></i> {{trans('file.Import Warehouse')}}</a>-->
    </div>
    <div class="table-responsive">
        <table id="warehouse-table" class="table">
            <thead>
                <tr>
                    <!--<th class="not-exported"></th>-->
                    <th class="not-exported">{{trans('file.action')}}</th>
                    <th>{{trans('file.Warehouse')}}</th>
                    <th>{{trans('file.outlet')}}</th>
                    <th>{{trans('file.type')}}</th>
                    <th>{{trans('file.Phone Number')}}</th>
                    <th>{{trans('file.Email')}}</th>
                    <th>{{trans('file.Address')}}</th>
                    <!--<th>{{trans('file.Number of Product')}}</th>
                    <th>{{trans('file.Stock Quantity')}}</th>-->
                    
                </tr>
            </thead>
            <tbody>
                @foreach($lims_warehouse_all as $key=>$warehouse)
                <?php
                    /*
                    $number_of_product = App\Product_Warehouse::
                    join('products', 'product_warehouse.product_id', '=', 'products.id')
                    ->where([ ['product_warehouse.warehouse_id', $warehouse->id],
                              ['products.is_active', true]
                    ])->count();

                    $stock_qty = App\Product_Warehouse::
                    join('products', 'product_warehouse.product_id', '=', 'products.id')
                    ->where([ ['product_warehouse.warehouse_id', $warehouse->id],
                              ['products.is_active', true]
                    ])->sum('product_warehouse.qty');
                    */
                ?>
                <tr data-id="{{$warehouse->id}}">
                    <!--<td>{{$key}}</td>-->
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans('file.action')}}
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                	<button type="button" data-id="{{$warehouse->id}}" class="open-EditWarehouseDialog btn btn-link" data-toggle="modal" data-target="#editModal"><i class="dripicons-document-edit"></i> {{trans('file.edit')}}
                                </button>
                                </li>
                                <li class="divider"></li>
                                {{ Form::open(['route' => ['warehouse.destroy', $warehouse->id], 'method' => 'DELETE'] ) }}
                                <li>
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{trans('file.delete')}}</button>
                                </li>
                                {{ Form::close() }}
                            </ul>
                        </div>
                    </td>

                    <td>{{ $warehouse->name }}</td>
                    <td>{{ $warehouse->outlet_name }}</td>
                    @if($warehouse->is_hq === 1)
                    <td>HQ</td>
                    @else
                    <td>Outlet</td>
                    @endif
                    <td>{{ $warehouse->phone}}</td>
                    <td>{{ $warehouse->email}}</td>
                    <td>{{ $warehouse->address}}</td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@include('warehouse.modals.create')
@include('warehouse.modals.edit')


@endsection

@push('scripts')
<script type="text/javascript">

    $("ul#setting").siblings('a').attr('aria-expanded','true');
    $("ul#setting").addClass("show");
    $("ul#setting #warehouse-menu").addClass("active");

    var warehouse_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

  function confirmDelete() {
      if (confirm("Are you sure want to delete?")) {
          return true;
      }
      return false;
  }

	$(document).ready(function() {

	    $(document).on('click', '.open-EditWarehouseDialog', function() {
	        var url = "warehouse/"
	        var id = $(this).data('id').toString();
	        url = url.concat(id).concat("/edit");

	        $.get(url, function(data) {
	            $("#editModal input[name='name']").val(data['name']);
                $("#editModal input[name='outlet_name']").val(data['outlet_name']);
	            $("#editModal input[name='phone']").val(data['phone']);
	            $("#editModal input[name='email']").val(data['email']);
	            $("#editModal textarea[name='address']").val(data['address']);
                $("#editModal textarea[name='description']").val(data['description']);
	            $("#editModal input[name='warehouse_id']").val(data['id']);

                $("#editModal input[name='is_hq']").prop( "checked", false );

                if (data['is_hq'] == 1) {

                    $("#editModal input[name='is_hq']").prop( "checked", true );
                }

	        });
	    });
  });

  $('#warehouse-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{trans("file.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0]
            },

            /*
            {
                'render': function(data, type, row, meta){
                    if(type === 'display'){
                        data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                    }

                   return data;
                },
                'checkboxes': {
                   'selectRow': true,
                   'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                },
                'targets': [0]
            }
            */
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],

        /*
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
            },
            {
                extend: 'csv',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
            },
            {
                text: '<i title="delete" class="dripicons-cross"></i>',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                    if(user_verified == '1') {
                        warehouse_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                warehouse_id[i-1] = $(this).closest('tr').data('id');
                            }
                        });
                        if(warehouse_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'warehouse/deletebyselection',
                                data:{
                                    warehouseIdArray: warehouse_id
                                },
                                success:function(data){
                                    alert(data);
                                }
                            });
                            dt.rows({ page: 'current', selected: true }).remove().draw(false);
                        }
                        else if(!warehouse_id.length)
                            alert('No warehouse is selected!');
                    }
                    else
                        alert('This feature is disable for demo!');
                }
            },
            {
                extend: 'colvis',
                columns: ':gt(0)'
            },
        ],

        */
    } );

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$( "#select_all" ).on( "change", function() {
    if ($(this).is(':checked')) {
        $("tbody input[type='checkbox']").prop('checked', true);
    }
    else {
        $("tbody input[type='checkbox']").prop('checked', false);
    }
});

$("#export").on("click", function(e){
    e.preventDefault();
    var warehouse = [];
    $(':checkbox:checked').each(function(i){
      warehouse[i] = $(this).val();
    });
    $.ajax({
       type:'POST',
       url:'/exportwarehouse',
       data:{

            warehouseArray: warehouse
        },
       success:function(data){
        alert('Exported to CSV file successfully! Click Ok to download file');
        window.location.href = data;
       }
    });
});
</script>
@endpush
