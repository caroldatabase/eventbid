<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class NewCategory extends Model
{
     /**
     * The metrics table.
     * 
     * @var string
     */
    protected $table = 'new_category';
    protected $guarded = ['created_at' , 'updated_at' , 'id' ];
    protected $fillable = [
                            'firstName',
                            'lastName',
                            'email',
                            'userType',
                            'titleOfNewCategory', 
                            'whyNeedCategory',
                        ];
 
}
