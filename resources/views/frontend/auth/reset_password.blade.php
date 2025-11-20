@extends('frontend.layouts.master')
@section('title')
Reset Your Password
@endsection
@section('Style')
<style>
  .logo {
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
      <form class="main_form p-5 rounded shadow FormResetPassword" action="{{route('reset_password_submit')}}" method="POST">
        @csrf
        <img src="{{asset('assets/images/logoold.png')}}" class="logo" alt="">
        <h1 class="h3 fw-normal text-center">Reset Your Password</h1>

        <div class="form-floating">
          <input type="password" class="form-control" required name="password" id="floatingInput" placeholder="">
          <label for="floatingInput">New Password</label>
        </div>

        <div class="form-floating mt-2">
          <input type="password" class="form-control" required name="confirm_password" id="floatingInput2" placeholder="">
          <label for="floatingInput">Confirm New Password</label>
        </div>

        <input type="hidden" value="{{ $_GET['token'] }}" name="password_reset_token">

        <div class="mt-2 d-flex justify-content-between align-items-center">
          <div>
            <button class="btn custom_btn_primary" type="submit">Save</button>
          </div>
          <!-- <div>
            <a href="{{ route('login') }}" class="btn custom_btn_primary btn-sm">Go Back</a>
          </div> -->
        </div>

      </form>

    </div>

  </div>
</div>

@endsection

@section('Script')
<script src="{{url('backend/app-assets/js/core/sweetalert2.js')}}"></script>
<script>
  var SuccessMsg = '<?= session('success_msg') ?>';
    if (SuccessMsg != "") {
        Swal.fire({
            position: 'center',
            icon: 'success',
            title: SuccessMsg,
            showConfirmButton: true,
        });
    }

    var FailureMsg = '<?= session('failure_msg') ?>';
    if (FailureMsg != "") {
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: FailureMsg,
            showConfirmButton: true,
        });
    }

    $('.FormResetPassword').on('submit',function(e){
            var Password = $('input[name="password"]').val();
            var ConfirmPassword = $('input[name="confirm_password"]').val();

            if(Password!==ConfirmPassword){
                e.preventDefault();
                Swal.fire({
                    icon: "error",
                    title: "Password Dont Match",
                });
            }

        });

</script>
@endsection