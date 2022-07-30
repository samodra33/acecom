<?php

namespace App\DataTables;

use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestProduct;
use App\Models\PurchaseOrder;
use App\Models\PurchaseType;
use App\Models\MasterProduct;
use App\Models\Grn;
use App\Models\GrnProduct;
use App\Supplier;
use App\User;

class MultipleFunctionPRDataTable extends DataTable
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

        ->addColumn("approve_box", function($query){
                return '<input type="checkbox" name="selected_approve_pr[]" value="'. $query->pr_id .'"/>';
        })

        ->addColumn("convert_box", function($query){
                return '<input type="checkbox" name="selected_convert_pr[]" value="'. $query->pr_id .'"/>';
        })

        ->addColumn("Modified Date", function($query){

            return date_format($query->updated_at,"Y/M/d");

        })

        ->addColumn("PR Date", function($query){

            return date_format($query->pr_date,"Y/M/d");

        })

        ->filter(function ($query) {

            if (request()->has('pr_no')) {
                if(request("pr_no")!=""){
                    $query->where("pr_no", "LIKE", "%".request('pr_no')."%");
                }
            }

            if (request()->has('pr_type')) {
                if(request("pr_type")!=""){
                    $query->where("type.type_id", request('pr_type'));
                }
            }

            if (request()->has('start_date')) {
                if(request("start_date")!=""){
                    $query->where("pr_date", ">=", request('start_date'));
                }
            }

            if (request()->has('end_date')) {
                if(request("end_date")!=""){
                    $query->where("pr_date", "<=", request('end_date'));
                }
            }

        })

        ->rawColumns(["approve_box", "convert_box"]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\App\PurchaseRequest $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PurchaseRequest $model)
    {

        $unaprove = 0;
        $convert = 0;

        if(!is_null($this->unaprove)){
            
            $unaprove = $this->unaprove;
        }

        if(!is_null($this->convert)){
            
            $convert = $this->convert;
        }

        $query = $model->newQuery()
                    ->leftJoin(User::getTableName()." as apllied", "apllied.id", PurchaseRequest::getTableName().".created_by")
                    ->leftJoin(PurchaseType::getTableName()." as type", "type.type_id", PurchaseRequest::getTableName().".pr_type")
                    ->select(
                                PurchaseRequest::getTableName().".pr_id",
                                PurchaseRequest::getTableName().".pr_no",
                                PurchaseRequest::getTableName().".pr_date",
                                PurchaseRequest::getTableName().".is_approve",
                                PurchaseRequest::getTableName().".created_by",
                                PurchaseRequest::getTableName().".created_at",
                                PurchaseRequest::getTableName().".updated_by",
                                PurchaseRequest::getTableName().".updated_at",
                                "apllied.name as Applied By",
                                "type.type_id",
                                "type.type_name as type"

                     )
                    ->where(PurchaseRequest::getTableName().".is_active", 1);

        if ($unaprove == 1) {
            $query->where(PurchaseRequest::getTableName().".is_approve", 0);
        }

        if ($convert == 1) {

            $po = PurchaseOrder::where(PurchaseOrder::getTableName().".is_active", 1)
                                  ->pluck("pr_id")
                                  ->all();

            $query->whereNotIn(PurchaseRequest::getTableName().".pr_id", $po)
                  ->where(PurchaseRequest::getTableName().".is_approve", 1);
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
                    ->setTableId('pr-multiple-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->parameters([ 
                                    'responsive' => true, 
                                    'autoWidth' => false, 
                                    'searching' => false  
                                ])
                    ->orderBy(4);
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
            "pr_no",
            "type"  => [ "data" => "type", "name" => "type.type_name" ],
            "PR Date"  => [ "data" => "PR Date", "name" => "pr_date" ],
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
        return 'MultipleFunctionPR_' . date('YmdHis');
    }
}
