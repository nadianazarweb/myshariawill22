@extends('frontend.layouts.master')

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

    .btn_back,
    .btn_next,
    .btn_proceed {
        pointer-events: all;
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
        font-size: 20px;
        color:var(--custom_primary);
    }

    .question_alphabet {
        border-radius: 69px;
        padding: 5px 14px;
        background-color: rgb(234, 234, 234);
        font-size: 22px;
        pointer-events: none;
        color: var(--custom_primary);
        font-weight: bold;
        margin-right: 15px;
    }

    .option_alphabet {
        /* border: solid 1px rgb(255, 221, 216); */
        border:solid 2px var(--custom_primary);
        /* border-radius:6px; */
        border-radius:50px;
        /* padding: 0px 15px; */
        padding:5px;
        background-color: rgb(255, 255, 255);
        height:25px;
        width:25px;
        overflow:hidden;
    }

    .custom_radio{
        height: 100%;
        width: 100%;
        border-radius: 50px;
    }

    .option_label {
        position: relative;
        border: 1px solid #ccc;
        /* background-color: rgb(255, 249, 248); */
        background-color:#424d7c14;
        border-radius: 6px;
        /* padding:15px 20px 15px 20px; */
        cursor: pointer;
        /* height:70px; */
        font-size: 20px;
        color: var(--custom_primary);
        overflow:hidden;
        transition: 0.2s ease-in-out;
    }

    .nav_header {
        background-image: linear-gradient(140deg, #424D7C 0%, #35757D 72%)
    }

    .option_label_child_div {
        padding: 15px;
        position: relative;
        z-index: 1;
        display:flex;
        align-items:center;
    }

    .option_text {
        margin-left: 1.5rem;
    }

    .input_radio_option {
        position: absolute;
        z-index: -1;
        opacity:0;
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

    .input_radio_option:checked+.option_label .option_alphabet>.custom_radio {
        background-color: var(--custom_primary);
    }

    .input_radio_option:checked+.option_label {
        border:1px solid var(--custom_primary);
        background-color:#424d7c4a;
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

    textarea {
        width: 100%;
        border-color: var(--custom_primary);
        border-radius: 6px;
        border-width: 3px;
        padding: 15px;
    }

    .get_started_main_div {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 2;
        /* display:none; */
    }

    .logo {
        width: 100%;
        max-width: 329px;
        display: block;
        margin: auto auto 30px auto;
    }

    .main_content {
        max-width: 754px;
        width: 100%;
    }

    .or_text_div {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .or_text_div_border {
        position: absolute;
        border: 1px solid #dee2e6;
        width: 100%;
        top: 50%;
        z-index: -1;
    }

    .or_text_main_div {
        max-width: 500px;
        width: 100%;
        display: block;
        margin: auto;
    }

    .append_next_back_btns_here {
        z-index: 1;
        pointer-events: none;
    }

    .almost_there_div {
        max-width: 875px;
        margin: auto;
    }

    footer {
        background-color: var(--custom_primary);
        color: #fff;
        text-align: center;
        padding: 10px;
        width: 100%;
        font-size: 0.8em;
        position: fixed;
        bottom: 0;
        z-index: 1;
    }

    .custom_container{
        max-width:800px;
    }
</style>
@endsection

@section('MainSection')
@php
$Alphabets = range('A','Z');
@endphp

<div class="get_started_main_div GetStartedMainDiv">
        <div class="main_content">
            <div class="container">
                <img src="{{asset('assets/images/logoold.png')}}" class="logo" alt="">
                <h2 class="mb-4 fw-bold text-center">Welcome!</h2>
                <h3 class="mb-4 text-center">Click on the Get Started button to begin your journey to a Sharia compliant
                    will!</h3>
                <div class="text-center">
                    <button class="btn btn-lg custom_btn_primary px-5 py-4 fw-bold BtnGetStarted">Get Started</button>
                </div>
                <div class="or_text_main_div my-4">
                    <div class="or_text_div">
                        <div class="or_text_div_border"></div><span class="mb-0 text-center bg-white px-3">OR</span>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="btn custom_btn_primary fw-bold">Login</a>
                </div>
            </div>
        </div>
    </div>

<div style="position:relative;margin-bottom:150px; margin-top:150px">
    
    <div class="h-100 w-100 d-flex justify-content-between flex-column QueryMainDiv" style="display:none !important">
    <h1 class="mb-0 text-center text-light p-4 fw-bold nav_header position-fixed w-100 top-0" style="z-index:1001">
    Sharia Compliant Form</h1>
    <div>
        <div>
            <div class="container custom_container">
                <div id="AppendQuestionsHere" class="p-4 shadow my-4 rounded">
                    <div class="QNAMainDiv qna_main_div">
                        <div>
                            <p class="question_heading d-flex align-items-center mb-4"><span
                                    class="question_alphabet">A</span> Do you live in England or Wales?</p>
                            <div class="position-relative">
                                <input type="radio" id="Option_A_1" class="input_radio_option InputRadioOption"
                                    value="Yes" name="QuestionAOption">
                                <label for="Option_A_1" class="d-block mb-2 option_label">
                                    <div class="option_label_child_div"><div class="option_alphabet"><div class="custom_radio"></div></div><span
                                            class="option_text">Yes</span> </div>
                                </label>
                            </div>
                            <div class="position-relative">
                                <input type="radio" id="Option_A_2" class="input_radio_option InputRadioOption"
                                    value="No" name="QuestionAOption">
                                <label for="Option_A_2" class="d-block mb-2 option_label">
                                    <div class="option_label_child_div"><div class="option_alphabet"><div class="custom_radio"></div></div><span
                                            class="option_text">No</span></div>
                                </label>
                            </div>
                        </div>



                        <div class="mt-5">
                            <p class="question_heading d-flex align-items-center mb-4">
                                <span class="question_alphabet">B</span>
                                The majority of my assets are in the UK?
                            </p>
                            <div class="position-relative">
                                <input type="radio" id="Option_B_1" class="input_radio_option InputRadioOption"
                                    value="Yes" name="QuestionBOption">
                                <label for="Option_B_1" class="d-block mb-2 option_label">
                                    <div class="option_label_child_div"><div class="option_alphabet"><div class="custom_radio"></div></div><span
                                            class="option_text">Yes</span> </div>
                                </label>
                            </div>
                            <div class="position-relative">
                                <input type="radio" id="Option_B_2" class="input_radio_option InputRadioOption"
                                    value="No" name="QuestionBOption">
                                <label for="Option_B_2" class="d-block mb-2 option_label">
                                    <div class="option_label_child_div"><div class="option_alphabet"><div class="custom_radio"></div></div><span
                                            class="option_text">No</span></div>
                                </label>
                            </div>
                        </div>

                        <!-- <div class="mt-5 text-center">
                            <button class="btn btn-sm custom_btn_primary rounded-0 py-2 px-3 btn_next BtnNext">
                                <span class="position-relative me-4">
                                    <span>Next</span>
                                    <i class="fa-regular fa-arrow-right right_arrow_icon arrow_icon"></i>
                                </span>
                            </button>
                        </div> -->


                    </div>

                </div>

                <div id="AppendNextBackBtnsHere"
                    class="d-flex justify-content-md-end justify-content-center custom_pointer_none">
                    <button
                        class="btn btn-sm custom_btn_primary rounded btn_next me-1 BtnNext custom_pointer_all">
                        <span class="position-relative me-4">
                            <span>Next</span>
                            <i class="fa-regular fa-arrow-right right_arrow_icon arrow_icon"></i>
                        </span>
                    </button>
                </div>

            </div>
        </div>
        <!-- <div id="AppendNextBackBtnsHere"
            class="d-flex justify-content-md-start justify-content-between position-fixed w-100 bottom-0 append_next_back_btns_here">
            <button class="btn btn-lg custom_btn_primary rounded-0 py-3 px-5 btn_back me-md-1 BtnBack">
                <span class="position-relative ms-4">
                    <i class="fa-regular fa-arrow-left left_arrow_icon arrow_icon"></i>
                    <span>Back</span>
                </span>
            </button>
            <button class="btn btn-lg custom_btn_primary rounded-0 py-3 px-5 btn_next BtnNext">
                <span class="position-relative me-4">
                    <span>Next</span>
                    <i class="fa-regular fa-arrow-right right_arrow_icon arrow_icon"></i>
                </span>
            </button>
        </div> -->

    </div>
    <footer>
    <p class="mb-0">&copy; {{date('Y')}} - My Sharia Will - All Rights Reserved | Created with <i class="fas fa-heart text-danger"></i> by <a href="https://www.optimizedtechandbi.co.uk" target="_blank" class="text-white">Optimized Tech & Bi</a>
    </p>
</footer>
</div>



@endsection

@section('Script')
<script src="{{url('backend/app-assets/js/core/sweetalert2.js')}}"></script>
<script>

    $(function () {
        $('.InputRadioOption').prop('checked',false);
        // RenderQuestion('A');

        $(document.body).on('change', 'input[name="QuestionAOption"], input[name="QuestionBOption"]', function () {
            // setTimeout(function () {
            //     $('.BtnNext').trigger('click');
            // }, 300);
        });

        $(document.body).on('click', '.BtnGetStarted', function () {
            $('.GetStartedMainDiv').fadeOut(200, function () {
                $(this).remove();
                $('.QueryMainDiv').fadeIn(250);
            });
        });

        $(document.body).on('click', '.BtnNext', function () {
            var isGoodToGo = true;
            if ($('input[name="QuestionAOption"]:checked').length == 0) {
                isGoodToGo = false;
            }

            if ($('input[name="QuestionBOption"]:checked').length == 0) {
                isGoodToGo = false;
            }

            if (isGoodToGo) {
                var IsGoodToProceed = true;
                var QuestionAOption = $('input[name="QuestionAOption"]:checked').val();
                var QuestionBOption = $('input[name="QuestionBOption"]:checked').val();

                if (QuestionAOption == "Yes" && QuestionBOption == "Yes") {


                    var RegisterRoute = "{{ route('register') }}";

                    $('#AppendQuestionsHere').html('<div class="almost_there_div">'
                        + '<h1 class="mb-4 fw-bold text-center">You\'re almost there!!!</h1>'
                        + '<h3 class="mb-4">This questionnaire will take you approximately 20 minutes to complete and once you\'ve finished, you can leave the rest to us.</h3>'
                        + '<h4 class="mb-4">The questionnaire will take you through 5 main sections:</h4>'
                        + '<ul class="mb-4">'
                        + '@foreach($QSData as $item)'
                        + '<li>{{ $item["title"] }}</li>'
                        + '@endforeach'
                        + '</ul>'
                        + '<div class="mb-5" style="background-color:var(--custom_primary);border:2px solid var(--custom_primary);overflow:hidden;border-radius:10px;color:var(--custom_primary)">'
                        + '<h4 class="mb-0 fw-bold py-4 text-center text-light">My Sharia Will</h4>'
                        + '<div class="py-5"style="background-color:#fff;border-top-left-radius:13%;border-top-right-radius:13%;overflow:hidden;cursor:pointer">'
                        + '<div class="py-5 text-center">'
                        + '<h5 class="mb-0">Complete the questionnaire first, then pay £99 to get your Sharia-compliant will</h5>'
                        + '<a href="' + RegisterRoute + '" class="btn custom_btn_primary mt-4">Start Questionnaire Now</a>'
                        + '</div>'
                        + '</div>'
                        + '</div>'
                        + '</div>');

                        $('#AppendNextBackBtnsHere').addClass('d-none');

                } else {
                    var Seconds = 3;

                    $('#AppendQuestionsHere').html('<div class="d-flex justify-content-center align-items-center" style="height:50vh"> <div> <h1 style="font-size:40px; font-weight:bold">Sorry!</h1> <p class="mb-0" style="font-size:25px">we don’t think our service is going to be suitable for you.</p><div class="mt-3 text-center">Redirecting you to the main page in <span class="RedirectSeconds">'+Seconds+' seconds</span></div></div> </div>');
                    $('#AppendNextBackBtnsHere').addClass('d-none');
                    var RedirectingSecondsInterval = setInterval(function(){
                        Seconds--;
                        if(Seconds>=0){
                            $('.RedirectSeconds').text(Seconds+' seconds');
                        }
                        if(Seconds==0){
                            clearInterval(RedirectingSecondsInterval);
                            location.reload();
                        }
                    },1000);

                    // setTimeout(function(){
                    //     location.reload();
                    // },3000);
                }

            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: "Please Select The Option...",
                    showConfirmButton: false,
                    timer: 1000
                });
            }

            // var ActiveQuestion = $('.QNAMainDiv').attr('Question');
            // if (ActiveQuestion == "A") {
            //     var SelectedOption = $('input[name="Question' + ActiveQuestion + 'Option"]:checked').val();
            //     if (SelectedOption == "Yes") {
            //         RenderQuestion('B');
            //     } else if (SelectedOption == "No") {
            //         RenderQuestion('None');
            //     }
            // } else if (ActiveQuestion == "B") {
            //     var SelectedOption = $('input[name="Question' + ActiveQuestion + 'Option"]:checked').val();
            //     var RegisterRoute = "{{ route('register') }}";
            //     if (SelectedOption == "Yes") {
            //         $('.QueryChildDiv').removeClass('d-flex align-items-center h-100');
            //         $('.QueryChildDiv').css('margin-top', '8rem')
            //         // window.location.href = "{{ route('register') }}";
            //         $('#AppendQuestionsHere').html('<div class="almost_there_div">'
            //             + '<h1 class="mb-4 fw-bold">You\'re almost there</h1>'
            //             + '<h3 class="mb-4">This questionnaire will take you approximately 20 minutes to complete and once you\'ve finished, you can leave the rest to us.</h3>'
            //             + '<h4 class="mb-4">The questionnaire will take you through 5 main sections:</h4>'
            //             + '<ul class="mb-4">'
            //             + '@foreach($QSData as $item)'
            //             + '<li>{{ $item["title"] }}</li>'
            //             + '@endforeach'
            //             + '</ul>'
            //             + '<div class="mb-5" style="background-color:var(--custom_primary);border:2px solid var(--custom_primary);overflow:hidden;border-radius:10px;color:var(--custom_primary)">'
            //             + '<h4 class="mb-0 fw-bold py-4 text-center text-light">My Sharia Will</h4>'
            //             + '<div class="py-5"style="background-color:#fff;border-top-left-radius:13%;border-top-right-radius:13%;overflow:hidden;cursor:pointer">'
            //             + '<div class="py-5 text-center">'
            //             + '<h1 class="mb-0">£89</h1>'
            //             + '<h5 class="mb-0">Details will be here..</h5>'
            //             + '<a href="' + RegisterRoute + '" class="btn custom_btn_primary mt-4">Proceed To Register</a>'
            //             + '</div>'
            //             + '</div>'
            //             + '</div>'
            //             + '</div>');

            //         $('#AppendNextBackBtnsHere').empty();

            //         // $('#AppendNextBackBtnsHere').html('<a href="'+RegisterRoute+'" class="btn btn-lg custom_btn_primary rounded-0 py-3 px-5 btn_proceed"><span class="position-relative me-4"><span>Proceed To Register</span><i class="fa-regular fa-arrow-right right_arrow_icon arrow_icon"></i></span></a>');

            //     } else if (SelectedOption == "No") {
            //         RenderQuestion('None');
            //     }
            // }
        });

        $(document.body).on('click', '.BtnBack', function () {
            var ActiveQuestion = $('.QNAMainDiv').attr('Question');
            if (ActiveQuestion != "A") {
                RenderQuestion('A');
            }
        });

    });

    function RenderQuestion(Question) {
        var FadingSpeed = 350;
        $('#AppendQuestionsHere').hide();
        if (Question == "A" || Question == "B") {
            var QuestionTitle = "";
            if (Question == "A") {
                QuestionTitle = 'Do you live in England or Wales?';
            } else if (Question == "B") {
                QuestionTitle = 'The majority of my assets are in the UK?';
            }
            var HTML = '<div class="QNAMainDiv qna_main_div" Question="' + Question + '">'
                + '<p class="custom_text_bold question_heading d-flex align-items-center mb-4">'
                + '<span class="question_alphabet">' + Question + '</span> ' + QuestionTitle + '</p>'

                + '<div class="position-relative">'
                + '<input type="radio" id="Option_' + Question + '_1" class="input_radio_option InputRadioOption" value="Yes" name="Question' + Question + 'Option">'
                + '<label for="Option_' + Question + '_1" class="d-block mb-2 option_label">'
                + '<div class="option_label_child_div">'
                + '<span class="option_alphabet">A</span>'
                + '<span class="option_text">Yes</span> '
                + '</div>'
                + '</label>'
                + '</div>'
                + '<div class="position-relative">'
                + '<input type="radio" id="Option_' + Question + '_2" class="input_radio_option InputRadioOption" value="No" name="Question' + Question + 'Option">'
                + '<labeln_' + Question + '_2" class="d-block mb-2 option_label">'
                + '<div class="option_label_child_div">'
                + '<span class="option_alphabet">B</span>'
                + '<span class="option_text">No</span>'
                + '</div>'
                + '</label>'
                + '</div>'
                + '</div>';
            $('#AppendQuestionsHere').html(HTML);
            $('#AppendQuestionsHere').fadeIn(FadingSpeed);
        } else if (Question == 'None') {
            $('#AppendQuestionsHere').html('<div class="d-flex justify-content-center"> <div> <h1 style="font-size:40px; font-weight:bold">Sorry!</h1> <p class="mb-0" style="font-size:25px">we don’t think our service is going to be suitable for you.</p> </div> </div>');
            $('#AppendQuestionsHere').fadeIn(FadingSpeed);
        }
    }

</script>
@endsection