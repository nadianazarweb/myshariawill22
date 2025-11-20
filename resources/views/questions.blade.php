@extends('/layouts/master')
@section('css')
<style>
    .ul_o,
    .ul_rq,
    .ul_ro {
        display: none;
    }

    .li_o,
    .li_rq,
    .li_ro {
        border: 1px solid rgba(34, 41, 47, 0.125);
        padding: 10px;
        margin-top: 10px;
    }

    .cursor_pointer {
        cursor: pointer;
    }

    .icon_chevron {
        transition: 0.5s;
    }

    .rotate_chevron {
        transform: rotateZ(90deg);
    }

</style>
@stop

@section('title')
<title>Questions</title>
@stop
@section('body')
<div class="content-wrapper container-xxl p-0">
    <div class="content-header row"></div>
    <div class="content-body">
        <div class="row">
            <div class="col-xl-12 col-md-6 col-12 mb-1">
                <div class="form-group">
                    <input type="text" class="form-control" id="search-fields" placeholder="Search Questions">
                </div>
            </div>
            <div class="col-12 mb-1">
                <div>
                    <a href="{{route('create_question')}}" type="button"
                        class="btn btn-outline-primary waves-effect add-user"><i class="fa-light fa-user"></i> &nbsp;Add
                        New Question</a>
                    <button
                        class="btn btn-success waves-effect waves-float waves-light BtnApplySortSetting d-none">Apply
                        This Sort Setting</button>
                </div>
            </div>
        </div>
        <style>
            .is_male{
                color:#0096FF;
            }

            .is_female{
                color:#FC0FC0;
            }

            .is_both{
                color:#9F2B68;
            }

            .gender_icon{
                font-size:18px;
            }
        </style>
        @if(count($QuestionData)>0)
        <ul class="list-group ULQ">
            @foreach($QuestionData as $QDKey=>$QDItem)
            <li class="list-group-item li_q LIQ" ParentQuestionID="{{$QDItem['ParentQuestionID']}}">
                <span class="PQTitle cursor_pointer">
                    <i class="fas fa-chevron-right mr-1 icon_chevron LIQChevron"
                        style="{!!count($QDItem['Options']) == 0 ? 'opacity:0' : ''!!}"></i>
                    <b class="text-success">Q.</b>
                    @php
$GenderFAClass = '';
if($QDItem['ParentQuestionForGenderTitle']=='Male'):
$GenderFAClass = 'fa-solid fa-person mx-1 is_male gender_icon';
                endif;

                if($QDItem['ParentQuestionForGenderTitle']=='Female'):
$GenderFAClass = 'fa-solid fa-person-dress mx-1 is_female gender_icon';
                endif;

                if($QDItem['ParentQuestionForGenderTitle']=='Both'):
