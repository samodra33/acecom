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
use App\Models\StockIn;
use App\Models\SerialNumber;
use App\User;
use App\PosSetting;
use App\Warehouse;
use App\Account;
use App\Supplier;
use App\Brand;
use App\Category;
use App\Unit;

class SerialNumberListDataTable extends DataTable
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
        ->addColumn('detail', 'stock_management.tables.button_action')
        ->rawColumns(["detail"]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\App\SerialNumber $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(SerialNumber $model)
    {
        $query = $model->newQuery()

                     ->leftjoin(GrnProduct::getTableName()." as gProd", "gProd.grn_product_id", SerialNumber::getTableName().".type_reff")

                     ->leftjoin(MasterProduct::getTableName()." as prod", "prod.product_id", "gProd.product_id") 

                     ->leftjoin(Brand::getTableName()." as brand", "brand.id", "prod.product_brand")

                     ->leftjoin(Warehouse::getTableName()." as warehouse", "warehouse.id", "gProd.warehouse_id")

                     ->select(
                                SerialNumber::getTableName().".serial_number_id",
                                SerialNumber::getTableName().".serial_number",
                                "prod.product_id",
                                "prod.product_name",
                                "prod.product_sku",
                                "prod.product_upc",
                                "brand.id as brand_id",
                                "brand.title as brand_name",
                                "warehouse.name as warehouse_name"


                              )

                     ->where(SerialNumber::getTableName().".is_active", 1);

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
                    ->setTableId('stock-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->parameters([ 
                                    'responsive' => true, 
                                    'autoWidth' => false, 
                                    'searching' => false  
                                ])
                    ->orderBy(2);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            "detail" => ["orderable" => false],
            "serial_number",
            "product_sku",
            "product_upc",
            "product_name",
            "Brand"  => [ "data" => "brand_name", "name" => "brand.title" ],
            "Warehouse"  => [ "data" => "warehouse_name", "name" => "warehouse.name" ],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'SerialNumber_' . date('YmdHis');
    }
}
