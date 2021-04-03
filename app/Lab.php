<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lab extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'content',        
    ];

    /**
     *  METHODS
     */

     /** 
      * create a record in lab_logs table
     */
    public function postLog()
    {
        LabLog::create(['code' => $this->code, 'content' => json_encode($this->content)]);
    }
}
