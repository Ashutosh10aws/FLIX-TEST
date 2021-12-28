@extends('admin.layouts.template',['menu'=>'pl_package_lists'])
@section('page-content')
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-heading">
                <h3 class="panel-title">PlayList Pricing</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table" id="news-sections-table">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Package Name</th>
                                <th>Duration</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($price_packages as $package)
                                <tr>
                                    <td>{{$loop->index+1}}</td>
                                    <td>{{$package->name}}</td>
                                    <td>{{$package->duration}}</td>
                                    <td>
                                        <a href="{{url('admin/playlist_package/create/'.$package->id)}}">
                                            <button class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></button>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="deleteNewsSection(this,{{$package->id}})">
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
                    <h4 class="modal-title">Confirm Delete Package</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure to delete this package?</p>
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
            var current_tr, current_package_id;
            function deleteNewsSection(targetElement, package_id) {
                current_tr=$(targetElement).closest('tr');
                current_package_id=package_id;
                $('#delete-news-confirm-modal').modal('show');
            }
            function confirmDelete() {
                $.ajax({
                    method:'post',
                    url:site_url+"/admin/playlist_package/delete/"+current_package_id,
                    success:data=>{
                        $('#delete-news-confirm-modal').modal('hide');
                        showSuccessNotify("Price package deleted successfully");
                        $(current_tr).remove();
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
