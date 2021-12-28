<?php
    $menu= isset($menu) ? $menu : '';
?>
<nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided"
                data-toggle="menubar">
            <span class="sr-only">Toggle navigation</span>
            <span class="hamburger-bar"></span>
        </button>
        <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse"
                data-toggle="collapse">
            <i class="icon wb-more-horizontal" aria-hidden="true"></i>
        </button>
        <div class="navbar-brand navbar-brand-center site-gridmenu-toggle" data-toggle="gridmenu" style="padding:0.5715rem 1.429rem">
            <img class="navbar-brand-logo" src="{{asset('/images/iptv_icon.jpeg')}}" title="IPTV" style="height:100%;margin-top:0;border-radius:50%">
            <span class="navbar-brand-text hidden-xs-down">FLEX IPTV</span>
        </div>
        <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-search"
                data-toggle="collapse">
            <span class="sr-only">Toggle Search</span>
            <i class="icon wb-search" aria-hidden="true"></i>
        </button>
    </div>

    <div class="navbar-container container-fluid">
        <!-- Navbar Collapse -->
        <div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
            <!-- Navbar Toolbar -->
            <ul class="nav navbar-toolbar">
                <li class="nav-item hidden-float" id="toggleMenubar">
                    <a class="nav-link" data-toggle="menubar" href="#" role="button">
                        <i class="icon hamburger hamburger-arrow-left">
                            <span class="sr-only">Toggle menubar</span>
                            <span class="hamburger-bar"></span>
                        </i>
                    </a>
                </li>
                <li class="nav-item hidden-float">
                    <a class="nav-link icon wb-search" data-toggle="collapse" href="#" data-target="#site-navbar-search"
                       role="button">
                        <span class="sr-only">Toggle Search</span>
                    </a>
                </li>
            </ul>
            <!-- End Navbar Toolbar -->


            <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">
                <li class="nav-item dropdown">
                    <a class="nav-link navbar-avatar" data-toggle="dropdown" href="#" aria-expanded="false"
                       data-animation="scale-up" role="button">
                <span class="avatar avatar-online">
                  <img src="{{asset('/images/iptv_icon.jpeg')}}" alt="...">
                  <i></i>
                </span>
                    </a>
                    <div class="dropdown-menu" role="menu">
                        <a class="dropdown-item" role="menuitem" href="{{url('/admin/logout')}}"
                        >
{{--                            onclick="event.preventDefault();--}}
{{--                            document.getElementById('logout-form').submit();"--}}
                            <i class="icon wb-power" aria-hidden="true"></i>
                            Logout
                        </a>
{{--                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">--}}
{{--                            @csrf--}}
{{--                        </form>--}}
                    </div>
                </li>
            </ul>
            <!-- End Navbar Toolbar Right -->
        </div>
        <!-- End Navbar Collapse -->

        <!-- Site Navbar Seach -->
        <div class="collapse navbar-search-overlap" id="site-navbar-search">
            <form role="search">
                <div class="form-group">
                    <div class="input-search">
                        <i class="input-search-icon wb-search" aria-hidden="true"></i>
                        <input type="text" class="form-control" name="site-search" placeholder="Search...">
                        <button type="button" class="input-search-close icon wb-close" data-target="#site-navbar-search"
                                data-toggle="collapse" aria-label="Close"></button>
                    </div>
                </div>
            </form>
        </div>
        <!-- End Site Navbar Seach -->
    </div>
</nav>
<div class="site-menubar">
    <div class="site-menubar-body">
        <div>
            <div>
                <ul class="site-menu" data-plugin="menu">
                    <li class="site-menu-category"> </li>
                    @if(Auth::guard('admin')->user()->is_admin==1)
                        <li class="site-menu-item has-sub <?= ($menu=='news' || $menu=='news-create') ? 'open active' : ''?>">
                            <a href="javascript:void(0)">
                                <i class="site-menu-icon far fa-newspaper-o" aria-hidden="true"></i>
                                <span class="site-menu-title">News</span>
                                <span class="site-menu-arrow"></span>
                            </a>
                            <ul class="site-menu-sub">
                                <li class="site-menu-item <?= $menu=='news-create' ? 'active' : ''?>">
                                    <a href="{{url('/admin/news/create')}}" class="animsition-link">
                                        <span class="site-menu-title">New Section</span>
                                    </a>
                                </li>
                                <li class="site-menu-item <?= $menu=='news' ? 'active' : ''?>">
                                    <a href="{{url('/admin/news/')}}">
                                        <span class="site-menu-title">Sections</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="site-menu-item <?= $menu=='faq' ? 'active' : ''?>">
                            <a href="{{url('admin/faq')}}">
                                <i class="site-menu-icon fas fa-tasks" aria-hidden="true"></i>
                                <span class="site-menu-title">FAQ</span>
                            </a>
                        </li>

                        <li class="site-menu-item has-sub <?= ($menu=='instruction_tags' || $menu=='instruction_summary') ? 'open active' : ''?>">
                            <a href="javascript:void(0)">
                                <i class="site-menu-icon far fa-newspaper-o" aria-hidden="true"></i>
                                <span class="site-menu-title">Instructions</span>
                                <span class="site-menu-arrow"></span>
                            </a>
                            <ul class="site-menu-sub">
                                <li class="site-menu-item <?= $menu=='instruction_tags' ? 'active' : ''?>">
                                    <a href="{{url('/admin/instruction/tags')}}">
                                        <span class="site-menu-title">Tags</span>
                                    </a>
                                </li>
                                <li class="site-menu-item <?= $menu=='instruction_summary' ? 'active' : ''?>">
                                    <a href="{{url('/admin/instruction/page/0')}}" class="animsition-link">
                                        <span class="site-menu-title">Summary Page</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="site-menu-item <?= $menu=='mylist' ? 'active' : ''?>">
                            <a href="{{url('admin/mylist')}}">
                                <i class="site-menu-icon fas fa-tasks" aria-hidden="true"></i>
                                <span class="site-menu-title">My List Content</span>
                            </a>
                        </li>
                        <li class="site-menu-item <?= $menu=='activation' ? 'active' : ''?>">
                            <a href="{{url('admin/activation')}}">
                                <i class="site-menu-icon fas fa-tasks" aria-hidden="true"></i>
                                <span class="site-menu-title">Activation Content</span>
                            </a>
                        </li>
                        <li class="site-menu-item has-sub <?= ($menu=='pl_package_lists' || $menu=='pl_package_create') ? 'open active' : ''?>">
                            <a href="javascript:void(0)">
                                <i class="site-menu-icon far fa-newspaper-o" aria-hidden="true"></i>
                                <span class="site-menu-title">PlayLists Pricing</span>
                                <span class="site-menu-arrow"></span>
                            </a>
                            <ul class="site-menu-sub">
                                <li class="site-menu-item <?= $menu=='pl_package_lists' ? 'active' : ''?>">
                                    <a href="{{url('/admin/playlist_package')}}">
                                        <span class="site-menu-title">Package Lists</span>
                                    </a>
                                </li>
                                <li class="site-menu-item <?= $menu=='pl_package_create' ? 'active' : ''?>">
                                    <a href="{{url('/admin/playlist_package/create')}}" class="animsition-link">
                                        <span class="site-menu-title">Create Package</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="site-menu-item <?= $menu=='terms' ? 'active' : ''?>">
                            <a href="{{url('admin/terms')}}">
                                <i class="site-menu-icon fas fa-tasks" aria-hidden="true"></i>
                                <span class="site-menu-title">Terms and Conditions</span>
                            </a>
                        </li>
                        <li class="site-menu-item <?= $menu=='privacy' ? 'active' : ''?>">
                            <a href="{{url('admin/privacy')}}">
                                <i class="site-menu-icon fas fa-tasks" aria-hidden="true"></i>
                                <span class="site-menu-title">Privacy and Policy</span>
                            </a>
                        </li>

                        <li class="site-menu-item <?= $menu=='transactions' ? 'active' : ''?>">
                            <a href="{{url('admin/transactions')}}">
                                <i class="site-menu-icon fas fa-tasks" aria-hidden="true"></i>
                                <span class="site-menu-title">Transactions</span>
                            </a>
                        </li>
                        <li class="site-menu-item <?= $menu=='epg-code' ? 'active' : ''?>">
                            <a href="{{url('admin/epg-code')}}">
                                <i class="site-menu-icon fas fa-tasks" aria-hidden="true"></i>
                                <span class="site-menu-title">Epg Codes</span>
                            </a>
                        </li>
                        <li class="site-menu-item <?= $menu=='epg-server' ? 'active' : ''?>">
                            <a href="{{url('admin/epg-server')}}">
                                <i class="site-menu-icon fas fa-tasks" aria-hidden="true"></i>
                                <span class="site-menu-title">Epg Servers</span>
                            </a>
                        </li>
                        <li class="site-menu-item <?= $menu=='epg-code' ? 'active' : ''?>">
                            <a href="{{url('admin/android-update')}}">
                                <i class="site-menu-icon fas fa-tasks" aria-hidden="true"></i>
                                <span class="site-menu-title">Android Update</span>
                            </a>
                        </li>
                        <li class="site-menu-item has-sub <?= strpos($menu,'language-')!==false ? 'open active' : '' ?>">
                            <a href="javascript:void(0)">
                                <i class="site-menu-icon far fa-newspaper-o" aria-hidden="true"></i>
                                <span class="site-menu-title">Languages</span>
                                <span class="site-menu-arrow"></span>
                            </a>
                            <ul class="site-menu-sub">
                                <li class="site-menu-item <?= $menu=='language-code' ? 'active' : ''?>">
                                    <a href="{{url('/admin/language')}}">
                                        <span class="site-menu-title">Language Lists</span>
                                    </a>
                                </li>
                                <li class="site-menu-item <?= $menu=='language-word' ? 'active' : ''?>">
                                    <a href="{{url('/admin/word')}}" class="animsition-link">
                                        <span class="site-menu-title">Words</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="site-menu-item has-sub <?= strpos($menu,'-setting')!=false ? 'open active' : ''?>">
                            <a href="javascript:void(0)">
                                <i class="site-menu-icon far fa-newspaper-o" aria-hidden="true"></i>
                                <span class="site-menu-title">Settings</span>
                                <span class="site-menu-arrow"></span>
                            </a>
                            <ul class="site-menu-sub">
                                <li class="site-menu-item <?= $menu=='seo-setting' ? 'active' : ''?>">
                                    <a href="{{url('/admin/seo_setting')}}">
                                        <span class="site-menu-title">Seo Setting</span>
                                    </a>
                                </li>
                                <li class="site-menu-item <?= $menu=='demo_url-setting' ? 'active' : ''?>">
                                    <a href="{{url('/admin/showDemoUrl')}}" class="animsition-link">
                                        <span class="site-menu-title">Demo M3U URL</span>
                                    </a>
                                </li>
                                <li class="site-menu-item <?= $menu=='app_background-setting' ? 'active' : ''?>">
                                    <a href="{{url('/admin/showAppBackground')}}" class="animsition-link">
                                        <span class="site-menu-title">App Backgrounds</span>
                                    </a>
                                </li>
                                <li class="site-menu-item <?= $menu=='advert-setting' ? 'active' : ''?>">
                                    <a href="{{url('/admin/showAdverts')}}" class="animsition-link">
                                        <span class="site-menu-title">Adverts</span>
                                    </a>
                                </li>
                                <li class="site-menu-item <?= $menu=='stripe-payment' ? 'active' : ''?>">
                                    <a href="{{url('/admin/showPaymentVisibility')}}" class="animsition-link">
                                        <span class="site-menu-title">Payment Visibility</span>
                                    </a>
                                </li>
                                <li class="site-menu-item <?= $menu=='stripe-setting' ? 'active' : ''?>">
                                    <a href="{{url('/admin/showStripeSetting')}}" class="animsition-link">
                                        <span class="site-menu-title">Stripe Setting</span>
                                    </a>
                                </li>
                                <li class="site-menu-item <?= $menu=='paypal-setting' ? 'active' : ''?>">
                                    <a href="{{url('/admin/showPaypalSetting')}}" class="animsition-link">
                                        <span class="site-menu-title">Paypal Setting</span>
                                    </a>
                                </li>
    {{--                            <li class="site-menu-item <?= $menu=='mollie-setting' ? 'active' : ''?>">--}}
    {{--                                <a href="{{url('/admin/showMollieSetting')}}" class="animsition-link">--}}
    {{--                                    <span class="site-menu-title">Mollie Setting</span>--}}
    {{--                                </a>--}}
    {{--                            </li>--}}
                                <li class="site-menu-item <?= $menu=='crypto-api-setting' ? 'active' : ''?>">
                                    <a href="{{url('/admin/showCryptoApiKey')}}" class="animsition-link">
                                        <span class="site-menu-title">Crypto Api Key</span>
                                    </a>
                                </li>
                                <li class="site-menu-item <?= $menu=='crypto-coin-list-setting' ? 'active' : ''?>">
                                    <a href="{{url('/admin/showCoinList')}}" class="animsition-link">
                                        <span class="site-menu-title">Coin List</span>
                                    </a>
                                </li>
                            </ul>
                            <li class="site-menu-item <?= $menu=='resellers' ? 'active' : ''?>">
                                <a href="{{url('admin/resellers')}}">
                                    <i class="site-menu-icon fas fa-tasks" aria-hidden="true"></i>
                                    <span class="site-menu-title">Resellers</span>
                                </a>
                            </li>
                            <li class="site-menu-item <?= $menu=='remove-device' ? 'active' : ''?>">
                                <a href="{{url('admin/remove-old-device')}}">
                                    <i class="site-menu-icon fas fa-tasks" aria-hidden="true"></i>
                                    <span class="site-menu-title">Remove Old Device</span>
                                </a>
                            </li>
                        </li>
                    @endif
                    <li class="site-menu-item <?= $menu=='playlists' ? 'active' : ''?>">
                        <a href="{{url('admin/playlists')}}">
                            <i class="site-menu-icon fas fa-tasks" aria-hidden="true"></i>
                            <span class="site-menu-title">PlayLists</span>
                        </a>
                    </li>
                    <li class="site-menu-item <?= $menu=='profile' ? 'active' : ''?>">
                        <a href="{{url('admin/profile')}}">
                            <i class="site-menu-icon fas fa-tasks" aria-hidden="true"></i>
                            <span class="site-menu-title">Profile</span>
                        </a>
                    </li>

                </ul>
                <div class="site-menubar-section">
                </div>
            </div>
        </div>
    </div>
    <div class="site-menubar-footer">

    </div>
</div>

