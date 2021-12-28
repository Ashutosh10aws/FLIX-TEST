<?php $menu=$tag_id==0 ? "instruction_summary" : 'instruction_tags';?>
@extends('admin.layouts.template',['menu'=>$menu])

@section('css')
    <link rel="stylesheet" href="{{asset('/admin/template/vendor/summernote/summernote.css')}}">
@endsection

@section('page-content')
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-body">
                <form method="post" action="{{url('admin/instruction/page/save/'.$tag_id)}}">
                    @csrf
                    <input hidden name="id" value="{{$tag_id}}">
                    <div class="form-group">
                        <label>Section Content (required *)</label>
                        <textarea id="section-content" class="form-control" required name="section-content">
                            {{is_null($instruction_content) ? "" : $instruction_content->contents}}
                        </textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">{{is_null($tag_id) ? "Submit" : "Update"}}</button>
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
                    ],

                });
            })
        </script>
    </div>
@endsection
