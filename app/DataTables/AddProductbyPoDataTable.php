<?php

namespace App\DataTables;

use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProduct;
use App\Models\PurchaseOrderProductWarehouse;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestProduct;
use App\Models\MasterProduct;
use App\Models\MasterProductSupplier;
use App\Models\GrnProduct;
use App\Brand;
use App\Category;
use App\Supplier;
use App\User;
use App\Unit;
use App\Warehouse;

class AddProductbyPoDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)

        ->addColumn("qty_input", function($query){

                $unreceivedQty = $this->unreceivedQtyByPoWarehouse($query->po_warehouse_id, $query->po_qty);

                return "<input name='add_part_qty_".$query->po_warehouse_id."' 

                        data-po_warehouse_id='".$query->po_warehouse_id."'' 
                        data-unreceived='".$unreceivedQty."'' 

                        style='text-align:center;' size='5' value='".$unreceivedQty."' onchange='checkQty(this);'/>";
            })

        ->addColumn("unreceived_qty", function($query){

                $unreceivedQty = $this->unreceivedQtyByPoWarehouse($query->po_warehouse_id, $query->po_qty);

                return $unreceivedQty;
            })

        ->addColumn("status_prod", function($query){

                if ($query->status == 1) {

                    return "Unreceived";
                }

                if ($query->status == 2) {

                    return "Partial Received";
                }

                if ($query->status == 3) {

                    return "Received";
                }

                return "Something wrong";
            })

        ->addColumn("#", function($query){
                return '<input type="checkbox" name="selected_product[]" value="'. $query->po_warehouse_id .'"/>';
            })

        ->filter(function ($query) {

            if ( request()->has('warehouse_id') && !empty(request('warehouse_id')) ) {
                $query->where("warehouse.id", request('warehouse_id'));
            }

            if ( request()->has('po_no') && !empty(request('po_no')) ) {
                $query->where("po.po_id", request('po_no'));
            }

            if (request()->has('product_name') && !empty(request('product_name')) ) {
                $query->where("prod.product_name", "LIKE", "%".request('product_name')."%");
            }

            if (request()->has('product_sku') && !empty(request('product_sku')) ) {
                $query->where("prod.product_sku", "LIKE", "%".request('product_sku')."%");
            }

            if ( empty(request('warehouse_id')) && empty(request('product_name')) && empty(request('product_sku')) && empty(request('po_no')) ) {

                $query->where("warehouse.id", 0);

            }

            
        })

        ->rawColumns(["#", "qty_input", "unreceived_qty"]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\App\PurchaseOrderProduct $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PurchaseOrderProduct $model)
    {


        $query = $model->newQuery()
                        ->join(PurchaseOrderProductWarehouse::getTableName()." as poWarehouse", "poWarehouse.po_product_id", PurchaseOrderProduct::getTableName().".po_product_id")
                        ->leftjoin(PurchaseOrder::getTableName()." as po", "po.po_id", PurchaseOrderProduct::getTableName().".po_id")
                        ->leftjoin(MasterProduct::getTableName()." as prod", "prod.product_id", PurchaseOrderProduct::getTableName().".product_id")
                        ->leftjoin(Brand::getTableName()." as brand", "brand.id", "prod.product_brand")

                        ->leftjoin(Category::getTableName()." as category", "category.id", "prod.product_category")
                        ->leftjoin(Supplier::getTableName()." as supp", "supp.id", PurchaseOrderProduct::getTableName().".supplier_id")
                        ->leftjoin(Unit::getTableName()." as unit", "unit.id", PurchaseOrderProduct::getTableName().".product_purchase_unit")

                        ->leftjoin(Warehouse::getTableName()." as warehouse", "warehouse.id", "poWarehouse.warehouse_id")
                        ->select(
                                    PurchaseOrderProduct::getTableName().".po_product_id",
                                    PurchaseOrderProduct::getTableName().".product_qty",
                                    PurchaseOrderProduct::getTableName().".product_price",
                                    "poWarehouse.po_warehouse_id",
                                    "poWarehouse.warehouse_qty as po_qty",
                                    "poWarehouse.status",
                                    "po.po_no",
                                    "po.is_approve",
                                    "prod.product_sku",
                                    "prod.product_upc",
                                    "prod.product_name",
                                    "brand.title",
                                    "supp.name",
                                    "unit.unit_code",
                                    "warehouse.id",
                                    "warehouse.name as warehouse_name"
                        )
                        ->where("poWarehouse.is_active", 1)
                        ->where("poWarehouse.status", "!=",  3)
                        ->where("po.is_active", 1)
                        ->where("po.is_approve", 1)
                        ->where(PurchaseOrderProduct::getTableName().".is_active", 1);

        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('po-product-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->parameters([ 
                                    'responsive' => true, 
                                    'autoWidth' => false, 
                                    'searching' => false  
                                ])
                    ->orderBy(1);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            "action" => ["orderable" => false],
            "product_sku",
            "product_upc",
            "product_name",
            "Brand" => [ "data" => "title", "name" => "brand.title" ],
            "Supplier" => [ "data" => "name", "name" => "supp.name" ],
            "Qty" => [ "data" => "product_qty", "name" => "product_qty" ],
            "unit" => [ "data" => "unit_code", "name" => "unit.unit_code" ],
            "Price" => [ "data" => "product_price", "name" => "product_price" ],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'AddProductbyPo_' . date('YmdHis');
    }

    public function unreceivedQtyByPoWarehouse($id, $po_qty)
    {
        $grnQty = GrnProduct::where("is_active", 1)
                            ->where("po_warehouse_id", $id)
                            ->sum("product_qty");


        return $po_qty - $grnQty;

    }
}
