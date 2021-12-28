@extends('frontend.layouts.template',['menu'=>"mylist"])
@section('content')
    <style>
        label{
            width:120px;
            color:#111;
            font-size:17px;
            margin-top:5px;
        }
        input.form-control{
            width:calc(100% - 120px);
            color: #222 !important;
            font-size: 20px;
            letter-spacing: 0.4px
        }
        @media screen and (min-width:768px){
            label.url-label{
                width:50px;
            }
            input.url-input{
                width:calc(100% - 50px);
            }
        }

        #vue{
            padding-left:20px;
            padding-right:60px;
        }

        .add-url-btn {
            right: 0px;
            top: 0px;
            font-size: 25px;
            color: #06a506;
            line-height: 42px;
            width: 40px;
            height: 40px;
            border-radius: 30px;
            background: #fff;
            box-shadow: 0 0 5px #000;
            text-align: center;
            cursor:pointer;
        }
        .delete-url-icon {
            position: absolute;
            right: 0px;
            font-size: 20px;
            color: #ce2206;
            cursor: pointer;
        }
        .delete-playlist-button-container{
            margin-top:5px;
        }
        @media screen and (max-width:767px){
            #vue{
                padding-right:0;
                padding-bottom:50px;
            }
            .add-url-btn {
                top:unset;
                bottom:0px;
            }
            .delete-playlist-button-container{
                width:100%;
                padding-right:15px;
                padding-top:10px;

            }
        }
        #send-url-btn{
            width: 270px;
            border-radius: 10px;
            font-size: 25px;
        }
    </style>

        <div class="news-section-container">
            @if($mylist_content)
                <?= $mylist_content->contents ?>
            @endif
            @if(session()->has('message'))
                <div class="alert alert-success">
                     <?php echo session()->get('message');?>
                </div>
            @endif
            @if(session()->has('error'))
                <div class="alert alert-danger">
                    <?php echo session()->get('error');?>
                </div>
            @endif
            <form method="post" action="{{url('/mylist/saveMacAdress')}}">
                @csrf
                <div class="row mt-20 pr-30" id="form-container">
                    <div class="col-12 col-md-5 form-group">
                        <div class="row m-0 mt-5">
                            <label>Mac Address:</label>
                            <input type="text" class="form-control mac_address" placeholder="00:aa:bb:cc:dd:11" name="mac-address" maxlength="17" required>
                        </div>
                    </div>
                    @verbatim

                        <div class="col-12 col-md-7 position-relative" id="vue">
                            <div class="add-url-btn position-absolute" @click="addUrl()">
                                <i class="fa fa-plus"></i>
                            </div>
                            <div class="row position-relative pr-25 m-0 mt-5" v-for="(item,index) in urls">
                                <i class="fa fa-trash delete-url-icon" v-if="urls.length>1" @click="deleteUrl(index)"></i>
                                <label class="url-label">Url: </label>
                                <input type="text" class="form-control url-input" :name="'url-'+index" v-model="item.url" required>
                            </div>
                            <input hidden :value="urls.length" name="url-count">
                        </div>
                    @endverbatim
                </div>
{{--                @if(env('GOOGLE_RECAPTCHA_KEY'))--}}
{{--                    <div class="g-recaptcha"--}}
{{--                         data-sitekey="{{env('GOOGLE_RECAPTCHA_KEY')}}">--}}
{{--                    </div>--}}
{{--                @endif--}}
                <input type="hidden" name="recaptcha" class="recaptcha">
                <div class="text-center m-20">
                    <button class="btn btn-primary btn-lg" id="send-url-btn">Send</button>
                </div>
            </form>
            <form method="post" action="{{url('/mylist/delete')}}">
                @csrf
                <div class="row mt-40 pr-30" id="delete-form-container">
                    <div class="col-12 col-md-5">
                        <div class="row m-0 mt-5">
                            <label>Mac Address:</label>
                            <input type="text" class="form-control mac_address" placeholder="00:aa:bb:cc:dd:11" name="mac_address" maxlength="17" required>
                        </div>
                    </div>
                    <input type="hidden" name="recaptcha" class="recaptcha">
                    <div class="delete-playlist-button-container text-right">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </form>
        </div>



@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.sitekey') }}"></script>

    <script>
        var id;
        var app=new Vue({
            el:"#vue",
            data:{
                urls:[
                    {
                        url:""
                    }
                ]
            },
            methods:{
                addUrl(){
                    this.urls.push({
                        url:""
                    })
                },
                deleteUrl(index){
                    this.urls.splice(index,1);
                }
            }
        });

        $(document).ready(function () {
            setTimeout(()=>{
                $('.alert').slideUp(300);
            },10000)
        })

        $(document).on('keyup', '.mac_address', function () {
            makeMacAddressFormat(this)
        })
        $(document).on('change','.mac_address',function () {
            makeMacAddressFormat(this)
        })

        function makeMacAddressFormat(targetElement) {
            var origin_value=$(targetElement).val();
            var max_count=origin_value.length>=16 ? 16 : origin_value.length;
            for(var i=2;i<max_count;i+=3) {
                if (origin_value[i] !== ':')
                    origin_value = [origin_value.slice(0,i),':',origin_value.slice(i)].join('');
            }
            $(targetElement).val(origin_value);
        }

        grecaptcha.ready(function() {
            grecaptcha.execute('{{ config('services.recaptcha.sitekey') }}', {action: 'contact'}).then(function(token) {
                if (token) {
                    $('.recaptcha').val(token);
                }
            });
        });
    </script>
@endsection
