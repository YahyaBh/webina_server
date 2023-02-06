<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Founders extends Model
{
    use HasFactory;



    protected $fillable = ['name' , 'role' , 'image' , 'portfolio_url' , 'skills'];
}
