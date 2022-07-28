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
use App\User;
use App\PosSetting;
use App\Warehouse;
use App\Account;
use App\Supplier;
use App\Brand;
use App\Category;
use App\Unit;

class StockListDataTable extends DataTable
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
        ->addColumn("stock_qty", function($query){

                $qty = StockIn::where("product_id", $query->product_id)->Where("is_active", 1)->sum("stock_qty");

                if ($qty > 0) {
                    return $qty;
                }
                return '-';
            })
        ->rawColumns(["detail"]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\App\MasterProduct $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(MasterProduct $model)
    {
        return $model->newQuery()

                     ->leftjoin(Brand::getTableName()." as brand", "brand.id", MasterProduct::getTableName().".product_brand")

                     ->leftjoin(Category::getTableName()." as category", "category.id", MasterProduct::getTableName().".product_category")

                     ->select(
                                MasterProduct::getTableName().".product_id",
                                MasterProduct::getTableName().".product_sku",
                                MasterProduct::getTableName().".product_upc",
                                MasterProduct::getTableName().".product_name",
                                MasterProduct::getTableName().".product_suggested_price",
                                MasterProduct::getTableName().".product_min_price",
                                MasterProduct::getTableName().".product_cost",
                                MasterProduct::getTableName().".product_image",
                                MasterProduct::getTableName().".is_active",
                                MasterProduct::getTableName().".sn_input_type",
                                MasterProduct::getTableName().".is_sn",
                                "brand.title",
                                "category.name"


                              )

                     ->where(MasterProduct::getTableName().".is_active", 1);
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
            "detail" => ["orderable" => false],
            "SKU" => [ "data" => "product_sku", "name" => "product_sku" ],
            "UPC" => [ "data" => "product_upc", "name" => "brand.product_upc" ],
            "product_name" => [ "data" => "product_name", "name" => "product_name" ],
            "stock_qty" => ["orderable" => false]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'StockList_' . date('YmdHis');
    }
}
