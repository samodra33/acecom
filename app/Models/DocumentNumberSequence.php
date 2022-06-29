<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\EloquentGetTableNameTrait;

class DocumentNumberSequence extends Model
{
    use HasFactory;

    //
    use EloquentGetTableNameTrait;
    
    protected $table = 'master_document_number_sequence'; 
    protected $primaryKey = 'sequence_id';
    protected $dates = ['created_dt', 'modified_dt'];
    const CREATED_AT = 'created_dt';
    const UPDATED_AT = 'modified_dt';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sequence_value' => 'integer', // Don't Remove
    ];
}