$GenderFAClass = 'fa-solid fa-person-half-dress mx-1 is_both gender_icon';
                endif;

                    @endphp
                    <span class="PQTitleText"><i class="{{ $GenderFAClass }}"></i>{{ $QDItem['ParentQuestionTitle'] }} | <b>{{ $QDItem['ParentQuestionnaireSectionTitle'] }}</b></span>
                </span>
                <!-- <i class="fas fa-edit ml-2 BtnEditPQTitle cursor_pointer text-info"></i> -->
                <a href="{{ route('edit_question',['QuestionID'=>$QDItem['ParentQuestionID']]) }}" class="ml-2 text-info"><i class="fas fa-edit"></i></a>
                @if(count($QDItem['Options'])>0)
                <ul class="ul_o ULO">
                    @foreach($QDItem['Options'] as $OKey=>$OItem)
                    <li class="list-unstyled li_o LIO" ParentOptionID="{{$OItem['ParentOptionID']}}">
                        <span class="POTitle cursor_pointer">
                            <i class="fas fa-chevron-right mr-1 icon_chevron LIOChevron"
                                style="{!!count($OItem['RelatedQuestions']) == 0 ? 'opacity:0' : ''!!}"></i>
                            <b class="text-info">O.</b>
                            <span class="POTitleText">{{$OItem['ParentOptionTitle']}}</span>
                        </span>
                        @if(count($OItem['RelatedQuestions'])>0)
                        <ul class="ul_rq ULRQ">
                            @foreach($OItem['RelatedQuestions'] as $RQKey=>$RQItem)

                            @php

                            $GetDataForText = '';
                            if($RQItem['RelatedQuestionOptionTypeID']=="6"):
                                if($RQItem['IncreDecreDataFor']!="" && $RQItem['IncreDecreGetData']!=""):
                                $GetDataForText = ' | Get '.str_replace(';',',',$RQItem['IncreDecreGetData']).' Of Each';
                                endif;
                            endif;

                            @endphp

                            <li class="list-unstyled li_rq LIRQ" RelatedQuestionID="{{$RQItem['RelatedQuestionID']}}">
                                <span class="RQTitle cursor_pointer">
                                    <i class="fas fa-chevron-right mr-1 icon_chevron LIRQChevron"
                                        style="{!!count($RQItem['RelatedOptions']) == 0 ? 'opacity:0' : ''!!}"></i>
                                    <b class="text-success">Q.</b>
                                    <span class="RQTitleText">{{$RQItem['RelatedQuestionTitle'].$GetDataForText}}</span>
                                </span>
                                @if(count($RQItem['RelatedOptions'])>0)
                                <ul class="ul_ro ULRO">
                                    @foreach($RQItem['RelatedOptions'] as $ROKey=>$ROItem)
                                    <li class="list-unstyled li_ro LIRO"
                                        RelatedOptionID="{{ $ROItem['RelatedOptionID'] }}">
                                        <b class="text-info">O.</b>
                                        <span class="ROTitleText">{{$ROItem['RelatedOptionTitle']}}</span>
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </li>
                    @endforeach
                </ul>
                @endif
            </li>
            @endforeach
        </ul>

        <div class="modal fade" id="UpdateQueriesModal" tabindex="-1" aria-labelledby="UpdateQueriesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="UpdateQueriesModalLabel">Update Query</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form method="post" action="{{route('update_question')}}">
            @csrf
            <div class="form-group">
            <input type="text" class="form-control" name="value_to_update" value="" required placeholder="Enter">
            <input type="hidden" value="" name="actual_id">
            <input type="hidden" value="" name="question_level">
            </div>
            <div class="form-group">
            <button class="btn btn-sm btn-outline-success" type="submit">Update</button>
            </div>
            </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
        </div>
        @else
        <h1>No Questions Created</h1>
        @endif

    </div>
</div>


@endsection

