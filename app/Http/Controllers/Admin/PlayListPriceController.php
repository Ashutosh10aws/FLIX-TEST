<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\PlayListPricePackage;

class PlayListPriceController extends Controller
{
    public function index(){
        $price_packages=PlayListPricePackage::get();
        return view('admin.playlist_package.index', compact('price_packages'));
    }
    public function createPackage($id=null){
        $package=null;
        if(!is_null($id)){
            $package=PlayListPricePackage::find($id);
        }
        return view('admin.playlist_package.create', compact('package','id'));
    }
    public function savePackage(Request $request){
        $input=$request->all();
        $package_name=$input['package_name'];
        $duration=$input['duration'];
        $price_count=$input['price-count'];
        $id=$input['id'];
        $package=null;
        if(!is_null($id)){
            $package=PlayListPricePackage::find($id);
        }
        else
            $package=new PlayListPricePackage;
        $package->name=$package_name;
        $package->duration=$duration;
        $prices=[];
        for($i=0;$i<$price_count;$i++){
            $prices[]=[
                'min'=>$input['package-min-'.$i],
                'max'=>$input['package-max-'.$i],
                'currency'=>$input['package-currency-'.$i],
                'price'=>$input['package-price-'.$i]
            ];
        }
        $package->price=$prices;
        $package->save();
        return redirect('/admin/playlist_package');
    }

    public function deletePackage($id){
        PlayListPricePackage::destroy($id);
        return [
            'status'=>'successs'
        ];
    }
}
