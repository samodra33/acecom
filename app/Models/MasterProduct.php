<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class MasterProduct extends Model
{
    use HasFactory;

    use EloquentGetTableNameTrait;

    protected $table = 'master_product'; 
    protected $primaryKey = 'product_id';
    protected $dates = ['created_at', 'updated_at'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        "product_code",
        "product_name",
        "product_brand",
        "product_category",
        "product_selling_price",
        "product_alert_qty",
        "bom_division",
        "created_by",
        "updated_by"
    ];
}
