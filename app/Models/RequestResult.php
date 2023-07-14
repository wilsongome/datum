<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestResult extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['batch', 'order_number', 'str_in', 'key_found', 'hash', 'tries'];
}
