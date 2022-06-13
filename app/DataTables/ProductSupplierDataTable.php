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
use App\Supplier;

class ProductSupplierDataTable extends DataTable
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

        ->addColumn('action', 'ProductMaster.tables.button_action_supplier')
        ->rawColumns(["action"]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\App\MasterProductSupplier $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(MasterProductSupplier $model)
    {
        $query = $model->newQuery()
                     ->join(Supplier::getTableName()." as supp", "supp.id", MasterProductSupplier::getTableName().".supplier_id",)
                     ->select(
                                MasterProductSupplier::getTableName().".product_supplier_id",
                                MasterProductSupplier::getTableName().".product_id",
                                MasterProductSupplier::getTableName().".supplier_moq",
                                MasterProductSupplier::getTableName().".supplier_price",
                                MasterProductSupplier::getTableName().".is_active",
                                "supp.name as supplier_name"


                              )

                     ->where(MasterProductSupplier::getTableName().".is_active", 1);

        if(!is_null($this->id)){

            $query->where(MasterProductSupplier::getTableName().".product_id", $this->id);

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
            "supplier_moq",
            "supplier_name"
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
