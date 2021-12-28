@extends('admin.layouts.template',['menu'=>'demo_url-setting'])

@section('css')
    <link rel="stylesheet" href="{{asset('/admin/template/vendor/summernote/summernote.css')}}">
    <style>
        .page-meta-content-wrapper{
            border: 1px solid;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            background: #eee;
        }
    </style>
@endsection

@section('page-content')
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-body">
                <form method="post" action="{{url('admin/saveDemoUrl')}}">
                    @csrf

                    <div class="form-group">
                        <label>Demo URL</label>
                        <input type="text" name="demo_url" value="{{$demo_url}}" class="form-control">
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
