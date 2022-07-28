<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class SerialNumber extends Model
{
    use HasFactory;

    use EloquentGetTableNameTrait;

    protected $table = 'app_serial_number'; 
    protected $primaryKey = 'serial_number_id';
    protected $dates = ['created_at', 'updated_at', 'grn_date'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        "type",
        "type_reff",
        "serial_number",
        "status",
        "is_active", 
        "created_by",
        "updated_by"
    ];
}
