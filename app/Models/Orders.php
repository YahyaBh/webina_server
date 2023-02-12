<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;


    protected $fillable = ['order_number', 'website_token' , 'user_id', 'user_token' , 'status' , 'grand_total'
    ,'item_count' , 'is_paid' , 'payment_method' , 'notes' , 'user_id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function websites() {
        return $this->belongsToMany(Website::class);
    }
}
