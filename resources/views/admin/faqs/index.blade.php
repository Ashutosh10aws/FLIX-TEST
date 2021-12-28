@extends('admin.layouts.template',['menu'=>'faq'])

@section('css')
    <link rel="stylesheet" href="{{asset('/admin/template/vendor/summernote/summernote.css')}}">
@endsection

@section('page-content')
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-body">
                <form method="post" action="{{url('admin/faq/save')}}">
                    @csrf
                    <div class="form-group">
                        <label>Faq Content (required *)</label>
                        <textarea id="section-content" class="form-control" required name="section-content">
                            {{is_null($faq) ? "" : $faq->contents}}
                        </textarea>
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
        <script>
            $(document).ready(function () {
                $('#section-content').summernote({
                    height:400,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']],
                        ['insert', ['link', 'picture', 'video','table']],
                        ['view', ['fullscreen', 'codeview', 'help']],
                    ]
                });
            })
        </script>
    </div>
@endsection
