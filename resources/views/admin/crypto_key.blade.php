@extends('admin.layouts.template',['menu'=>'crypto-api-setting'])

@section('css')
@endsection

@section('page-content')
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-body">
                <form method="post" action="{{url('admin/saveCryptoApiKey')}}">
                    @csrf
                    <div class="form-group">
                        <label>Public Key</label>
                        <input type="text" name="crypto_public_key" value="{{$crypto_public_key}}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Private Key</label>
                        <input type="text" name="crypto_private_key" value="{{$crypto_private_key}}" class="form-control" required>
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
