<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class StockIn extends Model
{
    use HasFactory;

    use EloquentGetTableNameTrait;

    protected $table = 'app_stock_in'; 
    protected $primaryKey = 'stock_id';
    protected $dates = ['created_at', 'updated_at', 'grn_date'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        "type",
        "type_reff",
        "product_id",
        "stock_qty",
        "stock_in_date",
        "warehouse_id",
        "is_active",
        "created_by",
        "updated_by"
    ];
}
