<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class Grn extends Model
{
    use HasFactory;

    use EloquentGetTableNameTrait;

    protected $table = 'app_grn'; 
    protected $primaryKey = 'grn_id';
    protected $dates = ['created_at', 'updated_at', 'grn_date'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        "grn_no",
        "supplier_do_no",
        "grn_date",
        "grn_remark",
        "is_approve", 
        "approve_by",
        "approve_date",
        "is_active",
        "created_by",
        "updated_by"
    ];
}
