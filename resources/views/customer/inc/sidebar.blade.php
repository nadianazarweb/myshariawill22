@php
$page = explode("/", $_SERVER['REQUEST_URI'])[2];
@endphp
<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header d-flex align-items-center">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand m-0" href="{{route('dashboard')}}">
                    <span class="brand-logo">
                        <img src="{{asset('assets/images/logoold.png')}}"
                            style="width: 100%;max-width: 150px;display: block" alt="">
                    </span>
                    {{-- <h2 class="brand-text">{{ucwords(config('app.name'))}}</h2> --}}
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                        <li class="nav-item @if($page == "dashboard") active @endif"><a class="d-flex align-items-center"
                    href="{{route('customer_dashboard')}}"><i data-feather='home'></i><span
                        class="menu-title text-truncate" data-i18n="Dashboard">Dashboard</span></a></li>
            <li class="nav-item @if($page == "forms") active @endif"><a class="d-flex align-items-center"
                    href="{{route('customer_forms')}}"><i data-feather='file-text'></i><span
                        class="menu-title text-truncate" data-i18n="My Form">My Form</span></a></li>

                        <li class="nav-item @if($page == "settings") active @endif"><a class="d-flex align-items-center"
                    href="{{route('customer_settings')}}"><i data-feather='settings'></i><span
                        class="menu-title text-truncate" data-i18n="Settings">Settings</span></a></li>

                        <!-- <li class="nav-item @if($page == "book_appointment") active @endif"><a class="d-flex align-items-center"
                    href="{{route('customer_book_appointment')}}"><i data-feather='calendar'></i><span
                        class="menu-title text-truncate" data-i18n="Calendar">Appointments</span></a></li> -->

        </ul>
    </div>
</div>
<!-- END: Main Menu-->