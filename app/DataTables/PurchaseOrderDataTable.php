<?php

namespace App\DataTables;

use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProduct;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestProduct;
use App\Models\PurchaseType;
use App\Supplier;
use App\User;

class PurchaseOrderDataTable extends DataTable
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
        ->addColumn("status", function($query){

            if ($query->is_approve == 0) {
                return "Pending Approval";
            }
            elseif($query->is_approve == 1){

                return "Approved";

            }
            else{

                return "";
            }
        })


        ->addColumn("Created Date", function($query){

            return date_format($query->created_at,"Y/M/d");

        })

        ->addColumn("Modified Date", function($query){

            return date_format($query->updated_at,"Y/M/d");

        })

        ->addColumn('action', 'purchase_order.tables.button_action')
        ->rawColumns(["action"]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\App\PurchaseOrder $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PurchaseOrder $model)
    {
        return $model->newQuery()
                    ->leftJoin(PurchaseRequest::getTableName()." as pr", "pr.pr_id", PurchaseOrder::getTableName().".pr_id")
                    ->leftJoin(Supplier::getTableName()." as supp", "supp.id", PurchaseOrder::getTableName().".po_supplier")
                    ->leftJoin(PurchaseType::getTableName()." as type", "type.type_id", PurchaseOrder::getTableName().".po_type")
                    ->select(
                                PurchaseOrder::getTableName().".po_id",
                                PurchaseOrder::getTableName().".po_no",
                                PurchaseOrder::getTableName().".is_approve",
                                PurchaseOrder::getTableName().".created_at",
                                PurchaseOrder::getTableName().".updated_at",
                                "supp.name as supplier_name",
                                "type.type_name as type",
                                "pr.pr_no"

                     )
                    ->where(PurchaseOrder::getTableName().".is_active", 1);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('pr-table')
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
            "status" => [ "data" => "status", "name" => "is_approve" ],
            "po_no",
            "pr_no",
            "Supplier"  => [ "data" => "supplier_name", "name" => "supp.name" ],
            "type"  => [ "data" => "type", "name" => "type.type_name" ],
            "Created Date"  => [ "data" => "Created Date", "name" => "created_at" ],
            "Modified Date" => [ "data" => "Modified Date", "name" => "updated_at" ]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'PurchaseOrder_' . date('YmdHis');
    }
}
