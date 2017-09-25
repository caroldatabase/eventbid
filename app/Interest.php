<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
     /**
     * The metrics table.
     * 
     * @var string
     */
    protected $table = 'interest';
    protected $guarded = ['created_at' , 'updated_at' , 'id' ]; 


    public function task()
    {
    	 return $this->belongsTo('App\PostTask','taskId','id');
    }
    public function taskPostedUser()
    {
    	 
    	return $this->belongsTo('App\User', 'taskPostedUserID','id');
    }
    public function assignUser()
    {
    	return $this->belongsTo('App\User', 'assignUserID','id');
    }
}
