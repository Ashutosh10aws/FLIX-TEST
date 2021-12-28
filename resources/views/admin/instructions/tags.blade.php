@extends('admin.layouts.template',['menu'=>'instruction_tags'])
@section('page-content')
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-body">
                <div class="row justify-content-center">
                    <div class="col-6">
                        <form id="create-tag-form">
                            <div class="form-group">
                                <label>Tag Name</label>
                                <input type="text" class="form-control" id="tag_name" required>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" id="create-tag-submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-boxed">
            <div class="panel-heading">
                <h3 class="panel-title">Instruction Tags</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table" id="instruction-tags-table">
                        <thead class="table-dark">
                            <tr>
                                <th>Tag Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tags as $tag)
                                <tr>
                                    <td>{{$tag->tag_name}}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editTag(this, {{$tag->id}})"><i class="fa fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteTag(this,{{$tag->id}})">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <a href="{{url('admin/instruction/page/'.$tag->id)}}">
                                            <button class="btn btn-success btn-sm"><i class="fa fa-eye"></i></button>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="delete-news-confirm-modal" aria-hidden="true"
         aria-labelledby="examplePositionCenter"
         role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple modal-center">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Confirm Delete News</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure to delete this news instruction tag?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmDelete()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit-tag-modal" aria-hidden="true"
         aria-labelledby="examplePositionCenter"
         role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple modal-center">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Edit Tag</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tag Name</label>
                        <input class="form-control" id="edit_tag_name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmUpdate()">Confirm</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <div>
        <script>
            var current_tr, current_tag_id, dataTable;
            function deleteTag(targetElement, tag_id) {
                current_tr=$(targetElement).closest('tr');
                current_tag_id=tag_id;
                $('#delete-news-confirm-modal').modal('show');
            }
            function confirmDelete() {
                $.ajax({
                    method:'post',
                    url:site_url+"/admin/instruction/deleteTag/"+current_tag_id,
                    success:data=>{
                        $('#delete-news-confirm-modal').modal('hide');
                        showSuccessNotify("Instruction tag deleted successfully");
                        $( current_tr ).remove();
                    },
                    error:error=>{
                        showErrorNotify("Sorry, Something is wrong, please try later")
                        $('#delete-news-confirm-modal').modal('hide');
                    }
                })
            }

            $('#create-tag-submit').click(function (e) {
                e.preventDefault();
                var tag_name=$('#tag_name').val();
                saveTag(tag_name);
            })

            function editTag(targetElement, tag_id){
                current_tr=$(targetElement).closest('tr');
                current_tag_id=tag_id;
                var current_tag_name=$(current_tr).find('td:eq(0)').text();
                $('#edit_tag_name').val(current_tag_name);
                $('#edit-tag-modal').modal('show');
            }
            function confirmUpdate(){
                var updated_tag_name=$('#edit_tag_name').val();
                saveTag(updated_tag_name, current_tag_id,true);
            }

            function saveTag(tag_name, id='', is_update=false){
                $.ajax({
                    method:'post',
                    url:site_url+"/admin/instruction/createTag",
                    dataType:'json',
                    data:{
                        tag_name:tag_name,
                        id:id
                    },
                    success:data=>{
                        $('#edit-tag-modal').modal('hide');
                        if(!is_update){ // if create mode
                            var htmlContent=`
                            <tr>
                                <td>${tag_name}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editTag(this,${data.id})"><i class="fa fa-edit"></i></button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteTag(this,${data.id})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <a href="/admin/instruction/page/${data.id}">
                                        <button class="btn btn-success btn-sm"><i class="fa fa-eye"></i></button>
                                    </a>
                                </td>
                            </tr>
                        `
                            $('#instruction-tags-table').find('tbody').append(htmlContent);
                        }
                        else{
                            var updated_tag_name=$('#edit_tag_name').val();
                            $(current_tr).find('td:eq(0)').text(updated_tag_name);
                        }
                    },
                    error:error=>{

                    }
                })
            }
        </script>
    </div>
@endsection





