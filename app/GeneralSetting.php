<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class GeneralSetting extends Model
{
    use EloquentGetTableNameTrait;
    
    protected $fillable =[

        "site_title", "site_logo", "is_rtl", "currency", "currency_position", "staff_access", "date_format", "theme", "developed_by", "invoice_format", "state"
    ];
}
