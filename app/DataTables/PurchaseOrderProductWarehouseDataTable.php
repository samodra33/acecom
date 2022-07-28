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
use App\Brand;
use App\Category;
use App\Supplier;
use App\User;
use App\Unit;
use App\Warehouse;

class PurchaseOrderProductWarehouseDataTable extends DataTable
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
            ->addColumn('action', function($query){

                return '
                    <div class="btn-group">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeWarehouse('.$query->po_warehouse_id.')">
                    <i class="fa fa-trash fa-fw"></i>
                    </button>
                    </div>
                ';

            })

            ->addColumn('qty', function($query){

                $form = '<div class="input-group" style="width: 100px;">

                <input id="warehouse_qty'.$query->id.'" name="warehouse_qty'.$query->id.'" style="width: 100px; text-align:center;" 
                value="'.$query->warehouse_qty.'" 

                data-ref="'.$query->po_warehouse_id.'"
                data-pop="'.$query->po_product_id.'"

                onchange="updateWarehouse(this);"

                />

                </div>';

                return $form;

            })

            ->rawColumns(["action", "qty"]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\App\PurchaseOrderProductWarehouse $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PurchaseOrderProductWarehouse $model)
    {
        $query = $model->newQuery()

                     ->leftjoin(PurchaseOrderProduct::getTableName()." as pod", "pod.po_product_id", PurchaseOrderProductWarehouse::getTableName().".po_product_id")

                     ->leftjoin(Warehouse::getTableName()." as warehouse", "warehouse.id", PurchaseOrderProductWarehouse::getTableName().".warehouse_id")

                     ->select(
                                PurchaseOrderProductWarehouse::getTableName().".po_warehouse_id",
                                PurchaseOrderProductWarehouse::getTableName().".po_product_id",
                                PurchaseOrderProductWarehouse::getTableName().".warehouse_qty",
                                "warehouse.name as warehouse_name"


                              )

                     ->where(PurchaseOrderProductWarehouse::getTableName().".is_active", 1);

        if(!is_null($this->id)){
            
            $query->where(PurchaseOrderProductWarehouse::getTableName().".po_product_id", $this->id);
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
                    ->setTableId('product-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->parameters([ 'responsive' => true, 'autoWidth' => false ])
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
            "warehouse_name",
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'PO_warehouse_' . date('YmdHis');
    }
}
