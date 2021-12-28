<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PlayListPricePackage extends Model
{
    protected $casts=[
        'price'=>'array'
    ];
}
