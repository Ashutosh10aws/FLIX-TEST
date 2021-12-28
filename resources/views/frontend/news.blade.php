@extends('frontend.layouts.template',['menu'=>"news","title"=>$title,"keyword"=>$keyword,"description"=>$description])
@section('content')
    @foreach($news_sections as $section)
        <div class="news-section-container">
            <?= $section->contents ?>
        </div>
    @endforeach
@endsection
