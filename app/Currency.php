<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class Currency extends Model
{
    use EloquentGetTableNameTrait;
    
    protected $fillable = ["name", "code", "exchange_rate"];
}
