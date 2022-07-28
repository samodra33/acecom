<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class Brand extends Model
{

    use EloquentGetTableNameTrait;
    
    protected $fillable =[

        "title", "image", "is_active"
    ];

}
