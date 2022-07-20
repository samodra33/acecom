<?php

namespace App\DataTables;

use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestProduct;
use App\Models\PurchaseType;
use App\Models\Grn;
use App\Models\GrnProduct;
use App\Models\MasterProduct;
use App\User;
use App\PosSetting;
use App\Warehouse;
use App\Account;
use App\Supplier;
use App\Brand;
use App\Category;
use App\Unit;

class GrnProductDataTable extends DataTable
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
        ->addColumn('action', 'grn.tables.button_product_action')
        ->rawColumns(["action"]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\App\GrnProduct $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(GrnProduct $model)
    {
        $query = $model->newQuery()
                    ->leftjoin(Grn::getTableName()." as grn", "grn.grn_id", GrnProduct::getTableName().".grn_id")
                    ->leftjoin(MasterProduct::getTableName()." as prod", "prod.product_id", GrnProduct::getTableName().".product_id") 
                    ->leftjoin(Brand::getTableName()." as brand", "brand.id", "prod.product_brand")
                    ->leftjoin(Supplier::getTableName()." as supp", "supp.id", GrnProduct::getTableName().".supplier_id")
                    ->leftjoin(Warehouse::getTableName()." as warehouse", "warehouse.id", GrnProduct::getTableName().".warehouse_id")
                    ->leftjoin(Unit::getTableName()." as pUnit", "pUnit.id", GrnProduct::getTableName().".unit_id")
                    ->select(
                                GrnProduct::getTableName().".grn_product_id",
                                GrnProduct::getTableName().".product_qty",
                                GrnProduct::getTableName().".product_price",
                                "grn.is_approve",
                                "prod.product_id",
                                "prod.product_name",
                                "prod.product_sku",
                                "prod.product_upc",
                                "brand.id as brand_id",
                                "brand.title as brand_name",
                                "supp.name as supplier_name",
                                "supp.id as supplier_id",
                                "warehouse.name as warehouse_name",
                                "pUnit.unit_code as prchsunit",
                                "pUnit.id as prchsunitId"

                     )
                    ->where(GrnProduct::getTableName().".is_active", 1);

        if(!is_null($this->id)){
            
            $query->where(GrnProduct::getTableName().".grn_id", $this->id);
        }

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
                    ->setTableId('grn-product-table')
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
            "Brand"  => [ "data" => "brand_name", "name" => "brand.title" ],
            "Supplier"  => [ "data" => "supplier_name", "name" => "supp.name" ],
            "Warehouse"  => [ "data" => "warehouse_name", "name" => "warehouse.name" ],
            "Qty"  => [ "data" => "product_qty", "name" => "product_qty" ],
            "Unit"  => [ "data" => "prchsunit", "name" => "pUnit.unit_code" ],
            "Price"  => [ "data" => "product_price", "name" => "product_price" ]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'GRN_' . date('YmdHis');
    }
}
