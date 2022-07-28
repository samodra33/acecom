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
    protected $dates = ['pr_approved_dt', 'created_at', 'updated_at'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        "pr_no",
        "pr_date",
        "pr_remarks",
        "pr_remarks_supplier",
        "pr_approved_by",
        "pr_approved_dt",
        "is_approve",
        "created_by",
        "updated_by"
    ];
}
