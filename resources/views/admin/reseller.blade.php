@extends('admin.layouts.template',['menu'=>'resellers'])

@section('page-content')
    <style>
        .filter-wrapper label{
            color:#fff !important;
        }
        .password-leave-blank {
            margin-left: 5px;
            color: #838684;
            font-size: 13px;
        }
    </style>
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-heading">
                <h3 class="panel-title">Resellers</h3>
            </div>
            <div class="panel-body">
                <div class="list-wrapper">
                    <div class="text-right mb-20">
                        <button class="btn btn-primary" id="add-new-btn">
                            Add New
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="item-list-table">
                            <thead class="table-dark">
                            <tr>
                                <th>Email</th>
                                <th>Max Connections</th>
                                <th>Activated Connections</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="tbody">
                                @foreach($resellers as $item)
                                    <tr>
                                        <td>{{$item->email}}</td>
                                        <td>{{$item->max_connections}}</td>
                                        <td>{{$item->used_connections}}</td>
                                        <td class="text-center">
                                            <button class="btn btn-primary btn-edit" data-reseller_id="{{$item->id}}" data-max_connections="{{$item->max_connections}}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-delete" data-reseller_id="{{$item->id}}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="modal fade" id="edit-modal">
            <div class="modal-dialog modal-simple modal-center">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">Add/Edit Reseller</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Email</label>
                            <input id="reseller_email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Password<span class="password-leave-blank">(Leave as blank if you don't want to change)</span> </label>
                            <input id="reseller_password" class="form-control" type="password">
                        </div>
                        <div class="form-group">
                            <label>Password Confirm<span class="password-leave-blank">(Leave as blank if you don't want to change)</span></label>
                            <input id="reseller_password_confirm" class="form-control" type="password">
                        </div>
                        <div class="form-group">
                            <label>Connection Count</label>
                            <input id="max_connections" class="form-control" type="number">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary" onclick="addReseller()">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="delete-modal" aria-hidden="true">
            <div class="modal-dialog modal-simple modal-center">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">Confirm Delete</h4>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure to delete this reseller</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="confirmDelete()">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
        @endsection
        @section('script')
            <div>
                <script>
                    var current_tr, current_reseller_id, dataTable;
                    dataTable=$('#item-list-table').DataTable({});

                    $('#add-new-btn').click(function () {
                        current_reseller_id=-1;
                        $('#reseller_email').val('');
                        $('#reseller_password').val('');
                        $('#reseller_password_confirm').val('');
                        $('.password-leave-blank').css({visibility:'hidden'})
                        $('#edit-modal').modal('show');
                    });

                    $(document).on('click','.btn-edit', function () {
                        current_tr=$(this).closest('tr');
                        current_reseller_id=$(this).data('reseller_id');
                        var email=$(current_tr).find('td:eq(0)').text();
                        var max_connections=$(current_tr).find('td:eq(1)').text();
                        $('#reseller_email').val(email);
                        $('#reseller_password').val('');
                        $('#reseller_password_confirm').val('');
                        $('#max_connections').val(max_connections);
                        $('.password-leave-blank').css({visibility:'visible'})
                        $('#edit-modal').modal('show');

                    })

                    function addReseller() {
                        var max_connections=$('#max_connections').val();
                        let reseller_email=$('#reseller_email').val();
                        let password=$('#reseller_password').val();
                        let password_confirm=$('#reseller_password_confirm').val();
                        if(current_reseller_id==-1){
                            if(password==''){
                                alert('Password is required');
                                return;
                            }
                        }
                        if(password!='' && password!=password_confirm){
                            alert('Sorry, Password does not match');
                            return;
                        }
                        $.ajax({
                            method:'post',
                            dataType:'json',
                            url:site_url+"/admin/reseller/create",
                            data:{
                                reseller_id:current_reseller_id,
                                max_connections:max_connections,
                                email:reseller_email,
                                password:password
                            },
                            success:data=>{
                                if(data.status=='success'){
                                    $('#edit-modal').modal('hide');
                                    let html1=`
                                        <td class="text-center">
                                            <button class="btn btn-primary btn-edit" data-reseller_id="${data.id}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-delete" data-reseller_id="${data.id}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    `
                                    if(current_reseller_id==-1){
                                        dataTable.row.add([reseller_email,max_connections,data.used_connections,html1]).draw();
                                    }else{
                                        dataTable.row(current_tr).data([reseller_email,max_connections,data.used_connections,html1]).draw();
                                    }
                                }else{
                                    showErrorNotify(data.msg);
                                }
                            }
                        })
                    }

                    $(document).on('click','.btn-delete', function () {
                        current_tr=$(this).closest('tr');
                        current_reseller_id=$(this).data('reseller_id');
                        $('#delete-modal').modal('show');
                    })
                    function confirmDelete() {
                        $.ajax({
                            method:'post',
                            dataType:'json',
                            url:site_url+"/admin/reseller/delete",
                            data:{
                                reseller_id:current_reseller_id,
                            },
                            success:data=>{
                                if(data.status=='success'){
                                    $('#delete-modal').modal('hide');
                                    dataTable.row(current_tr).remove().draw(false);
                                }else{
                                    showErrorNotify(data.msg);
                                }
                            }
                        })
                    }
                </script>
            </div>
@endsection





