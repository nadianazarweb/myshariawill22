@extends('customer.layouts.master')
@section('css')
<style>
    .rotate_animation {
        animation: rotate_animation 2.5s linear infinite;
        transform: translateZ(0);
        -webkit-transform: translateZ(0);
        transform-origin: 50% 51%;
        -webkit-transform-origin: 50% 51%;
    }

    @keyframes rotate_animation {
        from {
            transform: rotateZ(0deg);
        }

        to {
            transform: rotateZ(360deg);
        }
    }
</style>
@stop

@section('title')
<title>My Forms</title>

@stop
@section('body')
<div class="content-wrapper container-xxl p-0">
    <div class="content-header row"></div>
    <div class="content-body">
        @if(count($FinalArray)>0)
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        @foreach($FinalArray as $FAKey=>$FAItem)
                        <tr class="MainTR" FAKey="{{ $FAKey }}">
                            <td class="text-center"><button class="btn btn-info BtnShowData">View My Form</button>
                            <a class="btn btn-info mt-1 mt-md-0 ml-1 {{ $FAItem['ApprovedReport']=="" ? 'BtnNoReport' : '' }}" href="{{ $FAItem['ApprovedReport']!="" ? Storage::disk('reports')->url($FAItem['ApprovedReport']->folder.'/'.$FAItem['ApprovedReport']->file) : 'javascript:void(0)' }}" {{$FAItem['ApprovedReport']!="" ? 'target="_blank"' : ''}}>View My Approved Report</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @php
     
        $qry_submission_date = date('Y-m-d H:i:s',strtotime($QSubmissionDateTime->queries_submission_datetime));
        
        $expiration_date = date('Y-m-d H:i:s',strtotime($qry_submission_date.' +2 day'));
        
        @endphp

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
                    @if($QSubmissionDateTime->customer_approved==0)
                    @if($expiration_date>date('Y-m-d H:i:s'))
                    <div class="badge badge-danger mt-1 mb-2 shadow" style="white-space:unset">YOU WILL NOT BE ABLE TO EDIT THE ANSWERS AFTER 48 HOURS OF SUBMISSION</div>
                        @endif
                        @endif
                        <ul class="list-group ModalListGroup">

                        </ul>
                    </div>
                    <div class="modal-footer">
                        @if(date('Y-m-d H:i:s')>$expiration_date || $QSubmissionDateTime->customer_approved==1)
                        <button class="btn btn-info BtnRequestChange">Request Changes</button>
                        @elseif(date('Y-m-d H:i:s')<$expiration_date || $QSubmissionDateTime->customer_approved==0)
                        <button class="btn btn-info BtnFinalApprove">Final Approve</button>
                        @endif
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        @else
        <div class="card">

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Show Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">No Data Found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @endif

        <form action="{{ route('final_approve') }}" id="FormApprove" method="POST">
            @csrf
        </form>

    </div>
</div>
@endsection

@section('javascript')
<script src="{{url('frontend/js/moment.min.js')}}"></script>

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

        $('.BtnNoReport').on('click',function(){
                Swal.fire({
                    position: 'center',
                    icon: 'info',
                    title: "Your Report Status Is Pending",
                    showConfirmButton: false,
                });
        });

        $('.BtnFinalApprove').on('click',function(){
            Swal.fire({
                title: "Are you sure you want to approve? You will not be able to change the answers later",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, approve it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#FormApprove').submit();
                }
            });
        });

        $('.BtnRequestChange').on('click', function () {
            $('#ShowDataModal').modal('hide');
            Swal.fire({
                title: 'Request Changes',
                input: 'textarea', // You can use 'text', 'email', 'password', etc.
                inputPlaceholder: 'Enter your Request',
                inputAttributes: {
                    required: 'true', // Make the textarea required
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
            }).then(function (e) {
                if (e.isConfirmed) {
                    var Value = e.value;
                    Swal.fire({
                        title: 'Sending your request. Please wait.. <i class="fas fa-spinner rotate_animation"></i>',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                    });

                    var DataToSend = new Object();
                    DataToSend._token = $('meta[name="csrf-token"]').attr('content');
                    DataToSend.request_remarks = Value;
                    $.ajax({
                        url: '/customer/request_changes',
                        method: 'POST',
                        data: DataToSend,
                        success: function (e) {
                            if (e.status) {
                                Swal.fire({
                                    title: e.msg,
                                    icon: 'success'
                                });
                            } else {
                                Swal.fire({
                                    title: e.msg,
                                    icon: 'error'
                                });
                            }
                        }
                    });
                }
            });
        });
        var CurrentDate = moment().format('YYYY-MM-DD HH:mm:ss');
        var QSubmissionDateTime = <?= json_encode($QSubmissionDateTime) ?>;
        var CustomerApproved = QSubmissionDateTime.customer_approved;
        QSubmissionDateTime = moment(QSubmissionDateTime.queries_submission_datetime,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss');
        
        var ExpirationDate = moment(QSubmissionDateTime,'YYYY-MM-DD HH:mm:ss').add(1,'day').format('YYYY-MM-DD HH:mm:ss');
        
        $('.BtnShowData').on('click', function () {
            

           
            $('.ModalListGroup').empty();
            var FAKey = $(this).closest('.MainTR').attr('FAKey');
            $.each(json[FAKey].AttemptedAnswers, function (i, option) {
                var QuestionID = option.QuestionID;
                var QuestionTitle = option.QuestionTitle;
                var ParentQuestionSortID = option.ParentQuestionSortID;

                var EditURL = "{{ route('customer_edit_attempted_answer', ['QuestionID'=>':QuestionID']) }}";
                EditURL = EditURL.replace(':QuestionID', QuestionID);


                var URLAnchor = '<a class="fas fa-edit ml-2 text-info" href="' + EditURL + '"></a>';
                if (CurrentDate > ExpirationDate || CustomerApproved=="1") {
                    URLAnchor = '';
                }

                $('.ModalListGroup').append('<li class="list-group-item LIParentQuestion" ParentQuestionID="' + QuestionID + '"><b>' + SubAlphabets[option.ParentQuestionSortID] + '</b> - ' + QuestionTitle + ' ' + URLAnchor + '</li>');
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
    });
</script>
@stop