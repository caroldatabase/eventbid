<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Messges extends Model
{
     /**
     * The metrics table.
     * 
     * @var string
     */
    protected $table = 'messges';
    protected $guarded = ['created_at' , 'updated_at' , 'id' ];
    
    public function user()
    {
        return $this->belongsTo('App\User', 'userId','id');
    } 
    
    public function task()
    {
        return $this->belongsTo('App\PostTask', 'taskId','id')->with('postUserDetail','seekerUserDetail');
    }

}
