<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostCode extends Model
{
    use HasFactory;
    protected $table = 'post_codes';
    protected $fillable = [
        'postcode',
        'latitude',
        'longitude'
    ];
}
