<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Websites extends Model
{
    use HasFactory;

    protected $fillable  = ['website_name','token', 'image', 'description', 'stars', 'status', 'theme_document',  'category', 'price', 'old_price',  'developing_Time', 'specifications'];



    public function order()
    {
        return $this->belongsToMany(Orders::class);
    }
}
