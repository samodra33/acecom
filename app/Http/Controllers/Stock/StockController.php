<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use Keygen;
use Auth;
use DNS1D;
use DB;

use App\Models\Grn;
use App\Models\GrnProduct;
use App\Models\MasterProduct;
use App\Models\MasterProductSupplier;
use App\Models\StockIn;
use App\Models\SerialNumber;

use App\User;
use App\PosSetting;
use App\Warehouse;
use App\Account;
use App\Supplier;
use App\Brand;
use App\Category;
use App\Unit;
use App\GeneralSetting;

use App\DataTables\StockListDataTable;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $documentType = "serial_number";

    public function index(StockListDataTable $datatable)
    {
        return $datatable->render("stock_management.index");
    }

    
    public function stockIn($datas, $type)
    {

        $productData = [];

        foreach ($datas as $key => $value) {

            try {

                $type_reff = $value->grn_product_id;
                $product_id = $value->product_id;
                $stock_qty = $value->product_qty;
                $warehouse_id = $value->warehouse_id;
                $stock_in_date = now();

                $productData[] = array(
                    "type" =>  $type,
                    "type_reff" =>  $type_reff,
                    "product_id" =>  $product_id,
                    "stock_qty" =>  $stock_qty,
                    "stock_in_date" =>  $stock_in_date,
                    "warehouse_id" =>  $warehouse_id,
                    "is_active" =>  1,
                    "created_by" =>  Auth::user()->id,
                    "updated_by" =>  Auth::user()->id

                );
                
            } catch (Exception $e) {
                
                return 0;
            }

        }

        if (count($productData) > 0) {

            foreach ($productData as $key => $value) {

                try {

                    $stockIn = StockIn::create($productData[$key]);

                    $this->ganerateSerialNumber($productData[$key], $type);


                } catch (Exception $e) {

                    return 0;
                }
            }

        }

        return 1;

    }//stockIn    

    public function ganerateSerialNumber($data, $type)
    {

        $product = MasterProduct::where("product_id", $data["product_id"])
                                ->where("is_sn", 1)
                                ->where("sn_input_type", 0)
                                ->first();

        $generalSetting = GeneralSetting::select("site_title")->first();

        if ($product) {

            for ($i=0; $i < $data["stock_qty"]; $i++) { 

                $getDocumentNumber = app("App\Http\Controllers\DocumentNumber\DocumentNumberController")->generate($this->documentType);

                if (empty($getDocumentNumber)) {

                    \Session::flash('not_permitted', 'Something wrong. Please try again a moment.');  
                    return redirect()->back();
                }
                $sn_no = $getDocumentNumber["document_no"];
                $sequenceID = $getDocumentNumber["sequence_id"];
                $nextSequence = $getDocumentNumber["next_sequence"];

                ///////////////////////////////////////////////////////////////store serial number

                $sn = $generalSetting->site_title.'-'.$sn_no.'-'.$product->product_sku;

                $snDb = new SerialNumber();

                $snDb->type = $type;
                $snDb->type_reff = $data["type_reff"];
                $snDb->serial_number = $sn;
                $snDb->status = 1;
                $snDb->is_active = 1;

                $snDb->created_by = Auth::user()->id;
                $snDb->updated_by = Auth::user()->id;

                $snDb->save(); 
                /////////////////////////////////////////////////////////////////

                $getUpdateSequence = app("App\Http\Controllers\DocumentNumber\DocumentNumberController")
                                    ->updateSequence($sequenceID, $nextSequence);
            }

        }

    }//sn
}
