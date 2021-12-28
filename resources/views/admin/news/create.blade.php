@extends('admin.layouts.template',['menu'=>'news-create'])

@section('css')
    <link rel="stylesheet" href="{{asset('/admin/template/vendor/summernote/summernote.css')}}">
@endsection

@section('page-content')
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-body">
                <form method="post" action="{{url('admin/news/save')}}">
                    @csrf
                    <div class="form-group">
                        <label>Section Name (required *)</label>
                        <input type="text" class="form-control" id="section-name" required name="section-name" value="{{!is_null($section) ? $section->section_name : ""}}">
                    </div>
                    <input hidden name="id" value="{{$id}}">
                    <div class="form-group">
                        <label>Section Content (required *)</label>
                        <textarea id="section-content" class="form-control" required name="section-content">
                            {{is_null($section) ? "" : $section->contents}}
                        </textarea>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="section_status" value="{{!is_null($section) ? $section->status : "draft"}}">
                            <option value="draft" {{!is_null($section) && $section->status==="draft" ? "selected" : ""}}>Draft</option>
                            <option value="publish" {{!is_null($section) && $section->status==="publish" ? "selected" : ""}}>Publish</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">{{is_null($id) ? "Submit" : "Update"}}</button>
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
                    ]

                });
            })
        </script>
    </div>
@endsection
