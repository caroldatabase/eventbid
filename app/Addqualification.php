<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class  Addqualification extends Model
{
     /**
     * The metrics table.
     * 
     * @var string
     */
    protected $table = 'addqualification';
    protected $guarded = ['created_at' , 'updated_at' , 'id' ];
    
     
    public function user()
    {
        return $this->belongsTo('App\User', 'userId','id');
    }

}
