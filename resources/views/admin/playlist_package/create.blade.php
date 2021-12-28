@extends('admin.layouts.template',['menu'=>'pl_package_create'])

@section('css')
    <link rel="stylesheet" href="{{asset('/admin/template/vendor/summernote/summernote.css')}}">
    <style>
        .price-item-container{
            padding-right:20px;
            margin-left: 0 !important;
            margin-right:0 !important;
        }
        .price-delete-icon{
            right:10px;
            color: #c00c06;
            top: 40px;
            cursor:pointer;
        }
        .price-add-icon{
            top: 10px;
            right: 10px;
            color: #2eaf0e;
            font-size: 35px;
            /* box-shadow: 0 0 5px #0e860e; */
            border-radius: 40px;
            height: 40px;
            width: 40px;
            text-align: center;
            line-height: 40px;
            cursor:pointer;
        }
        .price-add-icon:hover{
            color: #31e00e
        }

        .prices-container{
            background: #eee;
            box-shadow: 0 0 5px #333;
            padding:20px;
            border-radius: 10px;
            font-size:20px;
            padding-top:40px;
        }
    </style>
@endsection

@section('page-content')
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-body">
                <form method="post" action="{{url('admin/playlist_package/save')}}">
                    @csrf
                    <div class="form-group">
                        <label>Package Name (required *)</label>
                        <input type="text" class="form-control" id="package-name" required name="package_name" value="{{!is_null($package) ? $package->name : ""}}">
                    </div>
                    <div class="form-group">
                        <label>Duration (months)</label>
                        <input type="number" class="form-control" id="duration" required name="duration" value="{{!is_null($package) ? $package->duration : ""}}">
                    </div>
                    <input hidden name="id" value="{{$id}}">
                    <label>Prices</label>
                    <div class="form-group prices-container position-relative" id="vue">
                        @verbatim
                            <i class="fa fa-plus-circle price-add-icon position-absolute" @click="addPrice()"></i>
                            <input hidden name="price-count" v-model="price.length">
                            <div v-for="(item, index) in price" class="row price-item-container position-relative">
                                <i class="fa fa-trash position-absolute price-delete-icon" @click="deletePrice(index)" v-if="price.length>1"></i>
                                <div class="form-group col-4">
                                    <label>Min Urls</label>
                                    <input type="number" class="form-control" :name="'package-min-'+index" required v-model="item.min">
                                </div>
                                <div class="form-group col-4">
                                    <label>Max Urls</label>
                                    <input type="number" class="form-control" :name="'package-max-'+index" required v-model="item.max">
                                </div>
                                <div class="form-group col-4">
                                    <label>Price</label>
                                    <input type="number" class="form-control" :name="'package-price-'+index" v-model="item.price" step="0.01">
                                </div>
                                <div class="form-group col-4">
                                    <label>Currency</label>
                                    <select class="form-control" :name="'package-currency-'+index" v-model="item.currency">
                                        <option value="EUR" selected>Euro</option>
                                        <option value="USD">Dollar</option>
                                    </select>
                                </div>
                            </div>
                        @endverbatim
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
        <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
        <script>
            var id;
            var app=new Vue({
                el:"#vue",
                data:{
                    price:[
                        {
                            min:1,
                            max:100,
                            price:'',
                            currency:'EUR'
                        }

                    ]
                },
                mounted(){
                    @if(!is_null($package))
                        this.price=JSON.parse(`<?php echo json_encode($package->price); ?>`)
                    @endif
                    {{--this.price=JSON.parse(`<?php echo !is_null($package) ? json_encode($package->price) : '[]'; ?>`)--}}
                },
                methods:{
                    addPrice(){
                        this.price.push({
                            min:'',
                            max:'',
                            price:'',
                            currency:''
                        })
                    },
                    deletePrice(index){
                        this.price.splice(index,1);
                    }
                }
            });

        </script>
    </div>
@endsection
