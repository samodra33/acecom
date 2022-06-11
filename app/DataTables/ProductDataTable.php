<?php

namespace App\DataTables;

use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use App\Models\MasterProduct;
use App\Models\MasterProductSku;
use App\Models\MasterProductSupplier;

class ProductDataTable extends DataTable
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
        ->addColumn("#", function($query){

            $product_name = $query->product_name;

            return "<button type='button' class='btn btn-link' data-ref='".$query->product_id."' onclick=selectPart(this)>
            ".$product_name."
            </button>";
        })
        ->addColumn('action', 'ProductMaster.tables.button_action')
        ->rawColumns(["#", "action"]);
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

                     ->select(
                                MasterProduct::getTableName().".product_id",
                                MasterProduct::getTableName().".product_code",
                                MasterProduct::getTableName().".product_name",
                                MasterProduct::getTableName().".product_selling_price",
                                MasterProduct::getTableName().".product_image",
                                MasterProduct::getTableName().".is_active"


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
                    ->setTableId('product-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
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
            "action",
            "product_code",
            "product_name"
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Product_' . date('YmdHis');
    }
}
