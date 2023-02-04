<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Websites extends Model
{
    use HasFactory;

    protected $fillable  = ['website_name', 'token', 'category', 'price', 'Developing_Time'];



    public function order()
    {
        return $this->belongsToMany(Orders::class);
    }
}
