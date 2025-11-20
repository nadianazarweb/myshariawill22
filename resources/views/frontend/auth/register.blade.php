@extends('frontend.layouts.master')
@section('title')
Register
@endsection
@section('Style')
<style>
  .logo{
    width: 100%;
    max-width: 329px;
    display: block;
    margin: auto auto 30px auto;
  }
  .main_form {
    max-width: 500px;
    width: 100%;
    background-color: #f5f5f5;
  }
</style>
@endsection

@section('MainSection')

<div style="height:100vh; width:100vw; background-color:var(--custom_primary)">
    <div class="container h-100 w-100">
        <div class="h-100 w-100 d-flex align-items-center justify-content-center">
        <form class="main_form p-5 rounded shadow" action="{{route('register_submit')}}" method="POST">
          @csrf
          <img src="{{asset('assets/images/logoold.png')}}" class="logo" alt="">
    <h1 class="h3 mb-3 fw-normal">Register</h1>


    <div class="form-floating">
      <input type="text" class="form-control" name="full_name" id="floatingInput" placeholder="Full Name" required>
      <label for="floatingInput">Full Name</label>
    </div>


    <div class="form-floating mt-2">
      <input type="text" class="form-control" name="user_name" id="user_name" placeholder="User Name" required>
      <label for="user_name">User Name</label>
    </div>

    <div class="form-floating mt-2">
      <input type="email" class="form-control" name="email" id="email" autocomplete="false" placeholder="Email address" required>
      <label for="email">Email address</label>
    </div>
    <div class="form-floating mt-2">
      <input type="password" class="form-control" name="password" id="floatingPassword" placeholder="Password" required>
      <label for="floatingPassword">Password</label>
    </div>
    <div class="form-floating mt-2">
      <input type="text" class="form-control" name="contact_no" pattern="[0-9]{5}-[0-9]{6}" id="contact_no" placeholder="Contact No (12345-123456)" required>
      <label for="contact_no">Contact No (01234-567890)</label>
    </div>
    <div class="mt-2 d-flex justify-content-between align-items-center flex-md-row flex-column gap-3">
        <button class="btn custom_btn_primary BtnCreateAccount" type="submit" disabled>Create Account</button>
        <a href="{{ route('login') }}" class="btn custom_btn_primary">Already Have An Account? Log In</a>
    </div>

  </form>
        </div>
    </div>
</div>

@endsection

@section('Script')
{{-- <script src="{{url('frontend/js/jquery.mask.min.js')}}"></script> --}}
<script src="https://rawgit.com/RobinHerbots/Inputmask/4.x/dist/jquery.inputmask.bundle.js"></script>
<script>
    var isUserNameAvailable = false;
    var isEmailAvailable = false;

    $(function(){
        ResetFields();
        ValidatingUserName();
        ValidatingEmail();

        $("#contact_no").inputmask("07999-999999");
        // $("#contact_no").mask('99999-999999');
    });

    function ValidatingUserName(){
        var OldUserName = '';
        var UsernameDefaultPlaceholder = 'User Name';
        var UsernameDefaultLabel = 'User Name';

        $('input[name="user_name"]').on('blur', function(){
            var UserName = $(this).val().trim();
            $(this).val(UserName);
            if(UserName != OldUserName || (UserName == OldUserName && isUserNameAvailable == false)){
                if(UserName!=""){
                    isUserNameAvailable = false;
                    CreateAccountBtnDisabled();
                    $('input[name="user_name"]').removeClass('is-invalid is-valid');
                    $.ajax({
                        url:'/validateusername/'+UserName,
                        method:'GET',
                        success:function(e){
                            if(e==1){
                                $('input[name="user_name"]').addClass('is-invalid');
                                $('input[name="user_name"]').attr('placeholder',UsernameDefaultPlaceholder+' - Not Available');
                                $('label[for="user_name"]').html(UsernameDefaultLabel+' - <span class="text-danger">Not Available</span>');
                                CreateAccountBtnDisabled();
                            }else{
                                $('input[name="user_name"]').addClass('is-valid');
                                $('input[name="user_name"]').attr('placeholder',UsernameDefaultPlaceholder+' - Is Available');
                                $('label[for="user_name"]').html(UsernameDefaultPlaceholder+' - <span class="text-success">Is Available</span>');
                                isUserNameAvailable = true;
                                CreateAccountBtnDisabled();
                            }
                        }
                    });

                }else{
                    isUserNameAvailable = false;
                    CreateAccountBtnDisabled();
                    $('input[name="user_name"]').removeClass('is-invalid is-valid');
                    $('input[name="user_name"]').attr('placeholder',UsernameDefaultPlaceholder);
                    $('label[for="user_name"]').html(UsernameDefaultLabel);
                }
            }
            OldUserName = UserName;
        });
    }

    function ValidatingEmail(){
        var OldEmail = '';
        var EmailDefaultPlaceholder = 'Email address';
        var EmailDefaultLabel = 'Email address';

        $('input[name="email"]').on('blur', function(){
            var Email = $(this).val().trim();
            $(this).val(Email);
            if(Email != OldEmail || (Email == OldEmail && isEmailAvailable == false)){

                if(Email!=""){
                    if(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(Email)){

                    isEmailAvailable = false;
                    CreateAccountBtnDisabled();
                    $('input[name="email"]').removeClass('is-invalid is-valid');
                    $.ajax({
                        url:'/validateemail/'+Email,
                        method:'GET',
                        success:function(e){
                            if(e==1){
                                $('input[name="email"]').addClass('is-invalid');
                                $('input[name="email"]').attr('placeholder',EmailDefaultPlaceholder+' - Not Available');
                                $('label[for="email"]').html(EmailDefaultLabel+' - <span class="text-danger">Not Available</span>');
                                CreateAccountBtnDisabled();
                            }else{
                                $('input[name="email"]').addClass('is-valid');
                                $('input[name="email"]').attr('placeholder',EmailDefaultPlaceholder+' - Is Available');
                                $('label[for="email"]').html(EmailDefaultPlaceholder+' - <span class="text-success">Is Available</span>');
                                isEmailAvailable = true;
                                CreateAccountBtnDisabled();
                            }
                        }
                    });
                }else{
                    isEmailAvailable = false;
                    CreateAccountBtnDisabled();
                    $('input[name="email"]').removeClass('is-invalid is-valid');
                    $('input[name="email"]').attr('placeholder',EmailDefaultPlaceholder+' - Invalid email!');
                    $('label[for="email"]').html(EmailDefaultPlaceholder+' - <span class="text-danger">Invalid email!</span>');
                }
                }else{
                    isEmailAvailable = false;
                    CreateAccountBtnDisabled();
                    $('input[name="email"]').removeClass('is-invalid is-valid');
                    $('input[name="email"]').attr('placeholder',EmailDefaultPlaceholder);
                    $('label[for="email"]').html(EmailDefaultLabel);
                }
            }
            OldEmail = Email;
        });
    }



    function CreateAccountBtnDisabled(){
        if(isUserNameAvailable == true && isEmailAvailable == true){
            $('.BtnCreateAccount').prop('disabled',false);
        }else{
            $('.BtnCreateAccount').prop('disabled',true);
        }
    }

    function ResetFields(){
        $('input[name="full_name"], input[name="user_name"], input[name="email"], input[name="password"]').val('');
        CreateAccountBtnDisabled();
    }

</script>

@endsection
