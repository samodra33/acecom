<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class GrnProduct extends Model
{
    use HasFactory;

    use EloquentGetTableNameTrait;

    protected $table = 'app_grn_product'; 
    protected $primaryKey = 'grn_product_id';
    protected $dates = ['created_at', 'updated_at'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        "grn_id",
        "po_warehouse_id",
        "warehouse_id",
        "po_product_id",
        "product_id", 
        "product_qty",
        "product_price",
        "unit_id",
        "supplier_id",
        "seq_num",
        "is_active",
        "created_by",
        "updated_by"
    ];
}
