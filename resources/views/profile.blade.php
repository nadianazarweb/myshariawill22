@extends('/layouts/master')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{url('backend/app-assets/vendors/css/vendors.min.css')}}">
@stop

@section('title')
    <title>Profile Update</title>
@stop
@section('body')
<div class="content-wrapper container-xxl p-0">
    <div class="content-header row"></div>
    <div class="content-body">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-12 mb-1">
                        <div class="alert alert-success" style="display: none;" role="alert"><p class="alert-body alert-body-success"></p></div>
                        <div class="alert alert-warning" style="display: none;" role="alert"><p class="alert-body alert-body-warning"></p></div>
                    </div>
                    <div class="col-xl-12 col-md-12 col-12 mb-1">
                        <div class="form-group">
                            <label for="name">User name</label>
                            <input type="text" class="form-control" id="username" readonly value="{{$user->username}}" placeholder="Enter User Name">
                        </div>
                    </div>
                    <div class="col-xl-12 col-md-12 col-12 mb-1">
                        <div class="form-group">
                            <label for="number">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Enter Name" value="{{$user->name}}">
                        </div>
                    </div>
                    <div class="col-xl-12 col-md-12 col-12 mb-1">
                        <div class="form-group">
                            <label for="number">Email</label>
                            <input type="text" class="form-control" id="email" placeholder="Enter Email" value="{{$user->email}}">
                        </div>
                    </div>
                    <div class="col-xl-12 col-md-12 col-12 mb-1">
                        <div class="form-group">
                            <label for="number">Contact Number</label>
                            <input type="text" class="form-control" id="number" placeholder="Enter Number" value="{{$user->contact_no}}">
                        </div>
                    </div>
                    <div class="col-xl-12 col-md-12 col-12 mb-1">
                        <div class="form-group">
                            <label for="number">Existing Password</label>
                            <input type="password" class="form-control" id="existing-password" placeholder="Enter Existing Password">
                        </div>
                    </div>
                    <div class="col-xl-12 col-md-12 col-12 mb-1">
                        <div class="form-group">
                            <label for="number">Password</label>
                            <input type="password" class="form-control" id="password" placeholder="Enter Password">
                        </div>
                    </div>
                    <div class="col-xl-12 col-md-12 col-12 mb-1 submit">
                        <div class="form-group">
                            <button type="button" class="btn btn-primary waves-effect waves-float waves-light btn-submit">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
    <script src="{{url('backend/app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
    <script src="{{url('backend/app-assets/vendors/js/ui/jquery.sticky.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js" integrity="sha256-yE5LLp5HSQ/z+hJeCqkz9hdjNkk1jaiGG0tDCraumnA=" crossorigin="anonymous" ></script>
    <script>
        $(document).ready(function(){
            $("#number").mask('99999999999');
            $("#email").blur(function(){
                var value = $(this).val();
                var link = '/dashboard/check?email=' + value;
                $.get(link,function(res){
                    if(res['status'] == "success"){
                        if($("#email").hasClass('is-invalid')){
                            $("#email").removeClass('is-invalid');
                        }
                        $("#email").addClass('is-valid');
                    }
                    else if(res['status'] == "warning"){
                        if($("#email").hasClass('is-valid')){
                            $("#email").removeClass('is-valid');
                        }
                        $("#email").addClass('is-invalid');
                        $(".btn-submit").attr("disabled","disabled");
                    }
                });
            });

            $("#number").blur(function(){
                var value = $(this).val();
                var link = '/dashboard/check?number=' + value;
                $.get(link,function(res){
                    if(res['status'] == "success"){
                        if($("#number").hasClass('is-invalid')){
                            $("#number").removeClass('is-invalid');
                        }
                        $("#number").addClass('is-valid');
                    }
                    else if(res['status'] == "warning"){
                        if($("#number").hasClass('is-valid')){
                            $("#number").removeClass('is-valid');
                        }
                        $("#number").addClass('is-invalid');
                        $(".btn-submit").attr("disabled","disabled");
                    }
                });
            });

            $(".btn-submit").click(function(){
                var name = $("#name").val();
                var number = $("#number").val();
                var email = $("#email").val();
                var password = $("#password").val();
                var currpassword = $("#existing-password").val();
                $(this).html("<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true'></span>");
                $(this).attr("disabled","disabled");
                $.ajax({
                    url : "/dashboard/profile-update-submit",
                    type : "POST",
                    data : {
                        "_token": "{{ csrf_token() }}",
                        name : name,
                        number : number,
                        email : email,
                        password : password,
                        currpassword : currpassword,
                    },
                    success:function(response){
                        if(response['status'] == "success"){
                            $(".alert-body-success").html(response['message']);
                            $(".alert-success").slideDown();
                            $(".btn-submit").html("Submit");
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        }
                        else if(response['status'] == "warning"){
                            $(".alert-body-warning").html(response['message']);
                            $(".alert-warning").slideDown();
                            $(".alert-success").slideUp();
                            $(".btn-submit").html("Submit");
                            $(".btn-submit").removeAttr('disabled');
                        }
                    }
                });
            });
        });
    </script>
@stop