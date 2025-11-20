<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <title>Login</title>
    <link rel="apple-touch-icon" href="{{url('backend/app-assets/images/ico/apple-icon-120.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{url('storage')}}/{{setting('site.favicon')}}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{url('backend/app-assets/vendors/css/vendors.min.css')}}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{url('backend/app-assets/css/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('backend/app-assets/css/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('backend/app-assets/css/colors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('backend/app-assets/css/components.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('backend/app-assets/css/themes/dark-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('backend/app-assets/css/themes/bordered-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('backend/app-assets/css/themes/semi-dark-layout.css')}}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{url('backend/app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('backend/app-assets/css/plugins/forms/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('backend/app-assets/css/pages/page-auth.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{url('backend/assets/css/style.css')}}">
    <!-- END: Custom CSS-->
    <style>
        .login_logo{
            width: 100%;
            max-width: 300px;
            display: block;
            margin-bottom: 35px;
        }
    </style>
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="auth-wrapper auth-v2">
                    <div class="auth-inner row m-0">
                        <!-- Brand logo-->
                        
                        <!-- /Brand logo-->
                        <!-- Left Text-->
                        <div class="d-none d-lg-flex col-lg-8 align-items-center p-0">
                            <div class="w-100 d-lg-flex align-items-center justify-content-center"><img class="img-fluid" src="{{url('backend/app-assets/images/pages/mysharialogin2.webp')}}" alt="Login V2" /></div>
                        </div>
                        <!-- /Left Text-->
                        <!-- Login-->
                        <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                            <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                                <img src="{{asset('assets/images/logoold.png')}}" class="login_logo" alt="">
                                <p class="card-text mb-2">Please sign-in to your account and start the adventure</p>
                                <form class="auth-login-form mt-2">
                                    <div class="alert alert-success" style="display: none;" role="alert"><p class="alert-body alert-body-success"></p></div>
                                    <div class="alert alert-danger alert-dismissible fade show" style="display: none;" role="alert"><p class="alert-body alert-body-danger"></p><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>
                                    <div class="alert alert-warning alert-dismissible fade show" style="display: none;" role="alert"><p class="alert-body alert-body-warning"></p><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>
                                    <div class="form-group">
                                        <label class="form-label" for="login-email">Email/Password/Contact No</label>
                                        <input class="form-control" id="login-email" type="text" />
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input class="form-control form-control-merge" id="login-password" type="password" name="login-password" placeholder="············" aria-describedby="login-password" tabindex="2" />
                                            <div class="input-group-append"><span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span></div>
                                        </div>
                                    </div>
                                    
                                    <button type="button" class="btn btn-primary btn-block btn-submit" tabindex="4">Sign in</button>
                                </form>
                                
                            </div>
                        </div>
                        <!-- /Login-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->


    <!-- BEGIN: Vendor JS-->
    <script src="{{url('backend/app-assets/vendors/js/vendors.min.js')}}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="{{url('backend/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{url('backend/app-assets/js/core/app-menu.js')}}"></script>
    <script src="{{url('backend/app-assets/js/core/app.js')}}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="{{url('backend/app-assets/js/scripts/pages/page-auth-login.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <!-- END: Page JS-->

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        });

        $(document).ready(function(){

            $(".btn-submit").click(function(){
                var email = $("#login-email").val();
                var password = $("#login-password").val();
                if(email != ""){
                    if(password != ""){
                        $(this).html("<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>");
                        $(this).attr("disabled","disabled");
                        $.ajax({
                            url : "/login/submit",
                            type : "POST",
                            data : {
                                email : email,
                                password : password,
        						"_token": "{{ csrf_token() }}",
                            },
                            success:function(response){
                                if(response['status'] == 'success'){
                                    $(".alert-body-success").html(response['message'] + "&nbsp;<span class='spinner-border spinner-border-sm text-success' role='status' aria-hidden='true'></span>");
                                    $(".alert-success").slideDown();
                                    $(".btn-submit").html("Sign in");
                                    setTimeout(() => {
                                        window.location.href = response['redirect'];
                                    }, 1000);
                                }
                                else if(response['status'] == 'danger'){
                                    $(".alert-body-danger").html(response['message']);
                                    $(".alert-danger").slideDown();
                                    $(".btn-submit").html("Sign in");
                                    $(".btn-submit").removeAttr("disabled");
                                }
                                else if(response['status'] == 'warning'){
                                    $(".alert-body-warning").html(response['message']);
                                    $(".alert-warning").slideDown();
                                    $(".btn-submit").html("Sign in");
                                    $(".btn-submit").removeAttr("disabled");
                                }
                            }
                        });
                    }
                    else{
                        $("#login-password").addClass('error');
                        $("#login-password").focus();
                    }
                }
                else{
                    $("#login-email").addClass('error');
                    $("#login-email").focus();
                }
            });
        });
    </script>
</body>
<!-- END: Body-->

</html>