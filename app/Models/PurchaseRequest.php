<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class PurchaseRequest extends Model
{
    use HasFactory;

    use EloquentGetTableNameTrait;

    protected $table = 'app_purchase_request'; 
    protected $primaryKey = 'pr_id';
    protected $dates = ['created_at', 'updated_at'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        "pr_no",
        "pr_date",
        "pr_remarks",
        "pr_remarks_supplier",
        "created_by",
        "updated_by"
    ];
}
