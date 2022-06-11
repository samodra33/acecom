<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class MasterProductSku extends Model
{
    use HasFactory;

    use EloquentGetTableNameTrait;

    protected $table = 'master_product_sku'; 
    protected $primaryKey = 'sku_id';
    protected $dates = ['created_at', 'updated_at'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        "sku_no",
        "sku_desc",
        "product_id",
        "seq_num",
        "created_by",
        "updated_by"
    ];
}
