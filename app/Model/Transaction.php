<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function PlayList(){
        return $this->belongsTo('App\Model\PlayList','playlist_id','id');
    }
}
