@extends('accountant.layouts.master')
@section('css')
@stop

@section('title')
<title>All Forms</title>
@stop
@section('body')
<div class="content-wrapper container-xxl p-0">
    <div class="content-header row"></div>
    <div class="content-body">
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>UserName</th>
                            <th>Email</th>
                            <th>Contact No</th>
                            <th>Show Data</th>
                            <th>Assign</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($FinalArray)>0)
                        @foreach($FinalArray as $FAKey=>$FAItem)
                        <tr class="MainTR" FAKey="{{ $FAKey }}">
                            <td>{{ $FAItem['UserFullName'] }}</td>
                            <td>{{ $FAItem['UserEmail'] }}</td>
                            <td>{{ $FAItem['ContactNo'] }}</td>
                            <td><button class="btn btn-info BtnShowData">Show Data</button></td>
                            <td>
                                @if($FAItem['IsLocked']=="1")

                                @if($FAItem['AssignedToAccountantID']!="")
                                @if(Auth::user()->id==$FAItem['AssignedToAccountantID'])
                                <span class="text-success"><i class="fas fa-check"></i> Assigned To Me</span>

                                @else
                                <span class="text-success"><i class="fas fa-check"></i> Assigned To {{
                                    $FAItem['AccountantName'] }}</span>
                                @endif

                                @else
                                <form action="{{ route('assign_to_accountant') }}" class="FormAssign" method="POST">
                                    @csrf
                                    <input type="hidden" value="{{ $FAItem['UserID'] }}" name="user_id">
                                    <button class="btn btn-success BtnConfirmAssign" type="button">Assign To Me</button>
                                </form>

                                @endif
                                @else
                                <button class="btn btn-info">Pending Queries</button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="4" class="text-center">No Data Found</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if(count($FinalArray)>0)
        <div class="modal fade" id="ShowDataModal" tabindex="-1" aria-labelledby="ShowDataModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ShowDataModalLabel">Attempted Questions</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="max-height:73vh; overflow:auto">
                        <ul class="list-group ModalListGroup">

                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        @endif



    </div>
</div>
@endsection

