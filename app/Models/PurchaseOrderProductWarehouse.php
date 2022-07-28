<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class PurchaseOrderProductWarehouse extends Model
{
    use HasFactory;

    use EloquentGetTableNameTrait;

    protected $table = 'app_purchase_order_product_warehouse'; 
    protected $primaryKey = 'po_warehouse_id';
    protected $dates = ['created_at', 'updated_at'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        "po_product_id",
        "warehouse_id",
        "warehouse_qty",
        "status",
        "is_active",
        "created_by",
        "updated_by"
    ];
}
