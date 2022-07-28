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
use App\Supplier;
use App\User;

class GrnDataTable extends DataTable
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
        ->addColumn('action', 'grn.tables.button_action')
        ->rawColumns(["action"]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\App\Grn $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Grn $model)
    {
        return $model->newQuery()
                    ->select(
                                Grn::getTableName().".grn_id",
                                Grn::getTableName().".grn_no",
                                Grn::getTableName().".supplier_do_no",
                                Grn::getTableName().".grn_date",
                                Grn::getTableName().".grn_remark",
                                Grn::getTableName().".is_approve",
                                Grn::getTableName().".created_by"

                     )
                    ->where(Grn::getTableName().".is_active", 1);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('grn-table')
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
            "grn_no"
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
