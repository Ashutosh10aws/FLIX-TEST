<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PlayList extends Model
{
    public function PlayListUrls()
    {
        return $this->hasMany('App\Model\PlayListUrl','playlist_id');
    }

    public function Transactions(){
        return $this->hasMany('App\Model\Transaction','playlist_id');
    }
}
