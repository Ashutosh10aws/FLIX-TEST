@extends('frontend.layouts.template',['menu'=>"instruction"])
@section('content')
    <style>

    </style>
    @if($instruction)
        <div class="news-section-container">
            <?= $instruction->contents ?>
        </div>
    @endif
@endsection