@section('javascript')
<script src="{{url('backend/app-assets/js/core/sweetalert2.js')}}"></script>

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

    // $('#ShowDataModal').modal('show');
    var json = <?= json_encode($FinalArray) ?>;
    $(function () {
        $('.BtnShowData').on('click', function () {
            $('.ModalListGroup').empty();
            var FAKey = $(this).closest('.MainTR').attr('FAKey');
            $.each(json[FAKey].AttemptedAnswers, function (i, option) {
                var QuestionID = option.QuestionID;
                var QuestionTitle = option.QuestionTitle;
                var ParentQuestionSortID = option.ParentQuestionSortID;
                $('.ModalListGroup').append('<li class="list-group-item LIParentQuestion" ParentQuestionID="' + QuestionID + '"><b>' + SubAlphabets[option.ParentQuestionSortID] + '</b> - ' + QuestionTitle + '</li>');
                if (option.AttemptedOptions.length > 0) {
                    $('.LIParentQuestion[ParentQuestionID="' + QuestionID + '"]').append('<ul class="ULParentOption"></ul>');
                    $.each(option.AttemptedOptions, function (j, AttemptedOption) {
                        if(AttemptedOption.TextValue != null || AttemptedOption.OptionID!=null){

                        if (AttemptedOption.TextValue != null) {
                            $('.LIParentQuestion[ParentQuestionID="' + QuestionID + '"] .ULParentOption').append('<li>' + AttemptedOption.TextValue + '</li>');
                        }

                        if (AttemptedOption.OptionID != null) {
                            $('.LIParentQuestion[ParentQuestionID="' + QuestionID + '"] .ULParentOption').append('<li>' + AttemptedOption.OptionTitle + '<ul class="ULRelatedQuestion" ParentQuestionID="' + QuestionID + '" OptionID="' + AttemptedOption.OptionID + '"></ul></li>');
                            if (AttemptedOption.RelatedQuestions.length > 0) {
                                $.each(AttemptedOption.RelatedQuestions, function (k, RelatedQuestion) {

                                    var RelatedQuestionSortID = RelatedQuestion.RelatedQuestionSortID;

                                    $('.ULRelatedQuestion[ParentQuestionID="' + QuestionID + '"][OptionID="' + AttemptedOption.OptionID + '"]').append('<li><b>' + SubAlphabets[ParentQuestionSortID] + (parseInt(RelatedQuestionSortID) + 1) + '</b> - ' + RelatedQuestion.RelatedQuestionTitle + '<ul class="ULRelatedOption" RelatedQuestionID="' + RelatedQuestion.RelatedQuestionID + '"></ul></li>');

                                    if (RelatedQuestion.RelatedOptions.length > 0) {
                                        $.each(RelatedQuestion.RelatedOptions, function (l, RelatedOption) {
                                            if (RelatedOption.RelatedOptionID != null) {
                                                $('.ULRelatedOption[RelatedQuestionID="' + RelatedQuestion.RelatedQuestionID + '"]').append('<li>' + RelatedOption.RelatedOptionTitle + '</li>');
                                            }

                                            if (RelatedOption.RelatedTextValue != null) {
                                                if(RelatedQuestion.RelatedQuestionOptionTypeID!=6){
                                                    $('.ULRelatedOption[RelatedQuestionID="' + RelatedQuestion.RelatedQuestionID + '"]').append('<li>' + RelatedOption.RelatedTextValue + '</li>');
                                                }else{
                                                    if(RelatedOption.RelatedTextValue!=""){
                                                        var RelatedTextArr = JSON.parse(RelatedOption.RelatedTextValue);
                                                        if(RelatedTextArr.length>0){
                                                            $.each(RelatedTextArr, function(m, RelatedTextOption){
                                                                var HTML = '';
                                                                if(RelatedTextOption.Data.length>0){
                                                                    HTML += '<li>'
                                                                        +'<div class="table-responsive">'
                                                                        +'<table class="table table-bordered table-striped table-hover">'

                                                                        + '<thead>'

                                                                        + '<tr><th colspan="100%" style="text-align:center;font-size:1.2em">'+RelatedTextOption.For+' | Total - <b>'+RelatedTextOption.Total+'</b></th></tr><tr>';

                                                                        $.each(RelatedTextOption.Data[0].TDValues, function(n, TDValueOption){
                                                                            HTML += '<th>'+TDValueOption.name+'</th>';
                                                                        });

                                                                        HTML += '</tr></thead>'

                                                                        + '<tbody>';

                                                                        $.each(RelatedTextOption.Data, function(n, DataOption){
                                                                            HTML += '<tr>';
                                                                            $.each(DataOption.TDValues, function(o, TDValueOption){
                                                                                HTML += '<td>'+TDValueOption.value+'</td>';
                                                                            });
                                                                            HTML += '</tr>';
                                                                        });

                                                                        HTML += '</tbody>'

                                                                        +'</table>'
                                                                        +'</div>'
                                                                        +'</li>';
                                                                }else{
                                                                    HTML += '<li>'+RelatedTextOption.Total+' - '+RelatedTextOption.For+'</li>';
                                                                }
                                                                $('.ULRelatedOption[RelatedQuestionID="' + RelatedQuestion.RelatedQuestionID + '"]').append(HTML);
                                                            });
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    }

                                });
                            }
                        }
                    }else{
                        $('.LIParentQuestion[ParentQuestionID="' + QuestionID + '"] .ULParentOption').append('<li><strong class="text-danger">SKIPPED</strong></li>');
                    }

                    });
                }
            });
            $('#ShowDataModal').modal('show');
        });

        $('.BtnConfirmAssign').on('click', function () {
            var Form = $(this).closest('.FormAssign');
            Swal.fire({
                title: "Are you sure you want to assign it?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes"
            }).then((result) => {
                if (result.isConfirmed) {
                    Form.submit();
                }
            });
        });

    });
</script>
@stop