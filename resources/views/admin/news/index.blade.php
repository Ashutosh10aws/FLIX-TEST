@extends('admin.layouts.template',['menu'=>'news'])
@section('page-content')
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-heading">
                <h3 class="panel-title">News Sections</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table" id="news-sections-table">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Section Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($news_sections as $section)
                                <tr>
                                    <td>{{$loop->index+1}}</td>
                                    <td>{{$section->section_name}}</td>
                                    <td>{{$section->status}}</td>
                                    <td>
                                        <a href="{{url('admin/news/create/'.$section->id)}}">
                                            <button class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></button>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="deleteNewsSection(this,{{$section->id}})">
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
                    <p>Are you sure to delete this news section?</p>
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
            var current_tr, current_section_id, dataTable;
            $(document).ready(function () {
                dataTable=$('#news-sections-table').DataTable();
            })
            function deleteNewsSection(targetElement, section_id) {
                current_tr=$(targetElement).closest('tr');
                current_section_id=section_id;
                $('#delete-news-confirm-modal').modal('show');
            }
            function confirmDelete() {
                $.ajax({
                    method:'post',
                    url:site_url+"/admin/news/delete/"+current_section_id,
                    success:data=>{
                        $('#delete-news-confirm-modal').modal('hide');
                        showSuccessNotify("News section saved successfully");
                        dataTable.row( current_tr ).remove().draw();
                    },
                    error:error=>{
                        showErrorNotify("Sorry, Something is wrong, please try later")
                        $('#delete-news-confirm-modal').modal('hide');
                    }
                })
            }
        </script>
    </div>
@endsection
