<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LabLog extends Model
{
    protected $fillable = [
        'code',
        'content',        
    ];
}
