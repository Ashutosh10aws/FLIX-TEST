@extends('admin.layouts.template',['menu'=>'playlists'])

@section('page-content')
    <style>
        .filter-wrapper label{
            color:#fff !important;
        }
        #activation-count-container{
            margin-bottom: 15px;
            color: #111;
            font-size: 17px;
        }
        #activation-count-label{
            font-weight: bold;
        }
    </style>
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-heading">
                <h3 class="panel-title">Play Lists</h3>
            </div>
            <div class="panel-body">
                <div class="list-container">
                    <div class="filter-wrapper">
                        <div class="select-country-wrapper">
                            <div class="checkbox-custom checkbox-primary">
                                <input type="checkbox" id="show_android" checked>
                                <label for="show_android">Show Android</label>
                            </div>
                            <div class="checkbox-custom checkbox-primary">
                                <input type="checkbox" id="show_samsung" checked>
                                <label for="show_samsung">Show Samsung</label>
                            </div>
                            <div class="checkbox-custom checkbox-primary">
                                <input type="checkbox" id="show_lg" checked>
                                <label for="show_lg">Show Lg</label>
                            </div>
                            <div class="checkbox-custom checkbox-primary">
                                <input type="checkbox" id="show_ios" checked>
                                <label for="show_ios">Show Ios</label>
                            </div>
                            <div class="checkbox-custom checkbox-primary">
                                <input type="checkbox" id="show_apple_tv" checked>
                                <label for="show_apple_tv">Show Apple TV</label>
                            </div>
                            <div class="checkbox-custom checkbox-primary">
                                <input type="checkbox" id="show_mac_os" checked>
                                <label for="show_mac_os">Show Mac OS</label>
                            </div>
                            <h4 class="example-title"
                                style="color:#fff;margin-top:20px"
                            >
                                Filter by activated status
                            </h4>
                            <div class="checkbox-custom checkbox-primary">
                                <input type="checkbox" id="show_activated" checked>
                                <label for="show_activated">Show Activated</label>
                            </div>
                            <div class="checkbox-custom checkbox-primary">
                                <input type="checkbox" id="show_trial" checked>
                                <label for="show_trial">Show Trial</label>
                            </div>
                        </div>
                    </div>
                    <div class="list-wrapper">
                        @if($is_admin==0)
                            <div id="activation-count-container">
                                <span id="activation-count-label">Remain Connections:</span>
                                <span id="activation-count">{{$activated_count}}</span>
                            </div>
                        @endif
                        <div class="table-responsive">
                    <table class="table" id="item-list-table">
                        <thead class="table-dark">
                            <tr>
                                <th>Mac Address</th>
                                <th>App Type</th>
                                <th>Created Date</th>
                                <th>Expire Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
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
            var current_tr, current_playlist_id, dataTable, current_button, is_admin=parseInt('<?= $is_admin ?>');
            dataTable=$('#item-list-table').DataTable({
                "order": [[ 2, "desc" ]]
            });
            $(document).ready(function () {
                updateDataTable();
            })
            function updateDataTable(){
                dataTable.destroy();
                dataTable=$('#item-list-table').DataTable({
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    pageLength: 50,
                    'ajax':{
                        url:site_url+"/admin/playlist/getPlaylists",
                        data:{
                            show_samsung:$('#show_samsung').prop('checked'),
                            show_android:$('#show_android').prop('checked'),
                            show_lg:$('#show_lg').prop('checked'),
                            show_ios:$('#show_ios').prop('checked'),
                            show_apple_tv:$('#show_apple_tv').prop('checked'),
                            show_mac_os:$('#show_mac_os').prop('checked'),
                            show_activated:$('#show_activated').prop('checked'),
                            show_trial:$('#show_trial').prop('checked'),

                        },
                        "dataSrc": function ( json ) {
                            return json.data;
                        }
                    },
                    'columns': [
                        { data: 'mac_address',sortable:false },
                        { data: 'app_type',sortable:true },
                        { data: 'created_time',sortable:true },
                        { data: 'expire_date',sortable:true },
                        { data:'action',sortable:false}
                    ]
                });
            }


            $(document).on('click','.btn-activate', function () {
                current_tr=$(this).closest('tr');
                current_playlist_id=$(this).data('playlist_id');
                current_button=$(this);
                $('#activate-confirm-modal').modal('show');

            })
            $(document).on('click','.btn-deactivate', function () {
                current_tr=$(this).closest('tr');
                current_playlist_id=$(this).data('playlist_id');
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
                        $(current_tr).find('td:eq(2)').text(data.expire_date);
                        if(action){
                            if(is_admin==1){
                                $(current_button).text('Deactivate').removeClass('btn-activate').addClass('btn-deactivate');
                                $(current_button).removeClass('btn-success').addClass('btn-danger');
                            }else{
                                $(current_button).closest('td').html('Activated');
                            }

                        }
                        else{
                            $(current_button).text('Activate').removeClass('btn-deactivate').addClass('btn-activate');
                            $(current_button).removeClass('btn-danger').addClass('btn-success');
                        }
                    }
                })
            }

            $('#show_android').change(function () {
                updateDataTable();
            })
            $('#show_samsung').change(function () {
                updateDataTable();
            })
            $('#show_ios').change(function () {
                updateDataTable();
            })
            $('#show_lg').change(function () {
                updateDataTable();
            })

            $('#show_activated').change(function () {
                updateDataTable();
            })

            $('#show_trial').change(function () {
                updateDataTable();
            })
        </script>
    </div>
@endsection





