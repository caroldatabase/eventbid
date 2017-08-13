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
    					'category_id',
    					'inspiration_photo1',
                        'inspiration_photo2',
                        'inspiration_photo3',
                        'task_status',
                        'category_question'

    					];

    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id','id')->select('id','name','categoryImage');
    } 
    public function postUserDetail()
    {
        return $this->belongsTo('App\User', 'post_user_id','id')->select('id','first_name');
    } 
    public function seekerUserDetail()
    {
        return $this->belongsTo('App\User', 'seeker_user_id','id')->select('id','first_name');
    } 
}
