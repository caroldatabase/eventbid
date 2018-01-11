<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Addinsurance extends Model
{
     /**
     * The metrics table.
     * 
     * @var string
     */
    protected $table = 'addInsurance';
    protected $guarded = ['created_at' , 'updated_at' , 'id' ];
    
    public function user()
    {
        return $this->belongsTo('App\User', 'userId','id');
    } 

}
