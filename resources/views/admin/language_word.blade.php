@extends('admin.layouts.template',['menu'=>'language-code'])

@section('css')

@endsection

@section('page-content')
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-body">
                <h3 class="text-right">{{$language->name}}</h3>
                <form method="post" action="{{url('admin/saveLanguageFile/'.$language_id)}}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Upload XML file</label>
                        <input type="file" name="language-file" class="form-control">
                    </div>
                    <div class="button-container">
                        <button class="btn btn-primary">Upload</button>
                    </div>
                </form>

                <h3 class="mt-50">Or Upload Words Manually</h3>
                <form method="post" action="{{url('admin/saveLanguageWord/'.$language_id)}}">
                    @csrf
                    <div class="row">
                        @foreach($words as $item)
                            <div class="col-12 col-lg-6">
                                <div class="form-group">
                                    <label>{{$item->name}}</label>
                                    <input type="text" name="language_word-{{$item->id}}"
                                       class="form-control"
                                       value="{{isset($language_words_map[strval($item->id)]) ? $language_words_map[strval($item->id)]->value : ''}}"
                                    >
                                </div>
                            </div>
                        @endforeach
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
