<?php
$menu=isset($menu) ? $menu : "";
?>
<div class="header">
    <div class="header-items-container">
        <div class="header-item-wrapper {{$menu=='news' ? 'active' : ''}}">
            <a href="{{url('/news')}}">News</a>
        </div>
        <div class="header-item-wrapper {{$menu=='faq' ? 'active' : ''}}">
            <a href="{{url('/faq')}}">Support</a>
        </div>
        <div class="header-item-wrapper {{$menu=='instruction' ? 'active' : ''}}">
            <a href="{{url('/instruction')}}">Instruction</a>
        </div>
        <div class="header-item-wrapper {{$menu=='mylist' ? 'active' : ''}}">
            <a href="{{url('/mylist')}}">My List</a>
        </div>
        <div class="header-item-wrapper {{$menu=='activation' ? 'active' : ''}}">
            <a href="{{url('/activation')}}">Activation</a>
        </div>
        <div class="header-item-wrapper {{$menu=='epg-codes' ? 'active' : ''}}">
            <a href="{{url('/epg-codes')}}">Epg Codes</a>
        </div>
    </div>
</div>
