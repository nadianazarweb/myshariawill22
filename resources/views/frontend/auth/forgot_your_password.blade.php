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
      <form class="main_form p-5 rounded shadow" action="{{route('forgot_your_password_submit')}}" method="POST">
        @csrf
        <img src="{{asset('assets/images/logoold.png')}}" class="logo" alt="">
        <h1 class="h3 fw-normal text-center">Forgot Your Password ?</h1>
        <p class="text-center mb-3">Please enter your email address below. We will send you a link to reset your password.</p>

        <div class="form-floating">
          <input type="email" class="form-control" required name="email" id="floatingInput" placeholder="name@example.com">
          <label for="floatingInput">Email address</label>
        </div>

        <div class="mt-2 d-flex justify-content-between align-items-center">
          <div>
            <button class="btn custom_btn_primary" type="submit">Reset Your Password</button>
          </div>
          <div>
            <a href="{{ route('login') }}" class="btn custom_btn_primary btn-sm">Go Back</a>
          </div>
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

</script>
@endsection