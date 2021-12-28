@extends('admin.layouts.template',['menu'=>'playlists'])
@section('page-content')
    <style>
        .detail-item{
            display: grid;
            grid-template-columns: 100px auto;
        }
    </style>
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-heading">
                <h3 class="panel-title">Play List Detail</h3>
            </div>
            <div class="panel-body">
                <div class="detail-item">
                    <span class="detail-item-label">Mac Address: </span>
                    <span class="detail-item-content">{{$play_list->mac_address}}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Expire Date: </span>
                    <span class="detail-item-content" id="expire_date">{{$play_list->expire_date}}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-item-label">Urls: </span>
                    <span class="detail-item-content">
                        @foreach($play_list->urls as $url)
                            {{$url->url}}<br>
                        @endforeach
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-item-label">Transactions: </span>
                    <span class="detail-item-content">
                        @foreach($play_list->transactions as $transaction)
                            ${{$transaction->amount}} {{$transaction->status}}<br>
                        @endforeach
                    </span>
                </div>
                <div class="detail-item">
                    @if($play_list->is_trial!=2)
                        <button class="btn btn-success btn-activate">Activate</button>
                    @else
                        <button class="btn btn-danger btn-deactivate">Deactivate</button>
                    @endif
                </div>



            </div>
        </div>
    </div>
    <div class="modal fade" id="activate-confirm-modal" aria-hidden="true"
         aria-labelledby="examplePositionCenter"
         role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple modal-center">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Confirm Activation</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure to activate this playlist?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmActivate()">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deactivate-confirm-modal" aria-hidden="true"
         aria-labelledby="examplePositionCenter"
         role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple modal-center">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Confirm DeActivation</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure to deactivate this playlist?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmActivate(false)">Confirm</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <div>
        <script>
            var current_button;
            var current_playlist_id="<?php echo $play_list->id ?>";
            $(document).on('click','.btn-activate', function () {
                current_button=$(this);
                $('#activate-confirm-modal').modal('show');
            })
            $(document).on('click','.btn-deactivate', function () {
                current_button=$(this);
                $('#deactivate-confirm-modal').modal('show');
            })

            function confirmActivate(action=true) {
                $.ajax({
                    method:'post',
                    dataType:'json',
                    url:site_url+"/admin/playlist/activate",
                    data:{
                        playlist_id:current_playlist_id,
                        action:action ? 1 : 0
                    },
                    success:data=>{
                        $('#activate-confirm-modal').modal('hide');
                        $('#deactivate-confirm-modal').modal('hide');
                        $('#expire_date').text(data.expire_date);
                        if(action){
                            $(current_button).text('Deactivate').removeClass('btn-activate').addClass('btn-deactivate');
                            $(current_button).removeClass('btn-success').addClass('btn-danger');
                        }
                        else{
                            $(current_button).text('Activate').removeClass('btn-deactivate').addClass('btn-activate');
                            $(current_button).removeClass('btn-danger').addClass('btn-success');
                        }
                    }
                })
            }
        </script>
    </div>
@endsection





