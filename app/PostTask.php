<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostTask extends Model
{
     /**
     * The metrics table.
     * 
     * @var string
     */
    protected $table = 'post_tasks';
    protected $guarded = ['created_at' , 'updated_at' , 'id' ];
    protected $fillable = [
    					'event_title',
    					'event_type',
    					'date_required',
    					'time_from',
    					'time_to',
    					'category',
    					'inspiration_photo'

    					];
}
