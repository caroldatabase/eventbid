<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomCategory extends Model
{
     /**
     * The metrics table.
     * 
     * @var string
     */
    protected $table = 'custom_categories';
    protected $guarded = ['created_at' , 'updated_at' , 'id' ];
    protected $fillable = [
                            'category_title',
                            'first_name',
                            'last_name',
                            'email',
                            'description',
                            'created_by',
                            'status'
                        ];
}
