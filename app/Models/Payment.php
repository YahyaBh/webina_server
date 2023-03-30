<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;


    protected $fillable = ['payment_token', 'website_name', 'website_token', 'user_id', 'amount', 'paid', 'method'];
}
