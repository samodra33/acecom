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
use App\Brand;
use App\Category;

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
        ->addColumn("image", function($query){

            if ($query->product_image) {

                $product_image = explode(",", $query->product_image);
                $product_image = htmlspecialchars($product_image[0]);

                return '<img src="'.url('public/images/product', $product_image).'" height="80" width="80">';
            }

            return "Image not found";
        })
        ->addColumn("supplier", function($query){

            $supplier = MasterProductSupplier::join(Supplier::getTableName()." as supp", "supp.id", MasterProductSupplier::getTableName().".supplier_id")
            ->where("product_id", $query->product_id)
            ->where(MasterProductSupplier::getTableName().".is_active", 1)
            ->select("supp.name")
            ->get();

            $supplierPrint = "";

            foreach ($supplier as $key => $value) {

                $supplierPrint .= "<li>".$value->name."</li>";
            }

            return "<ul>".$supplierPrint."</ul>";
        })

        ->addColumn("S/N Input Type", function($query){


            if ($query->is_sn == 1) {

                if ($query->sn_input_type == 1) {
                    return "Manual Input";
                }
                elseif($query->sn_input_type == 0) {

                    return "Auto Generate by System";
                }else{

                    return "Auto Generate by System";
                }

            }else{

                return "";

            }

        })

        ->addColumn('action', 'ProductMaster.tables.button_action')
        ->rawColumns(["image", "action", "supplier"]);
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
                    ->setTableId('product-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->parameters([ 'responsive' => true, 'autoWidth' => false ])
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
            "action" => ["orderable" => false],
            "image",
            "product_sku",
            "product_upc",
            "product_name",
            "Brand" => [ "data" => "title", "name" => "brand.title" ],
            "Category" => [ "data" => "name", "name" => "category.name" ],
            "suggested_price" => [ "data" => "product_suggested_price" ],
            "min_price" => [ "data" => "product_min_price" ],
            "cost" => [ "data" => "product_cost" ],
            "S/N Input Type",
            "supplier",
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