@section('javascript')
<script src="{{url('backend/app-assets/js/core/sweetalert2.js')}}"></script>
<script>
    var SessionSuccessMsg = <?= json_encode(session('success_msg')) ?>;
    if (SessionSuccessMsg != "" && SessionSuccessMsg != null) {
        Swal.fire({
        position: 'center',
        icon: 'success',
        title: SessionSuccessMsg,
        });
    }

    var SessionFailureMsg = <?= json_encode(session('failure_msg')) ?>;
    if (SessionFailureMsg != "" && SessionFailureMsg != null) {
        Swal.fire({
        position: 'center',
        icon: 'error',
        title: SessionFailureMsg,
        });
    }

    var LISlideTime = 500;

    function QueriesModalForm(ActualValue, ParentQuestionID, QuestionLevel){
        var Title = '';
        if(QuestionLevel=="parent_question"){
            Title = 'Question';
        }else if(QuestionLevel=="parent_option"){
            Title = 'Option';
        }else if(QuestionLevel=="related_question"){
            Title = 'Related Question';
        }else if(QuestionLevel=="related_option"){
            Title = 'Related Option';
        }
        $('input[name="value_to_update"]').val(ActualValue);
        $('input[name="value_to_update"]').attr('placeholder','Enter '+Title+' Title');

        $('input[name="actual_id"]').val(ParentQuestionID);
        $('input[name="question_level"]').val(QuestionLevel);


        $('#UpdateQueriesModal').modal('show');

    }

    $(function () {

        $('.BtnEditPQTitle').on('click', function () {
            var ActualValue = $(this).closest('.LIQ').find('.PQTitleText').text().trim();
            var ParentQuestionID = $(this).closest('.LIQ').attr('ParentQuestionID');
            QueriesModalForm(ActualValue, ParentQuestionID, 'parent_question');
        });

        $('.BtnEditPOTitle').on('click', function () {
            var ActualValue = $(this).closest('.LIO').find('.POTitleText').text().trim();
            var ParentOptionID = $(this).closest('.LIO').attr('ParentOptionID');
            QueriesModalForm(ActualValue, ParentOptionID, 'parent_option');
        });

        $('.BtnEditRQTitle').on('click', function () {
            var ActualValue = $(this).closest('.LIRQ').find('.RQTitleText').text().trim();
            var RelatedQuestionID = $(this).closest('.LIRQ').attr('RelatedQuestionID');
            QueriesModalForm(ActualValue, RelatedQuestionID, 'related_question');
        });

        $('.BtnEditROTitle').on('click', function () {
            var ActualValue = $(this).closest('.LIRO').find('.ROTitleText').text().trim();
            var RelatedOptionID = $(this).closest('.LIRQ').attr('RelatedOptionID');
            QueriesModalForm(ActualValue, RelatedOptionID, 'related_option');
        });



        if ($('.ULQ').length > 0) {
            $('.ULQ,.ULO,.ULRQ,.ULRO').sortable({
                update: function () {
                    $('.BtnApplySortSetting').removeClass('d-none');
                }
            });
        }

        $(document.body).on('click', '.BtnApplySortSetting', function () {
            var Questions = ApplySortingSettingArray();

            if (Questions.length > 0) {
                var Obj = new Object();
                Obj.Data = JSON.stringify(Questions);
                Obj._token = $('meta[name="csrf-token" ]').attr('content');
                $.ajax({
                    url: '/dashboard/questions/update_question_sorting',
                    method: 'POST',
                    beforeSend: function () {
                        Swal.fire({
                            position: 'center',
                            icon: 'info',
                            title: "Please Wait...",
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });
                    },
                    data: Obj,
                    success: function (e) {
                        if (e.status) {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: e.message,
                                showConfirmButton: true,
                                // allowOutsideClick: false
                            });
                        } else {
                            Swal.fire({
                                position: 'center',
                                icon: 'error',
                                title: e.message,
                                showConfirmButton: true,
                                // allowOutsideClick: false
                            });
                        }
                    }

                })
            }
        });

        $(document.body).on('click', '.PQTitle', function (e) {

            if ($(this).closest('.LIQ').find('.ULO').length > 0) {
                $(this).closest('.LIQ').find('.ULO').stop().slideToggle(LISlideTime);
                $(this).closest('.LIQ').find('.LIQChevron').stop().toggleClass('rotate_chevron');
            }

        });

        $(document.body).on('click', '.POTitle', function (e) {
            if ($(this).closest('.LIO').find('.ULRQ').length > 0) {
                $(this).closest('.LIO').find('.ULRQ').stop().slideToggle(LISlideTime);
                $(this).closest('.LIO').find('.LIOChevron').stop().toggleClass('rotate_chevron');
            }
        });

        $(document.body).on('click', '.RQTitle', function (e) {

            if ($(this).closest('.LIRQ').find('.ULRO').length > 0) {
                $(this).closest('.LIRQ').find('.ULRO').stop().slideToggle(LISlideTime);
                $(this).closest('.LIRQ').find('.LIRQChevron').stop().toggleClass('rotate_chevron');
            }

        });
    });

    function ApplySortingSettingArray(){
            var Questions = [];
            $('.LIQ').each(function (i) {
                var ParentQuestionID = $(this).attr('ParentQuestionID');
                var ParentQuestionIndex = Questions.push({
                    SortID: i,
                    ParentQuestionID: ParentQuestionID,
                    Options: []
                }) - 1;

                if ($(this).find('.LIO').length > 0) {
                    $(this).find('.LIO').each(function (j) {
                        var ParentOptionID = $(this).attr('ParentOptionID');
                        var ParentOptionIndex = Questions[ParentQuestionIndex].Options.push({
                            SortID: j,
                            ParentOptionID: ParentOptionID,
                            RelatedQuestions: []
                        }) - 1;

                        if ($(this).find('.LIRQ').length > 0) {
                            $(this).find('.LIRQ').each(function (k) {
                                var RelatedQuestionID = $(this).attr('RelatedQuestionID');

                                var RelatedQuestionIndex = Questions[ParentQuestionIndex].Options[ParentOptionIndex].RelatedQuestions.push({
                                    SortID: k,
                                    RelatedQuestionID: RelatedQuestionID,
                                    RelatedOptions: []
                                }) - 1;

                                if ($(this).find('.LIRO').length > 0) {

                                    $(this).find('.LIRO').each(function (l) {
                                        var RelatedOptionID = $(this).attr('RelatedOptionID');
                                        Questions[ParentQuestionIndex].Options[ParentOptionIndex].RelatedQuestions[RelatedQuestionIndex].RelatedOptions.push({
                                            SortID: l,
                                            RelatedOptionID: RelatedOptionID
                                        });
                                    });

                                }

                            });

                        }

                    });
                }
            });

            return Questions;
        }

</script>
@stop
