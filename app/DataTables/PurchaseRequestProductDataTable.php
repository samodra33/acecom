<?php

namespace App\DataTables;

use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestProduct;
use App\Models\MasterProduct;
use App\Models\MasterProductSupplier;
use App\Brand;
use App\Category;
use App\Supplier;
use App\User;
use App\Unit;

class PurchaseRequestProductDataTable extends DataTable
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

        ->addColumn('action', 'purchase_request.tables.button_product_action')
        ->rawColumns(["action"]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\App\PurchaseRequest $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PurchaseRequestProduct $model)
    {
        $query = $model->newQuery()
                        ->leftjoin(PurchaseRequest::getTableName()." as pr", "pr.pr_id", PurchaseRequestProduct::getTableName().".pr_id")
                        ->leftjoin(MasterProduct::getTableName()." as prod", "prod.product_id", PurchaseRequestProduct::getTableName().".product_id")
                        ->leftjoin(Brand::getTableName()." as brand", "brand.id", "prod.product_brand")

                        ->leftjoin(Category::getTableName()." as category", "category.id", "prod.product_category")
                        ->leftjoin(Supplier::getTableName()." as supp", "supp.id", PurchaseRequestProduct::getTableName().".supplier_id")
                        ->leftjoin(Unit::getTableName()." as unit", "unit.id", PurchaseRequestProduct::getTableName().".product_purchase_unit")
                        ->select(
                                    PurchaseRequestProduct::getTableName().".pr_product_id",
                                    PurchaseRequestProduct::getTableName().".product_qty",
                                    PurchaseRequestProduct::getTableName().".product_price",
                                    "pr.is_approve",
                                    "prod.product_sku",
                                    "prod.product_upc",
                                    "prod.product_name",
                                    "brand.title",
                                    "supp.name",
                                    "unit.unit_code"
                        )
                        ->where(PurchaseRequestProduct::getTableName().".is_active", 1);
        
        if(!is_null($this->id)){
            
            $query->where(PurchaseRequestProduct::getTableName().".pr_id", $this->id);
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
                    ->setTableId('pr-product-table')
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
        return 'PurchaseRequestProduct_' . date('YmdHis');
    }
}
