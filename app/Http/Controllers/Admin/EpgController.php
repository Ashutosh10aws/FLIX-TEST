<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\CountryCode;
use App\Model\EpgCode;
use App\Model\EpgServer;
use Illuminate\Http\Request;

class EpgController extends Controller
{
    public function showEpgCode(){
        $epg_codes=EpgCode::get();
        $countries=CountryCode::get();
        return view('admin.epg_code',compact('epg_codes','countries'));
    }
    public function create(Request $request){
        $input=$request->all();
        $id=$input['id'];
        $url=$input['url'];
        $country=$input['country'];
        if(is_null($id) || $id==''){
            $epg_code=new EpgCode;
        }
        else
            $epg_code=EpgCode::find($id);
        $epg_code->url=$url;
        $epg_code->country=$country;
        $epg_code->save();
        return [
            'status'=>'success',
            'epg_code'=>$epg_code
        ];
    }

    public function delete(Request $request, $id){
        EpgCode::destroy($id);
        return [
            'status'=>'success'
        ];
    }

    public function showEpgServer(){
        $epg_servers=EpgServer::get();
        return view('admin.epg_server',compact('epg_servers'));
    }

    public function createServer(Request $request){
        $input=$request->all();
        $id=$input['id'];
        $url=$input['url'];
        if(is_null($id) || $id==''){
            $epg_server=new EpgServer;
        }
        else
            $epg_server=EpgServer::find($id);
        $epg_server->url=$url;
        $epg_server->save();
        return [
            'status'=>'success',
            'id'=>$epg_server->id
        ];
    }

    public function deleteServer(Request $request, $id){
        EpgServer::destroy($id);
        return [
            'status'=>'success'
        ];
    }
}
