<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class PurchaseType extends Model
{
    use HasFactory;

    use EloquentGetTableNameTrait;

    protected $table = 'master_purchase_type'; 
    protected $primaryKey = 'type_id';
    protected $dates = ['created_at', 'updated_at'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        "type_name",
        "type_description",
        "is_active",
        "created_by",
        "updated_by"
    ];
}
