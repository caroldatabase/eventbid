<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
     /**
     * The metrics table.
     * 
     * @var string
     */
    protected $table = 'transaction';
    protected $guarded = ['created_at' , 'updated_at' , 'id' ]; 

     
    public function postTask()
    {
        return $this->belongsTo('App\User', 'userId','id');
    } 
    public function task()
    {
        return $this->belongsTo('App\PostTask', 'taskId','id');
    } 
}
