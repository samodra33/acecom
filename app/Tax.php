<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class Tax extends Model
{
    use EloquentGetTableNameTrait;
    
    protected $fillable =[
        "name", "rate", "is_active"
    ];

}
