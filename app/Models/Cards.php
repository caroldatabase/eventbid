<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cards extends Model
{
    
    protected $table = 'user_cards';
    protected $fillable = [
                            "card_id",
                            "external_card_id",
                            "customer_id",
                            "first_name",
                            "last_name",
                            "card_number",
                            "expire_month",
                            "expire_year",
                            "type",
                            "updated_at",
                            "created_at" 
                        ];

}
