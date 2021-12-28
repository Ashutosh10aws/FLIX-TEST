@extends('admin.layouts.template',['menu'=>'android-update'])

@section('css')

@endsection

@section('page-content')
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-body">
                <form method="post" action="{{url('admin/saveAndroidUpdate')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <a href="{{$apk_url}}">Current Apk Url</a>
                    </div>
                    <div class="form-group">
                        <label>Version Code</label>
                        <input type="text" name="android_version_code" value="{{$android_version_code}}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Apk File</label>
                        <input type="file" name="apk_file" id="apk_file" class="form-control" accept=".apk">
                    </div>
                    @if(session()->has('message'))
                        <div class="alert alert-success">
                            <?php echo session()->get('message');?>
                        </div>
                    @endif
                    <div class="form-group">
                        <button class="btn btn-primary" id="submit-btn">Upload Now</button>
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
        <script>
            $('#submit-btn').click(function (e) {
                var files=$('#apk_file').prop('files');
                if(files.length==0){
                    alert("Apk file is required");
                    e.preventDefault();
                }
            })
            setTimeout(function () {
                $(document).ready(function () {
                    $('.alert').slideUp();
                })
            },4000)

        </script>
    </div>
@endsection
