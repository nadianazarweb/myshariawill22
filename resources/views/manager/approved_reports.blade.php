@extends('manager.layouts.master')
@section('css')
<style>
    .info_icon{
        font-size:27px;
    }
    .fa-whatsapp{
        color:#25d366;
    }
</style>
@stop

@section('title')
    <title>Reports</title>
@stop
@section('body')
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row"></div>
        <div class="content-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                        <div class="card">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>UserName</th>
                                            <th>Show Data</th>
                                            <th>Report</th>
                                            <th>Created At</th>
                                            <th>Inform Customer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($ReportsHistoryData)>0)
                                        @foreach($ReportsHistoryData as $key=>$item)

                                        <tr  UserID="{{ $item['user_id'] }}" class="MainTR">
                                            <td>{{ $item->username }}</td>
                                            <td><button class="btn btn-sm btn-info BtnShowData" ModalID="ShowDataModal" type="button">Show Data</button></td>
                                            <td><a href="{{ Storage::disk('reports')->url($item->folder.'/'.$item->file) }}" target="_blank" class="btn btn-sm btn-info">View Report</a></td>
                                            <td>{{ date('d/m/Y', strtotime($item->created_at)) }}</td>
                                            <td>
                                                <form action="{{ route('manager_send_email') }}" class="SendEmailForm" method="post">
                                                    @csrf
                                                    <input type="hidden" value="{{ $item->user_id }}" name="user_id">
                                                    <a href="javascript:void(0)" class="BtnSendEmail"><i class="far fa-envelope info_icon"></i></a>
                                                </form>
                                            </td>
                                        </tr>

                                        @endforeach

                                        @else
                                        <tr>
                                            <td colspan="100%" class="text-center">No Data Found</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
@endsection
@section('javascript')
<script src="{{url('backend/app-assets/js/core/sweetalert2.js')}}"></script>
<script>
    var json = <?= json_encode($FinalArray) ?>;
    console.log(json);
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
    $('.BtnConfirmApproveReport').on('click', function(){
        var Form = $(this).closest('.ApproveForm');
        Swal.fire({
                title: "Are you sure you want to Approve this report?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Approve it!"
                }).then((result) => {
                    if(result.isConfirmed){
                        Form.submit();
                    }
                });
    });

    $('.BtnSendEmail').on('click', function(){
        var Form = $(this).closest('.SendEmailForm');
        Swal.fire({
                title: "Are you sure you want to Send Email?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes"
                }).then((result) => {
                    if(result.isConfirmed){
                        Form.submit();
                    }
                });
    });

    $('.BtnConfirmRejectReport').on('click', function(){
        var Form = $(this).closest('.RejectForm');
        Swal.fire({
    title: 'Enter Remarks',
    input: 'textarea',
    showCancelButton: true,
    inputValidator: (value) => {
      if (!value) {
        return 'You need to enter something!';
      }else{
        console.log(value);
        Form.find('input[name="rejection_remarks"]').val(value);
        Form.submit();
      }
    }
  });

    });

    $(function(){
        $('.BtnShowData').on('click', function () {
            var ModalID = $(this).attr('ModalID');
            $('.ModalListGroup').empty();
            var UserID = $(this).closest('.MainTR').attr('UserID');
            var FAKey = json.findIndex(obj => obj['UserID'] == UserID);


            if(ModalID=="SendReportModalOld"){
                var UserOBJ = json[FAKey];
                $('#UserInformation').html('<table style="border-spacing: 15px 7px;border-collapse: separate;">'
                +'<tbody>'
                +'<tr>'
                    +'<th>Name:</th>'
                    +'<td id="ReportUserFullName" UserID="'+UserID+'">'+UserOBJ.UserFullName+'</td>'
                +'</tr>'

                +'<tr>'
                    +'<th>Email:</th>'
                    +'<td>'+UserOBJ.UserEmail+'</td>'
                +'</tr>'

                +'<tr>'
                    +'<th>Contact:</th>'
                    +'<td>'+UserOBJ.ContactNo+'</td>'
                +'</tr>'

                +'<tr>'
                    +'<th>Submission Date</th>'
                    +'<td>'+moment(UserOBJ.QueriesSubmissionDateTime,'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss')+'</td>'
                +'</tr>'

                +'</tbody>'
                +'</table>');
            }

            $.each(json[FAKey].AttemptedAnswers, function (i, option) {
                var QuestionID = option.QuestionID;
                var QuestionTitle = option.QuestionTitle;
                var ParentQuestionSortID = option.ParentQuestionSortID;
                var EditURL = '';

                if(ModalID == "ShowDataModal"){
                    if(json[FAKey].ApprovalStatus=='NoAction' || json[FAKey].ApprovalStatus=='Rejected'){
                        EditURL = "{{ route('myforms_edit_attempted_answer', ['UserID'=>':UserID','QuestionID'=>':QuestionID']) }}";
                        EditURL = EditURL.replace(':UserID',UserID);
                        EditURL = EditURL.replace(':QuestionID',QuestionID);

                        EditURL = '<a class="fas fa-edit ml-2 text-info" href="'+EditURL+'"></a>';
                    }
                }
                $('#'+ModalID+' .ModalListGroup').append('<li class="list-group-item LIParentQuestion" ParentQuestionID="' + QuestionID + '"><b>' + SubAlphabets[option.ParentQuestionSortID] + '</b> - ' + QuestionTitle + ' '+EditURL+'</li>');
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

                                    $('.ULRelatedQuestion[ParentQuestionID="' + QuestionID + '"][OptionID="' + AttemptedOption.OptionID + '"]').append('<li><p>' + RelatedQuestion.RelatedQuestionTitle + '</p><ul class="ULRelatedOption" RelatedQuestionID="' + RelatedQuestion.RelatedQuestionID + '"></ul></li>');

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
            $('#'+ModalID).modal('show');
        });
    });
</script>
@stop
