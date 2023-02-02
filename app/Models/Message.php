<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messsage extends Model
{

    protected $fillable = ['message'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
