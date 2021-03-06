<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class Unit extends Model
{

    use EloquentGetTableNameTrait;
    
    protected $fillable =[

        "unit_code", "unit_name", "base_unit", "operator", "operation_value", "is_active"
    ];

}
