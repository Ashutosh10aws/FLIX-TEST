<!DOCTYPE html>
{{--<html class="no-js">--}}
<html>
    @include('frontend.layouts.head')
    <body>
        <div class="container">
            @include('frontend.layouts.header')
            <div class="content-container">
                @yield('content')
            </div>
            @include('frontend.layouts.footer')
            @yield('script')
        </div>

    </body>
</html>
