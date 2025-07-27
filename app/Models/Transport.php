<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    protected $fillable = [
        'job_title',
        'job_description',
        'user_id',
        'job_status',
        
    ];
    protected $primaryKey = 'id';
    
}
