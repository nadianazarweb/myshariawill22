@extends('customer.layouts.master')
@section('css')
<link rel="stylesheet" href="{{url('frontend/css/daterangepicker.css')}}">
<style>
    .cursor_pointer {
        cursor: pointer;
    }

    .custom_pointer_none {
        pointer-events: none !important;
    }

    .related_dropdown .dropdown-item.active, .dropdown-item:active{
        color: unset;
        background-color: unset;   
    }
</style>
@stop

@section('title')
<title>Edit Attempted Answer</title>
@stop
@section('body')
<div class="content-wrapper container-xxl p-0">
    <div class="content-header row"></div>
    <div class="content-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('customer_update_attempted_answer') }}" method="post">
                            @csrf
                            <div id="AppendEditableHere">

                            </div>
                            <input type="hidden" name="parent_question_id" value="{{ $FinalArray->ParentQuestionID }}">
                            <input type="hidden" name="user_id" value="{{ $FinalArray->UserID }}">
                            <button type="submit" class="btn btn-success mt-2">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('javascript')
<script src="{{url('backend/app-assets/js/core/sweetalert2.js')}}"></script>
<script src="{{url('frontend/js/moment.min.js')}}"></script>
<script src="{{url('frontend/js/daterangepicker.min.js')}}"></script>

