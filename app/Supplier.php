<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class Supplier extends Model
{

    use EloquentGetTableNameTrait;
    
    protected $fillable =[

        "name", "image", "company_name", "vat_number",
        "email", "phone_number", "address", "city",
        "state", "postal_code", "country", "is_active"
        
    ];

    public function product()
    {
    	return $this->hasMany('App/Product');
    	
    }
}
