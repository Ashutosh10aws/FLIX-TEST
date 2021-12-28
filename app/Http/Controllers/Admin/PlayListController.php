<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\PlayList;
use App\Model\PlayListPricePackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PlayListController extends Controller
{
    public function index(){
        $user=Auth::guard('admin')->user();
        $is_admin=$user->is_admin;
        $activated_count=1000;
        if($is_admin==0)
            $activated_count=$user->max_connections-$user->used_connections;
        return view('admin.playlist',compact('activated_count','is_admin'));
    }

    public function getPlaylists(Request $request){
        try{
            $input=$request->all();
            $show_samsung=$input['show_samsung'];
            $show_ios=$input['show_ios'];
            $show_android=$input['show_android'];
            $show_lg=$input['show_lg'];
            $show_apple_tv=$input['show_apple_tv'];
            $show_mac_os=$input['show_mac_os'];
            $show_activated=$input['show_activated'];
            $show_trial=$input['show_trial'];

            $draw = $input['draw'];
            $rowperpage = $input['length'];
            $row = $input['start'];

            $columnIndex = $input['order'][0]['column']; // Column index
            $columnName = $input['columns'][$columnIndex]['data']; // Column name
            $columnSortOrder = $input['order'][0]['dir']; // asc or desc
            $searchValue = $input['search']['value']; // Search value

            $playlists=PlayList::query();
            if($show_android==false || $show_android=='false'){
                $playlists=$playlists->where('app_type','!=','android');
            }

            if($show_samsung==false || $show_samsung=='false'){
                $playlists=$playlists->where('app_type','!=','samsung');
            }

            if($show_ios==false || $show_ios=='false'){
                $playlists=$playlists->where('app_type','!=','ios');
            }

            if($show_lg==false || $show_lg=='false'){
                $playlists=$playlists->where('app_type','!=','lg');
            }

            if($show_apple_tv==false || $show_apple_tv=='false')
                $playlists=$playlists->where('app_type','!=','appleTV');

            if($show_mac_os==false || $show_mac_os=='false')
                $playlists=$playlists->where('app_type','!=','macOS');


            $today=(new \DateTime())->format('Y-m-d');
            $user=Auth::guard('admin')->user();

            if($show_activated==false || $show_activated=='false')
                $playlists=$playlists->where(function($query) use ($today){
                    return $query->where('is_trial','!=',2);
                });

            if($show_trial==false || $show_trial=='false')
                $playlists=$playlists->where(function($query) use ($today){
                    return $query->whereNotIn('is_trial',[0,1]);
                });

            if(!is_null($searchValue) && $searchValue!='')
                $playlists=$playlists->where(function($query) use ($searchValue){
                    return $query->
                    where('mac_address','LIKE',"%$searchValue%");
                });
//            $totalRecords=$playlists->get()->count();
            $totalRecords=$playlists->count();
            $playlists=$playlists->select('id','mac_address','app_type','created_at','expire_date','is_trial')->skip($row)->take($rowperpage);
            if($columnName=='app_type')
                $playlists=$playlists->orderBy('app_type',$columnSortOrder);
            if($columnName=='created_time')
                $playlists=$playlists->orderBy('created_at',$columnSortOrder);
            if($columnName=='expire_date')
                $playlists=$playlists->orderBy('expire_date',$columnSortOrder);

            $playlists=$playlists->get();
            foreach ($playlists as $item){
                $item->created_time=$item->created_at->format('Y-m-d H:i');
            }
            foreach ($playlists as $item){
                $item->action='';
                if($item->is_trial==2){
                    if($user->is_admin==1){
                        $item->action.='<button class="btn btn-sm btn-danger btn-deactivate" data-playlist_id="'.$item->id.'">Deactivate</button>';
                    }else
                        $item->action.='Activated';
                }

                if($item->is_trial!=2){
                    $item->action.='<button class="btn btn-sm btn-success btn-activate" data-playlist_id="'.$item->id.'">Activate</button>';
                }
            }
            if($user->is_admin==1){
                foreach ($playlists as $item){
                    $item->action.='<a href="'.url('/admin/playlist/'.$item->id).'" target="_blank" style="margin:0 5px">'.
                        '<button class="btn btn-sm btn-primary">'.
                        '<i class="fa fa-eye"></i>'.
                        '</button>'.
                        '</a>';
                }
            }
            return ['data'=>$playlists,"draw" => intval($draw),"iTotalDisplayRecords" => $totalRecords,
                "iTotalRecords" => $totalRecords,'inputs'=>$input];
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }
    public function activate(Request $request){
        $input=$request->all();
        $playlist_id=$input['playlist_id'];
        $action=$input['action'];
        $play_list=PlayList::find($playlist_id);
        $today=new \DateTime();
        $user=Auth::guard('admin')->user();
        if($action==1){  // i
            $available_count=100000000000;
            if($user->is_admin==0)
            {
                $available_count=$user->max_connections-$user->used_connections;
                if($available_count<1){
                    return [
                        'status'=>'error',
                        'Sorry, you have '.$available_count."credit remained"
                    ];
                }
            }
            $price_package=PlayListPricePackage::get()->first();
            $current_expire_date=$today->format('Y-m-d');
            if($play_list->expire_date>$current_expire_date)
                $current_expire_date=$play_list->expire_date;
            $expire_date=((new \DateTime($current_expire_date))->modify("+".$price_package->duration.' months'))->format('Y-m-d');
            $play_list->expire_date=$expire_date;
            $play_list->is_trial=2;
            $play_list->save();
            $user->used_connections=$user->used_connections+1;
            $user->save();
        }
        else{
            $expire_date=$today->modify('-2 days')->format('Y-m-d');
            $play_list->expire_date=$expire_date;
            $play_list->is_trial=1;
            $play_list->save();
        }
        return [
            'status'=>'success',
            'expire_date'=>$play_list->expire_date,
            'activated_count'=>$user->max_connections-$user->used_connections
        ];
    }

    public function showDetail($id){
        $play_list=PlayList::find($id);
        $play_list->urls=$play_list->PlayListUrls;
        $play_list->transactions=$play_list->Transactions;
        return view('admin.playlist_detail',compact('play_list'));
    }
}
