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
            <li class=" nav-item @if($page == "dashboard") active @endif"><a class="d-flex align-items-center"
                    href="{{route('manager_dashboard')}}"><i data-feather="home"></i><span class="menu-title text-truncate"
                        data-i18n="Dashboards">Dashboard</span></a></li>

                        <li class=" nav-item {{ $page=='reports' || $page=='approved_reports' ? 'sidebar-group-active open' : '' }} @if($page == "reports") @endif has-sub"><a
                    class="d-flex align-items-center" href="{{route('manager_reports')}}"><i
                        data-feather="file-text"></i><span class="menu-title text-truncate"
                        data-i18n="Dashboards">Reports</span></a>
                    <ul class="menu-content">
                        <li class="{{ $page=='reports' ? 'active' : '' }}"><a href="{{route('manager_reports')}}"><i
                        data-feather="circle"></i><span class="menu-title text-truncate"
                        data-i18n="Pending Reports">Pending Reports</a></li>
                        <li class="{{ $page=='approved_reports' ? 'active' : '' }}"><a href="{{route('manager_approved_reports')}}"><i
                        data-feather="circle"></i><span class="menu-title text-truncate"
                        data-i18n="Approved Reports">Approved Reports</a></li>
                    </ul>
                    </li>
<!-- 
                        <li class=" nav-item @if($page == "appointments") active @endif"><a
                    class="d-flex align-items-center" href="{{route('manager_appointments')}}"><i
                        data-feather="calendar"></i><span class="menu-title text-truncate"
                        data-i18n="Dashboards">Appointments</span></a></li> -->


        </ul>
    </div>
</div>
<!-- END: Main Menu-->