<script>
    var Alphabets = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var abc = Alphabets.split('');

    var maxLength = 3; // Set your desired maximum length
    var SubAlphabets = [];
    for (var i = 0; i < Alphabets.length; i++) {
        for (var j = 0; j < Alphabets.length; j++) {
            // Concatenate the current characters
            var combination = Alphabets[i] + Alphabets[j];

            // Check if the combination length is less than or equal to the maximum length
            if (combination.length <= maxLength) {
                SubAlphabets.push(combination);
            }
        }
    }
    SubAlphabets = abc.concat(SubAlphabets);
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


    

    $(function () {
        $(document).on('click', '.RelatedDropdown>.dropdown-menu', function (e) {
            e.stopPropagation();
            });
        $(document.body).on('change', '.ParentOption', function () {
            var SelectedParentOptionID = $(this).val();
            $('.RelatedQuestionDiv').empty();
            RelatedQuestionsHTML(SelectedParentOptionID, 'Append');
            if(AlreadyEnteredValue.length>0){
                AutoLoadIncreDecreTable(AlreadyEnteredValue);
            }
        });

        var MouseDownInterval = 0;
        var MouseDownTimeout = 0;
        $(document.body).on('mousedown', '.BtnIncreDecre', function () {
            var BtnThis = $(this);
            MouseDownTimeout = setTimeout(function () {
                MouseDownInterval = setInterval(function () {
                    IncreDecre(BtnThis);
                }, 100);
            }, 350);
        }).on('mouseup mouseleave', function () {
            clearInterval(MouseDownInterval);
            clearTimeout(MouseDownTimeout);
        });
        $(document.body).on('click', '.BtnIncreDecre', function () {
            IncreDecre($(this));
        });

    })

    var json = <?= json_encode($FinalArray) ?>;


    var IncreDecreMin = 0;
    var IncreDecreMax = 10;
    function IncreDecre(BtnThis) {
        var InputClass = BtnThis.attr('InputClass');
        var IncreDecre = BtnThis.attr('IncreDecre');
        var NewVal = 0;
        if (IncreDecre == "Increment" || IncreDecre == "Decrement") {
            if (IncreDecre == 'Increment') {

                NewVal = parseFloat(BtnThis.closest('td').find('.IncreDecreInput').val()) + 1;
            } else if (IncreDecre == 'Decrement') {
                NewVal = parseFloat(BtnThis.closest('td').find('.IncreDecreInput').val()) - 1;
            }
            if (NewVal >= IncreDecreMin && NewVal <= IncreDecreMax) {
                BtnThis.closest('td').find('.IncreDecreInput').val(NewVal);
                IncreDecreTableAppend(BtnThis, IncreDecre);
            }

        }
    }

    function AutoLoadIncreDecreTable(PreDefinedValuesArr) {
        $('.TableChildren').each(function(TableIndex){
            var IncreDecreGetData = $(this).attr('IncreDecreGetData');
            if (IncreDecreGetData != "") {
                $(this).find('.IncreDecreInput').each(function () {
                    var IncreDecreDataFor = $(this).closest('tr').attr('IncreDecreDataForItem');
                    var ParentIndex = -1;
                    var Index = -1;
                    $.each(PreDefinedValuesArr, function(i, option){
                        if(option.length>0){
                            $.each(option, function(j, option2){
                                if(option2.For==IncreDecreDataFor){
                                    ParentIndex = i;
                                    Index = j;
                                    return false;
                                }
                            });
                        }
                    });
                    
                    if(Index>=0 && ParentIndex >= 0){
                        if(PreDefinedValuesArr[ParentIndex][Index].Data.length>0){
                            var NumberOfRows = $(this).val();
                            if (NumberOfRows > 0) {
                                for (var i = 0; i < NumberOfRows; i++) {
                                    if(PreDefinedValuesArr[ParentIndex][Index].Data[i]){
                                        InsertingIncreDecreTable(TableIndex, IncreDecreDataFor, IncreDecreGetData, PreDefinedValuesArr[ParentIndex][Index].Data[i].TDValues);
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    }

    function InsertingIncreDecreTable(TableIndex, IncreDecreDataFor, IncreDecreGetData, PreDefinedValuesObj = null){
        if ($('.DivIncreDecreDataForChild[IncreDecreDataFor="' + IncreDecreDataFor + '"] tbody.TBodyIncreDecreDataFor tr').length < 10) {
        var Index = $('.DivIncreDecreDataForChild[IncreDecreDataFor="' + IncreDecreDataFor + '"] tbody.TBodyIncreDecreDataFor tr').length;

        var TBodyTR = '<tr><td>' + parseInt(Index + 1) + '</td>';
        $.each(IncreDecreGetData.split(';'), function (i, option) {
            var PreAttemptedValue = "";

            if(PreDefinedValuesObj!=null){
                var PreDefinedIndex = PreDefinedValuesObj.findIndex(obj=>obj['name'].trim()==option.trim());
                PreAttemptedValue = PreDefinedValuesObj[PreDefinedIndex].value;
            }

            TBodyTR += '<td IncreDecreGetDataItem="' + option.trim() + '"><input type="text" class="form-control InputGetIncreDecreData" value="'+PreAttemptedValue.trim()+'" name="For['+TableIndex+']['+IncreDecreDataFor.trim()+'][Data]['+Index+']['+option.trim()+']" placeholder="Enter ' + option + '"></td>';
        });
        TBodyTR += '</tr>';
        if ($('.DivIncreDecreDataFor').length == 0) {
            $('.RelatedQuestionDiv').append('<div class="div_incre_decre_data_for DivIncreDecreDataFor" IncreDecreGetData="' + IncreDecreGetData + '"></div><input type="hidden" value="'+IncreDecreGetData+'" name="IncreDecreGetData">');
        }
        if ($('.DivIncreDecreDataForChild[IncreDecreDataFor="' + IncreDecreDataFor + '"]').length == 0) {
            var THeadTR = '<tr><th></th>';
            $.each(IncreDecreGetData.split(';'), function (i, option) {
                THeadTR += '<th>' + option + '</th>';
            });
            THeadTR += '</tr>';

            $('.DivIncreDecreDataFor').append('<div class="div_incre_decre_data_for_child DivIncreDecreDataForChild" IncreDecreDataFor="' + IncreDecreDataFor + '">'
                + '<div class="table-responsive">'
                + '<table class="table">'
                + '<thead>'
                + '<tr>'
                + '<th colspan="100%" class="text-center" style="font-size:1.4em">' + IncreDecreDataFor + '</th>'
                + '</tr>'
                + THeadTR
                + '</thead>'
                + '<tbody style="vertical-align:middle" class="TBodyIncreDecreDataFor">'
                + TBodyTR
                + '</tbody>'
                + '</table>'
                + '</div>'
                + '</div>');
        } else {
            $('.DivIncreDecreDataForChild[IncreDecreDataFor="' + IncreDecreDataFor + '"] .TBodyIncreDecreDataFor').append(TBodyTR);
        }
        }
    }

    var AlreadyEnteredValue = [];

    function IncreDecreTableAppend(BtnThis, IncreDecre) {
        var IncreDecreGetData = BtnThis.closest('.TableChildren').attr('IncreDecreGetData');
        var TableIndex = BtnThis.closest('.TableChildren').index();
        var TableIndex = BtnThis.closest('.TableChildren').closest('.RelatedDropdown').index();
        if(IncreDecreGetData!=""){
            var IncreDecreDataFor = BtnThis.closest('tr').attr('IncreDecreDataForItem');
            if (IncreDecre == "Increment") {

                InsertingIncreDecreTable(TableIndex, IncreDecreDataFor, IncreDecreGetData);

                
            } else if (IncreDecre == "Decrement") {
                if ($('.DivIncreDecreDataForChild[IncreDecreDataFor="' + IncreDecreDataFor + '"] tbody.TBodyIncreDecreDataFor tr').length > 1) {
                    $('.DivIncreDecreDataForChild[IncreDecreDataFor="' + IncreDecreDataFor + '"] tbody.TBodyIncreDecreDataFor tr:last-child').remove();
                } else {
                    $('.DivIncreDecreDataForChild[IncreDecreDataFor="' + IncreDecreDataFor + '"]').remove();
                }
            }

            $('.DivIncreDecreDataFor').sortable();
        }
    }

    var HTML = '';
    HTML += '<h1>' + json.ParentQuestionTitle + '</h1>';
    var ParentAttemptedOptionObj = json.AttemptedOptions.find(item => item['QuestionType'] === 'Parent');

    if (json.ParentQuestionOptionTypeID == 1) {
        if (json.Options.length > 0) {
            var SelectedParentOptionID = "";
            var IsParentOptionSelected = false;

            HTML += '<div class="demo-inline-spacing mt-2">';
            $.each(json.Options, function (i, option) {
                var Checked = '';
                if (ParentAttemptedOptionObj.option_id == option.ParentOptionID) {
                    Checked = 'checked';
                    IsParentOptionSelected = true;
                    SelectedParentOptionID = option.ParentOptionID;
                }
                HTML += '<div class="custom-control custom-control-success custom-radio mt-0">'
                    + '<input type="radio" id="parent_option_' + option.ParentOptionID + '" ' + Checked + ' name="parent_option_id" class="custom-control-input ParentOption" value="' + option.ParentOptionID + '">'
                    + '<label class="custom-control-label" for="parent_option_' + option.ParentOptionID + '">' + option.ParentOptionTitle + '</label>'
                    + '</div>';
            });
            HTML += '</div>';

            HTML += '<div class="RelatedQuestionDiv">';
            if (IsParentOptionSelected) {
                HTML += RelatedQuestionsHTML(SelectedParentOptionID, 'Return');
            }

            HTML += '</div>';
        }
    } else if (json.ParentQuestionOptionTypeID == 2 || json.ParentQuestionOptionTypeID == 4 || json.ParentQuestionOptionTypeID == 5) {
        if (json.ParentQuestionOptionTypeID == 2) {
            var ParentTextValue = "";
            if (ParentAttemptedOptionObj) {
                if(ParentAttemptedOptionObj.text_value){
                    ParentTextValue = ParentAttemptedOptionObj.text_value;
                }
            }
            HTML += '<textarea name="text_value" class="form-control">' + ParentTextValue + '</textarea>';
        }
        if (json.ParentQuestionOptionTypeID == 4) {
            var ParentTextValue = "";
            if (ParentAttemptedOptionObj) {
                if(ParentAttemptedOptionObj.text_value){
                    ParentTextValue = ParentAttemptedOptionObj.text_value;
                }
            }
            HTML += '<input type="text" name="text_value" class="form-control" value="' + ParentTextValue + '">';
        }

        if (json.ParentQuestionOptionTypeID == 5) {
            var ParentTextValue = "";
            if (ParentAttemptedOptionObj) {
                if(ParentAttemptedOptionObj.text_value){
                    ParentTextValue = ParentAttemptedOptionObj.text_value;
                }
            }
            HTML += '<input type="text" name="text_value" class="IsDateInput form-control" value="' + ParentTextValue + '">';
        }
    }

    $('#AppendEditableHere').html(HTML);
    if(AlreadyEnteredValue.length>0){
        AutoLoadIncreDecreTable(AlreadyEnteredValue);
    }
    DateRangeMaker();

    function RelatedQuestionsHTML(ParentOptionID, AppendOrReturn) {
        var ParentOptionOBJ = json.Options.find(item => item['ParentOptionID'] == ParentOptionID);
        var HTML = '';

        if (ParentOptionOBJ.RelatedQuestions.length > 0) {
            $.each(ParentOptionOBJ.RelatedQuestions, function (j, RQOption) {
                if (RQOption.RelatedQuestionOptionTypeID == 2 || RQOption.RelatedQuestionOptionTypeID == 4 || RQOption.RelatedQuestionOptionTypeID == 5) {
                    HTML += '<div class="mt-2 ml-md-2"><h3>' + RQOption.RelatedQuestionTitle + '</h3>';
                    HTML += '<input type="hidden" value="' + RQOption.RelatedQuestionID + '" name="related_question_id[]">'
                    var RQObject = json.AttemptedOptions.find(item => item['related_question_id'] === RQOption.RelatedQuestionID);
                    var RelatedTextValue = "";
                    if (RQObject) {
                        RelatedTextValue = RQObject.related_text_value;
                    }
                    if (RQOption.RelatedQuestionOptionTypeID == 2) {
                        HTML += '<textarea name="related_text_value[rq_id_' + RQOption.RelatedQuestionID + ']" class="form-control">' + RelatedTextValue + '</textarea>';
                    }

                    if (RQOption.RelatedQuestionOptionTypeID == 4) {
                        HTML += '<input type="text" value="' + RelatedTextValue + '" class="form-control" name="related_text_value[rq_id_' + RQOption.RelatedQuestionID + ']">';
                    }

                    if (RQOption.RelatedQuestionOptionTypeID == 5) {
                        HTML += '<input type="text" class="IsDateInput form-control" value="' + RelatedTextValue + '" name="related_text_value[rq_id_' + RQOption.RelatedQuestionID + ']">';
                    }
                    HTML += '</div>';
                } else if (RQOption.RelatedQuestionOptionTypeID == 6) {
                    var RQObject = json.AttemptedOptions.find(item => item['related_question_id'] === RQOption.RelatedQuestionID);
                    if (RQObject) {
                        // AlreadyEnteredValue = JSON.parse(RQObject.related_text_value);
                        AlreadyEnteredValue.push(JSON.parse(RQObject.related_text_value));
                    }

                    var IncreDecreGetData = "";

                    if(RQOption.IncreDecreGetData){
                        IncreDecreGetData = RQOption.IncreDecreGetData;
                    }


                    HTML += '<div class="related_dropdown RelatedDropdown dropdown my-2">'
                        + '<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">' + RQOption.RelatedQuestionTitle + '</button>'
                        + '<ul class="dropdown-menu">'
                        + '<li class="dropdown-item">'
                        + '<table class="TableChildren" IncreDecreGetData="' + IncreDecreGetData + '">';
                    $.each(RQOption.IncreDecreDataFor.split(';'), function (i, option) {

                        var AlreadyAttemptedIndex = -1;
                        var AlreadyAttemptedValue = "0";

                        if (AlreadyEnteredValue.length > 0) {
                            $.each(AlreadyEnteredValue, function(j, option2){
                                AlreadyAttemptedIndex = option2.findIndex(obj => obj['For'] == option.trim());
                                if(AlreadyAttemptedIndex>=0){
                                    AlreadyAttemptedValue = AlreadyEnteredValue[j][AlreadyAttemptedIndex].Total;
                                    return false;
                                }
                                // if(option2.For.trim()==option.trim()){
                                //     AlreadyAttemptedIndex = j;
                                //     return true;
                                // }
                            });
                            // AlreadyAttemptedIndex = AlreadyEnteredValue.findIndex(obj => obj['For'] == option.trim());
                            // if (AlreadyAttemptedIndex >= 0) {
                            //     AlreadyAttemptedValue = AlreadyEnteredValue[AlreadyAttemptedIndex].Total;
                            // }
                        }

                        HTML += '<tr IncreDecreDataForItem="' + option.trim() + '">'
                            + '<td style="width:45%">'
                            + '<p class="mb-0 text-center">'
                            + '<i class="fas fa-user mr-2"></i>' + option.trim() + '</p>'
                            + '</td>'
                            + '<td style="width:55%">'
                            + '<div class="input-group">'
                            + '<div class="input-group-prepend">'
                            + '<button class="btn btn-primary rounded-0 BtnIncreDecre" IncreDecre="Decrement" type="button">-</button>'
                            + '</div>'
                            + '<input type="text" InputNumberOf="' + option.trim() + '" class="form-control rounded-0 IncreDecreInput custom_pointer_none text-center" name="For['+j+']['+option.trim()+'][Total]" value="' + AlreadyAttemptedValue + '">'
                            + '<div class="input-group-append">'
                            + '<button class="btn btn-primary rounded-0 BtnIncreDecre" IncreDecre="Increment" type="button">+</button>'
                            + '</div>'
                            + '</div>'
                            + '</td>'
                            + '</tr>';
                    });

                    HTML += '</table>'
                        + '</li>'
                        + '</ul>';
                    HTML += '<input type="hidden" value="' + RQOption.RelatedQuestionID + '" name="related_question_id[]">'
                        + '</div>';
                }
            });
        }
        if (AppendOrReturn == 'Return') {
            return HTML;

        } else if (AppendOrReturn == 'Append') {
            $('.RelatedQuestionDiv').html(HTML);
            DateRangeMaker();
        }
    }

    // function handleDropdownClick(event) {
    //     // Prevent the default behavior and stop the event propagation
    //     // event.preventDefault();
    //     event.stopPropagation();
    // }

    function DateRangeMaker() {

        if ($('.IsDateInput').length > 0) {
            $('.IsDateInput').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'DD/MM/YYYY'  // Set the desired date format
                },
                // maxDate: moment()
                maxDate: moment().subtract(19, 'years')
            });
        }
    }

</script>
@stop