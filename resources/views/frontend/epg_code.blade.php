@extends('frontend.layouts.template',['menu'=>"epg-codes"])
<link rel="stylesheet" href="//cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
@section('content')
    <style>
        .page-container{
            display: grid;
            grid-template-columns: 300px 1fr;
            grid-column-gap: 10px;
            min-height: calc(100vh - 200px);
        }
        .country-list-part{
            background: #fff;
            color:#111;
            padding:10px 20px;
            border-radius: 5px;
        }
        .country-list-title{
            font-size:22px;
            font-weight: bold;
            margin-bottom:5px;
        }
        .country-list-item{
            cursor: pointer;
            font-size: 18px;
            padding:5px 10px;
            border-radius: 3px;
        }

        .country-list-item:hover, .country-list-item.active{
            background: #111111bd;
            color: #fff;
            transition:all 0.3s;
        }
        .channel-list-container{
            background: #fff;
            border-radius: 5px;
            padding:10px;
        }
        .loader{
            left:0;
            top:0;
            width:100%;
            height: 100%;
            background: #111;
            z-index:1000;
        }
        .loader img{
            position:absolute;
            left:50%;
            top:50%;
            transform: translate(-50%, -50%);
        }

    </style>
    <div class="page-container">
        <div class="country-list-part">
            <div class="country-list-title">COUNTRY NAME</div>
            <div class="country-list-container">
                <div class="country-list-item active">ALL</div>
                @foreach($countries as $item)
                    <div class="country-list-item">{{$item->country}}</div>
                @endforeach
            </div>
        </div>
        <div class="channel-list-container position-relative">
            <div class="loader position-absolute">
                <img src="{{asset('public/images/loader.gif')}}">
            </div>
            <div class="table-responsive">
                <table class="table" id="epg-table">
                    <thead class="table-dark">
                        <tr>
                            <td>Channel Name</td>
                            <td>Epg Code</td>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>


@endsection

@section('script')
    <script src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script>
        var dataTable=$('#epg-table').DataTable({}), country="ALL", timer=null;
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            'use strict';
            updateDataTable();
        })

        var site_url="<?= url('/')?>";
        function updateDataTable(){
            $('.loader').show();
            dataTable.destroy();
            dataTable=$('#epg-table').DataTable({
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                pageLength: 25,
                'ajax':{
                    url:site_url+"/getEpgCodes",
                    data:{
                        country:country
                    },
                    "dataSrc": function ( json ) {
                        $('.loader').hide();
                        console.log(json);
                        return json.data;
                    }
                },
                'columns': [
                    { data: 'name',sortable:true },
                    { data: 'channel_id',sortable:true },
                ]
            });
        }
        $(document).on('click','.country-list-item',function () {
            $('.country-list-item').removeClass('active');
            country=$(this).text();
            $(this).addClass('active');
            updateDataTable();
        })
    </script>
@endsection
