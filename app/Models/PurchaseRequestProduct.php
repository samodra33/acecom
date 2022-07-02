<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class PurchaseRequestProduct extends Model
{
    use HasFactory;

    use EloquentGetTableNameTrait;

    protected $table = 'app_purchase_request_product'; 
    protected $primaryKey = 'pr_product_id';
    protected $dates = ['created_at', 'updated_at'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        "product_id",
        "pr_id",
        "supplier_id",
        "supplier_moq_id",
        "product_qty",
        "product_purchase_unit",
        "product_price",
        "seq_num",
        "is_active",
        "created_by",
        "updated_by"
    ];
}
