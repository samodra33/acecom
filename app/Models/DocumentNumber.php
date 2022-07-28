<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class DocumentNumber extends Model
{
    use HasFactory;

    use EloquentGetTableNameTrait;

    protected $table = 'master_document_number'; 
    protected $primaryKey = 'doc_no_id';
    protected $dates = ['created_dt', 'modified_dt'];
    const CREATED_AT = 'created_dt';
    const UPDATED_AT = 'modified_dt';
}
