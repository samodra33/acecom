<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class MasterProductSupplier extends Model
{
    use HasFactory;

    use EloquentGetTableNameTrait;

    protected $table = 'master_product_supplier'; 
    protected $primaryKey = 'product_supplier_id';
    protected $dates = ['created_at', 'updated_at'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        "product_id",
        "supplier_id",
        "supplier_moq",
        "supplier_price",
        "is_active",
        "seq_num",
        "created_by",
        "updated_by"
    ];
}
