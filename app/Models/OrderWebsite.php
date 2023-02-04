<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderWebsite extends Model
{
    use HasFactory;


    protected $fillable = ['order_id', 'website_id'];
}
