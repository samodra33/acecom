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

class ProductSkuDataTable extends DataTable
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
        ->addColumn('action', 'ProductMaster.tables.button_action_sku')
        ->rawColumns(["action"]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\App\MasterProductSku $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(MasterProductSku $model)
    {
        $query = $model->newQuery()

                     ->select(
                                MasterProductSku::getTableName().".sku_id",
                                MasterProductSku::getTableName().".product_id",
                                MasterProductSku::getTableName().".sku_no",
                                MasterProductSku::getTableName().".sku_desc",
                                MasterProductSku::getTableName().".is_active"


                              )

                     ->where(MasterProductSku::getTableName().".is_active", 1);

        if(!is_null($this->id)){

            $query->where(MasterProductSku::getTableName().".product_id", $this->id);

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
                    ->setTableId('product-sku-table')
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
            "sku_no",
            "sku_desc"
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Product_SKU_' . date('YmdHis');
    }
}
