@extends('admin.layouts.template',['menu'=>'paypal-setting'])

@section('css')

@endsection

@section('page-content')
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-body">
                <form method="post" action="{{url('admin/savePaypalSetting')}}">
                    @csrf
                    <div class="form-group">
                        <label>Client ID</label>
                        <input type="text" name="paypal_client_id" value="{{$paypal_client_id}}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Secret</label>
                        <input type="text" name="paypal_secret" value="{{$paypal_secret}}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Paypal Mode</label>
                        <select name="paypal_mode" class="form-control" value="{{$paypal_mode}}" placeholder="sandbox or live">
                            <option value="sandbox" {{$paypal_mode=="sandbox" ? "selected" : ""}}>Sandbox</option>
                            <option value="live" {{$paypal_mode=="live" ? "selected" : ""}}>Live</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <div>
        <script src="{{asset('/admin/template/vendor/summernote/summernote.min.js')}}"></script>
        <script src="{{asset('/admin/template/vendor/summernote-image-attribute-editor-master/summernote-image-attributes.js')}}"></script>
    </div>
@endsection
