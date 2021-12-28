@extends('admin.layouts.template',['menu'=>'stripe-setting'])

@section('css')

@endsection

@section('page-content')
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-body">
                <form method="post" action="{{url('admin/saveStripeSetting')}}">
                    @csrf
                    <div class="form-group">
                        <label>Public Key</label>
                        <input type="text" name="public_key" value="{{$stripe_public_key}}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Secret Key</label>
                        <input type="text" name="secret_key" value="{{$stripe_secret_key}}" class="form-control">
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
