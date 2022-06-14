<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class Category extends Model
{

    use EloquentGetTableNameTrait;
    
    protected $fillable =[

        "name", 'image', "parent_id", "is_active"
    ];

    public function product()
    {
    	return $this->hasMany('App\Product');
    }
}
