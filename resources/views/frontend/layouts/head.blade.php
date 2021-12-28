<head>
    <?php
        $title=isset($title) ? $title : "FLEX IPTV";
        $meta_keyword=isset($keyword) ? $keyword : "";
        $meta_description=isset($description) ? $description : "";
    ?>
    <title>{{$title}}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="keywords" content="{{$meta_keyword}}">
    <meta name="description" content="{{$meta_description}}">

    <link rel="stylesheet" href="{{asset('/admin/template/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('/admin/template/css/bootstrap-extend.min.css')}}">
    <link rel="stylesheet" href="{{asset('/frontend/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('/admin/template/fonts/font-awesome/font-awesome.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{asset('/images/logo.png')}}"/>
</head>
