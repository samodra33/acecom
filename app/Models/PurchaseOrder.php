<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class PurchaseOrder extends Model
{
    use HasFactory;

    use EloquentGetTableNameTrait;

    protected $table = 'app_purchase_order'; 
    protected $primaryKey = 'po_id';
    protected $dates = ['approve_date', 'created_at', 'updated_at'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        "pr_id",
        "po_no",
        "po_supplier",
        "po_date",
        "po_type",
        "po_remark",
        "is_approve",
        "approve_by",
        "approve_date",
        "is_active",
        "created_by",
        "updated_by"
    ];
}
