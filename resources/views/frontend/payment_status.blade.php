@extends('frontend.layouts.template',['menu'=>"activation"])
@section('content')
    <style>
        .status-message {
            font-size: 20px;
            padding: 20px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            /*position: absolute;*/
            /*width: 80%;*/
            /*left: 50%;*/
            /*top: 50%;*/
            /*transform: translate(-50%,-50%);*/
        }
        #error-payment-container {
            background: #ef560d;
            color: #000;
        }
        #pending-payment-container {
            background: #1d72da;
            color: #fff;
        }
        #success-payment-container {
            background: #20ad07;
            color: #fff;
        }
        /*#payment-status-page{*/
        /*    position: relative;*/
        /*    min-height: 60vh;*/
        /*}*/
    </style>
    <div class="news-section-container" id="payment-status-page">
        <div id="success-payment-container" class="status-message">
            Your payment proceed successfully and your mac address activated.
        </div>
    </div>
@endsection
@section('script')

@endsection
