<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class PurchaseOrderProduct extends Model
{
    use HasFactory;

    use EloquentGetTableNameTrait;

    protected $table = 'app_purchase_order_product'; 
    protected $primaryKey = 'po_product_id';
    protected $dates = ['created_at', 'updated_at'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        "pr_product_id",
        "po_id",
        "product_id",
        "supplier_id",
        "supplier_moq_id",
        "product_qty",
        "product_purchase_unit",
        "product_price",
        "product_gst",
        "product_currency",
        "seq_num",
        "is_active",
        "created_by",
        "updated_by"
    ];
}
