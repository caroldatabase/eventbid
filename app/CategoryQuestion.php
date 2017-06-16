<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryQuestion extends Model
{
     /**
     * The metrics table.
     * 
     * @var string
     */
    protected $table = 'category_question';
    protected $guarded = ['created_at' , 'updated_at' , 'id' ];
    
}
