<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CoinList extends Model
{
    protected $casts=[
        'data'=>'array'
    ];
}
