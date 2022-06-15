<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\EloquentGetTableNameTrait;

class Warehouse extends Model
{

    use EloquentGetTableNameTrait;

    protected $fillable =[

        "name", "outlet_name", "phone", "email", "address", "description", "is_hq", "is_active"
    ];

    public function product()
    {
    	return $this->hasMany('App\Product');
    	
    }
}
