@extends('frontend.layouts.master')
@section('title')
Login
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
    background-color: #fff;
  }
</style>
@endsection

@section('MainSection')

<div style="height:100vh; width:100vw; background-color:var(--custom_primary)">
  <div class="container h-100 w-100">
    <div class="h-100 w-100 d-flex align-items-center justify-content-center">
      <form class="main_form p-5 rounded shadow" action="{{route('userlogin_submit')}}" method="POST">
        @csrf
        <img src="{{asset('assets/images/logoold.png')}}" class="logo" alt="">
        <h1 class="h3 mb-3 fw-normal">Login</h1>

        <div class="form-floating">
          <input type="email" class="form-control" name="email" id="floatingInput" placeholder="name@example.com">
          <label for="floatingInput">Email address</label>
        </div>
        <div class="form-floating mt-2">
          <input type="password" class="form-control" name="password" id="floatingPassword" placeholder="Password">
          <label for="floatingPassword">Password</label>
        </div>

        <div class="mt-2 d-flex justify-content-between align-items-center flex-md-row flex-column gap-3">
          <div>
            <button class="btn custom_btn_primary" type="submit">Sign in</button>
            <a href="{{ route('register') }}" class="btn custom_btn_primary" type="button">Register</a>
          </div>
          <div>
            <a href="{{ route('forgot_your_password') }}" class="btn custom_btn_primary btn-sm">Forgot your password?</a>
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
  var SessionMsg = <?= json_encode(session('msg')) ?>;
  if (SessionMsg != "" && SessionMsg != null) {
    Swal.fire({
      position: 'center',
      icon: 'info',
      title: SessionMsg,
    });
  }

</script>
@endsection