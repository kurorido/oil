<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'date',
        'time',
        'percentage',
        'type',
        'price'
    ];
}
