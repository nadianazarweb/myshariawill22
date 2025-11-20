@extends('frontend.layouts.master')
@section('title')
Payment Required
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
    max-width: 400px;
    width: 100%;
  }

  /* .btn_back:hover .left_arrow_icon{
    margin-right:20px !important;
  } */

  .left_arrow_icon {
    right: 100%;
    margin-right: 10px;
  }

  .right_arrow_icon {
    left: 100%;
    margin-left: 5px;
  }

  .arrow_icon {
    position: absolute;
  }

  .btn_back:hover .arrow_icon {
    margin-right: 15px;
    transition: 0.5s;
  }

  .btn_next:hover .arrow_icon {
    margin-left: 10px;
    transition: 0.5s;
  }

  .qna_main_div {
    max-width: 925px;
    margin: auto;
  }

  .question_heading {
    font-size: 35px;
  }

  .question_alphabet {
    border-radius: 69px;
    padding: 5px 14px;
    background-color: rgb(234, 234, 234);
    font-size: 22px;
    pointer-events: none;
    color: #c1c1c1;
    font-weight: bold;
    margin-right: 15px;
  }

  .option_alphabet {
    border: solid 1px rgb(255, 221, 216);
    /* border-radius:6px; */
    padding: 0px 15px;
    background-color: rgb(255, 255, 255);
  }

  .option_label {
    position: relative;
    border: solid 1px rgb(255, 221, 216);
    background-color: rgb(255, 249, 248);
    border-radius: 6px;
    /* padding:15px 20px 15px 20px; */
    cursor: pointer;
    /* height:70px; */
    font-size: 29px;
    color: var(--custom_primary);
  }

  .nav_header {
    background-image: linear-gradient(140deg, #424D7C 0%, #35757D 72%)
  }

  .option_label_child_div {
    padding: 15px;
    position: relative;
    z-index: 1;
  }

  .option_text {
    margin-left: 1.5rem;
  }

  .input_radio_option {
    position: absolute;
    z-index: -1;
  }

  .option_label::after {
    content: '';
    position: absolute;
    top: 0;
    height: 100%;
    left: 0;
    width: 0%;
    background-color: var(--custom_primary);
    transition: 0.3s ease-in-out;
  }

  .input_radio_option:checked+.option_label::after {
    width: 100%;
  }

  .input_radio_option:checked+.option_label .option_text {
    color: #f5f5f5;
  }

  .options_main_div {
    opacity: 0;
    animation: show_options_animation 0.8s forwards;
  }

  @keyframes show_options_animation {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
  }

  .refreshing_icon {
    font-size: 4em;
  }

  textarea, input[type="text"] {
    width: 100%;
    border-color: var(--custom_primary);
    border-radius: 6px;
    border-width: 3px;
    padding: 15px;
  }
</style>
@endsection

@section('MainSection')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="py-5">
                <h1 class="mb-4">Questionnaire Completed Successfully!</h1>
                <h4 class="mb-4">You've completed the questionnaire and now need to pay £99 to access your forms and continue with the process.</h4>
                <p class="mb-4">You'll be redirected to the payment page in a few seconds...</p>
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('Script')
<script src="{{url('backend/app-assets/js/core/sweetalert2.js')}}"></script>
<script>
    $(function(){
        Swal.fire({
        position: 'center',
        icon: 'info',
        title: "Questionnaire Completed!",
        text: "Now you need to pay £99 to access your forms and continue with the process.",
        showConfirmButton: true,
        confirmButtonText: 'Proceed to Payment',
        allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('payment_checkout') }}";
            }
        });
    });
</script>
@endsection