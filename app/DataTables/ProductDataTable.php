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
        ->addColumn('action', 'ProductMaster.tables.button_action')
        ->rawColumns(["image", "action"]);
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
                                MasterProduct::getTableName().".product_image",
                                MasterProduct::getTableName().".is_active",
                                "brand.title as Brand",
                                "category.name as Category"


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
            "action",
            "image",
            "product_sku",
            "product_upc",
            "product_name",
            "Brand",
            "Category"
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
