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
use App\Models\MasterProduct;
use App\Supplier;
use App\User;

class PurchaseRequestDataTable extends DataTable
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

        ->addColumn("supplier", function($query){

            $supplier = PurchaseRequestProduct::join(Supplier::getTableName()." as supp", "supp.id", PurchaseRequestProduct::getTableName().".supplier_id")
            ->where("pr_id", $query->pr_id)
            ->where(PurchaseRequestProduct::getTableName().".is_active", 1)
            ->select("supp.name", "supp.company_name")
            ->groupBy("supp.name", "supp.company_name")
            ->get();

            $supplierPrint = "";

            foreach ($supplier as $key => $value) {

                $supplierPrint .= "<li>".$value->name." - ".$value->company_name."</li>";
            }

            return "<ul>".$supplierPrint."</ul>";
        })

        ->addColumn("PR Date", function($query){

            return date_format($query->pr_date,"Y/M/d");

        })

        ->addColumn("Modified Date", function($query){

            return date_format($query->updated_at,"Y/M/d");

        })

        ->filter(function ($query) {

            $query->join(PurchaseRequestProduct::getTableName()." as prProd", "prProd.pr_id", PurchaseRequest::getTableName().".pr_id")
                  ->join(Supplier::getTableName()." as supp", "supp.id", "prProd.supplier_id")
                  ->join(MasterProduct::getTableName()." as prod", "prod.product_id", "prProd.product_id")
                  ->where("prProd.is_active", 1);

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

            if (request()->has('supp_name')) {
                if(request("supp_name")!=""){

                    $suppName = request('supp_name');

                    $query->where(function($filter) use ($suppName){
                            $filter->where("supp.name",'like', "%".request('supp_name')."%");
                            $filter->orwhere("supp.company_name",'like', "%".request('supp_name')."%");
                          });
                          
                }
            }

            if (request()->has('product_name')) {
                if(request("product_name")!=""){
                    $query->where("prod.product_name",'like', "%".request('product_name')."%");
                }
            }

            if (request()->has('product_sku')) {
                if(request("product_sku")!=""){
                    $query->where("prod.product_sku",'like', "%".request('product_sku')."%");
                }
            }

            $query->groupBy(PurchaseRequest::getTableName().".pr_id");
        })

        ->addColumn('action', 'purchase_request.tables.button_action')
        ->rawColumns(["action", "supplier"]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\App\PurchaseRequest $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PurchaseRequest $model)
    {
        return $model->newQuery()
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
                    ->orderBy(7);
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
            "pr_no",
            "supplier",
            "type"  => [ "data" => "type", "name" => "type.type_name" ],
            "PR Date"  => [ "data" => "PR Date", "name" => "pr_date" ],
            "Applied By" => [ "data" => "Applied By", "name" => "apllied.name" ],
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
        return 'PurchaseRequest_' . date('YmdHis');
    }
}
