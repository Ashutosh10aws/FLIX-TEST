@extends('frontend.layouts.template',['menu'=>"activation"])
@section('content')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://www.paypal.com/sdk/js?client-id={{$paypal_client_id}}&currency=EUR"></script>

    <link rel="stylesheet" href="https://flixiptv.eu/admin/template/fonts/font-awesome/font-awesome.css">
    <link rel="stylesheet" href="https://flixiptv.eu/admin/template/fonts/web-icons/web-icons.min.css">
    <link rel="stylesheet" href="https://flixiptv.eu/admin/template/fonts/brand-icons/brand-icons.min.css">

    <style>
        #payment-methods-part{
            display: none;
        }
        label{
            color:#111;
            font-size:17px;
            margin-top:5px;
        }
        .submit-btn{
            width: 270px;
            border-radius: 10px;
            font-size: 25px;
        }
        #price-text {
            /*margin-top:20px;*/
            font-size: 17px;
            color: #333;
            font-weight: bold;
        }
        .payment-method-container {
            /*max-width: 400px;*/
            margin: 0 auto;
        }
        .payment-title {
            font-size: 20px;
            font-weight: normal;
            color: #222;
            margin-bottom: 5px;
        }
        .payment-method-item-container {
            /*font-size: 20px;*/
            /*background: #fff;*/
            /*padding: 5px 10px;*/
            /*border-radius: 5px;*/
            /*color: #444;*/
            position: absolute;
            font-size: 20px;
            background: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            color: #444;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: fit-content;
        }
        .payment-method-item-container[data-payment_type="card"] {
            border-bottom: none;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            cursor: pointer;
            transition: all 0.5s;
        }
        .payment-method-item-container[data-payment_type="paypal"] {
            border-radius: 0;
        }
        .payment-method-item-container[data-payment_type="sofort"] {
            border-radius: 0;
            border-top:0;
            border-bottom: none;
        }
        .payment-method-item-container[data-payment_type="crypto"] {
            border-top-left-radius:0;
            border-top-right-radius: 0;
        }
        .payment-method-item-container:hover,.payment-method-item-container.active {
            background: #ddd;
            color: #000;
        }
        .payment-method-icon {
            margin-right: 20px;
            /*width: 40px;*/
            text-align: center;
        }
        .payment-item-detail-container{
            display: none;
            overflow: hidden;
            transition: height 1.5s;
            border: 1px solid #666;
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 5px;
            margin-bottom:10px;
        }
        .payment-item-detail-container.active {
            display: block;
        }

        .card-payment-method-body{
            padding:10px 10px;
        }
        .card-error{
            color: #ee0404;
        }
        .error {
            color: #cc0000;
            margin-top: 0;
        }

        .border-bottom-rad-0{
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }
        .border-bottom-none{
            border-bottom: none;
        }
        #coin-select {
            border-top:none;
            padding-top:5px;
            padding-bottom: 10px;
            padding-left:20px;
            padding-right: 5px;
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 5px;
        }

        .hide{
            display: none;
        }
        .or{
            width: fit-content;
            background: #fff;
            z-index: 100;
            padding: 0 10px;
            font-size: 20px;
            color: #111;
            letter-spacing: 2px;
            overflow: hidden;
            position: absolute;
            left:50%;
            top: 50%;
            transform: translate(-50%,-50%);
        }
        .or-border{
            margin-top: 0;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            overflow: hidden;
            border-bottom: 2px solid #333;
            z-index: 0;
        }
        .or-container{
            height: 80px;
        }

        .payment-item-container{
            /*border: 1px solid #555;*/
            /*border-radius: 5px;*/

            position:relative;
            border: 1px solid #555;
            border-radius: 5px;
            padding-top: 20px;
            margin-top: 20px;
        }

        #disabled-container{
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: #fff;
            opacity: 0.5;
            z-index: 100;
        }
        #mac-invalid-info-container{
            display: none;
            color: #ff0000;
        }
        #mac-valid-info-container{
            display: none;
        }
        #expire-date-container{
            font-size: 17px;
            color: #333;
            font-weight: bold;
        }
        .accepted-cards-logo {
            background-image: url(data:image/png;base64,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);
            width: 200px;
            height: 40px;
            display: block;
            margin: auto;
        }

    </style>

    <div class="news-section-container">
        @if($activation_content)
            <?= $activation_content->contents ?>
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

        <div class="alert alert-success hide" id="success-message">

        </div>


        <div class="alert alert-danger hide" id="error-message">
        </div>

        <form id="form" method="post" action="{{url('/activation/saveActivation')}}">
            @csrf
            <h2 style="margin-top:50px;margin-bottom:0">Activation</h2>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label>Mac Address:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-map-marker"></i></span>
                            </div>
                            <input name="mac-address" id="mac-address" type="text" class="form-control mac_address" placeholder="00:aa:bb:cc:dd:11" required>
                            <div class="input-group-append">
                                <button type="button" onclick="checkValidMac();" class="btn btn-sm btn-outline-secondary waves-effect" style="border-radius: 0px;"><i class="fas fa-check"></i> Check</button>
                            </div>
                        </div>
                        <div id="mac-invalid-info-container">

                        </div>
                    </div>
                    <div id="mac-valid-info-container">
                        <div id="expire-date-container">
                            <span id="expire-date-label">
                                Expire Date after activate:
                            </span>
                            <span id="expire-date-value">
                                Life time.
                            </span>
                        </div>
                        <p id="price-text"><span>Price:</span><span class="price-value">&euro;{{$price}}</span></p>
                    </div>
                </div>
                <div class="col-12 col-md-6" id="payment-methods-part">
                    <div class="position-relative" id="payment-items-container">
                        @if($show_paypal==1)
                            <div id="paypal-element"></div>
                            @if($show_coin==1 || $show_mollie==1)
                                <div id="" class="position-relative or-container">
                                    <div class="or-border"></div>
                                    <div class="or text-center">Or</div>
                                </div>
                            @endif
                        @endif
                        @if($show_mollie==1)
                            <div class="payment-item-container">
                                <div class="payment-method-item-container">
                                    Pay with Mollie
                                </div>
                                <div class="text-center mt-10 mb-10">
                                    <button class="btn btn-warning submit-btn" id="submit-btn-1">Pay Now</button>
                                </div>
                            </div>
                            @if($show_coin==1)
                                <div class="position-relative or-container">
                                    <div class="or-border"></div>
                                    <div class="or text-center">Or</div>
                                </div>
                            @endif
                        @endif
                        @if($show_coin==1)
                            <div class="payment-item-container">
                                <div class="payment-method-item-container">
                                    Pay with Crypto Currency
                                </div>
                                <div id="coin-select">
                                    <label>Select Crypto Type</label>
                                    <select class="form-control" id="coin_type">
                                        @foreach($coin_list as $item)
                                            <option value="{{$item['code']}}">{{$item['name']}}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-center mt-10">
                                        <button class="btn btn-primary submit-btn" id="submit-btn">Pay Now</button>
                                    </div>
                                </div>
                            </div>
                    @endif
                    <!--
                        <div id="or-container" class="position-relative">
                            <div id="or-border"></div>
                            <div id="or" class="text-center">Or</div>
                        </div>
                        <div class="payment-item-container">
                            <div class="payment-method-item-container">
                                Pay with Credit Card
                            </div>
                            <div class="accepted-cards-logo"></div>
                            <div id="coin-select">
                             <!--   <div class="form-group">
                                    <label>
                                        Phone Number
                                        <input type="text" class="form-control" placeholder="Insert phone number" name="phonenumber" required>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label style="width: 97%;">
                                        <small style="font-weight:600">Card Number</small>

                                        <input type="text" class="form-control" placeholder="Insert card number" name="cardnumber" required>
                                    </label>
                                </div>
                                <div class="form-group">
                                   <label style="width: 97%;">
                                       <small style="font-weight:600">Card Holder Full Name</small>

                                        <input type="text" class="form-control" placeholder="Insert card holder full name" name="cardHolderFullName" required>
                                    </label>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                         <label style="width: 100%;">
                                              <small style="font-weight:600">Expire Month</small>

                                            <input type="number" min="01" max="12" minlength="2" maxlength="2" class="form-control" name="expMonth" placeholder="MM" required>
                                        </label>
                                    </div>
                                    <div class="col-sm-4">
                                        <label style="width: 100%;">
                                             <small style="font-weight:600">Expire Year</small>

                                            <input type="number" min="2021" max="2500" class="form-control" name="expYear" placeholder="YYYY" required>
                                        </label>
                                    </div>
                                     <div class="col-sm-4">
                                        <label style="width: 90%;">
                                            <small style="font-weight:600;font-size:12px">CVC</small>
                                            <input type="password" class="form-control" name="cvcNumber" placeholder="***" required>
                                        </label>
                                    </div>
                                </div>



                                <div class="text-center mt-10">
                                    <button class="btn btn-primary" name="payment_type" value="">Pay Now</button>
                                </div>
                            </div>
                             -->
                    </div>

                </div>
            </div>
    </div>
    </form>
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.js"></script>
    {{--    <script src="https://www.google.com/recaptcha/api.js?render={{env('GOOGLE_RECAPTCHA_KEY')}}"></script>--}}

    <script>
        var site_url=`<?php echo(url('')); ?>`, mac_address, timer, price=<?= $price ?>;
        var show_paypal=<?=$show_paypal?>;
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            setTimeout(()=>{
                $('.alert.alert-success').slideUp(100);
            },10000);
        })
        var payment_type="crypto";

        function checkValidMac(){
            mac_address=$('#mac-address').val();
            if(mac_address.length==17){
                $.ajax({
                    method:'post',
                    url:site_url+"/checkMacValid",
                    dataType:'json',
                    data:{
                        mac_address:mac_address
                    },
                    success:res=>{
                        if(res.status==="success"){
                            $('#payment-methods-part').slideDown();
                            // $('#expire-date-value').text(res.msg);
                            $('#mac-invalid-info-container').hide();
                            $('#mac-valid-info-container').slideDown();
                        }else{
                            $('#mac-valid-info-container').hide();
                            $('#mac-invalid-info-container').text(res.msg).slideDown();
                            $('#payment-methods-part').slideUp();
                        }
                    }
                })

            }
        }

        if(show_paypal==1){
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return fetch(`${site_url}/paypal/order/create`, {
                        method: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }).then(function(res) {
                        if(res.ok){
                            return res.json();
                        }else{
                            $('#error-message').text(res.msg).slideDown();
                        }
                    }).then(
                        function(orderData) {
                            return orderData.id;
                        },
                        function (error) {
                            console.log(error);
                        }
                    );
                },
                // Finalize the transaction
                onApprove: function(data, actions) {
                    return fetch(`${site_url}/paypal/order/capture?mac_address=${mac_address}&order_id=${data.orderID}`, {
                        method: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }).then(function(res) {
                        return res.json();
                    }).then(function(orderData) {
                        // Three cases to handle:
                        //   (1) Recoverable INSTRUMENT_DECLINED -> call actions.restart()
                        //   (2) Other non-recoverable errors -> Show a failure message
                        //   (3) Successful transaction -> Show a success / thank you message

                        // Your server defines the structure of 'orderData', which may differ
                        var errorDetail = Array.isArray(orderData.details) && orderData.details[0];

                        if (errorDetail && errorDetail.issue === 'INSTRUMENT_DECLINED') {
                            // Recoverable state, see: "Handle Funding Failures"
                            // https://developer.paypal.com/docs/checkout/integration-features/funding-failure/
                            return actions.restart();
                        }

                        if (errorDetail) {
                            var msg = 'Sorry, your transaction could not be processed.';
                            if (errorDetail.description) msg += '\n\n' + errorDetail.description;
                            if (orderData.debug_id) msg += ' (' + orderData.debug_id + ')';
                            // Show a failure message
                            return alert(msg);
                        }
                        // Show a success message to the buyer
                        $('#success-message').text('Thanks for your payment, your mac address activated now.').slideDown();
                        setTimeout(function () {
                            $('#success-message').slideUp();
                        },5000)
                        // alert('Transaction completed by ' + orderData.payer.name.given_name);
                    });
                }
            }).render('#paypal-element');
        }

        $('#submit-btn').click(function (e) {
            e.preventDefault();
            submitForm(e);
        })

        $('#submit-btn-1').click(function (e) {
            e.preventDefault();
            payment_type='mollie';
            submitForm(e);
        })

        function submitForm(e) {
            var isValid = $($(e.target).parents('form')).validate({
                rules: {
                    "mac-address": "required",
                },
                messages: {
                    name: "Mac address is needed",
                }
            });
            if(!isValid)
                return;
            else
                checkout();
        }

        function checkout() {
            $("<input />").attr("type", "hidden")
                .attr("name", "payment_type")
                .attr("value", payment_type)
                .appendTo("#form");
            $("<input />").attr("type", "hidden")
                .attr("name", "coin_type")
                .attr("value", $('#coin_type').val())
                .appendTo("#form");
            $("#form").submit();
        }

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

    </script>


    <script type="text/javascript">
        window.addEventListener('message', function(event) {
            if(event.origin !== 'https://api.paymentwall.com') return;
            var eventData = JSON.parse(event.data);
            if (eventData.event == 'paymentSuccess') {
                alert('Thank you for paying ' + eventData.data.amount + ' ' + eventData.data.currency);
            }
        },false);
    </script>
@endsection
