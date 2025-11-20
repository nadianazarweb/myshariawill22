@extends('accountant.layouts.master')
@section('css')
<style>
    :root{
        --pending_color:#ff9f43;
        --rejected_color:#ea5455;
        --approved_color:#28c76f;
    }
    #ModalListGroup ul{
        /* list-style:none !important; */
    }

    .table_reports tr th:nth-child(1),
    .table_reports tr th:nth-child(2),
    .table_reports tr th:nth-child(3),
    .table_reports tr th:nth-child(4),
    .table_reports tr th:nth-child(5){
        background-color:#9086f3 !important;
        color:white;
    }

    .table_reports tr td:nth-child(1),
    .table_reports tr td:nth-child(2),
    .table_reports tr td:nth-child(3),
    .table_reports tr td:nth-child(4),
    .table_reports tr td:nth-child(5){
        background-color:#eeecff !important;
    }

    .table_reports .tr_rejected td:nth-child(6),
    .table_reports .tr_rejected td:nth-child(7),
    .table_reports .tr_rejected td:nth-child(8){
        background-color:var(--rejected_color) !important;
        color:white;
    }

    .table_reports .tr_pending td:nth-child(6),
    .table_reports .tr_pending td:nth-child(7),
    .table_reports .tr_pending td:nth-child(8){
        background-color:var(--pending_color) !important;
        color:white;
    }

    .table_reports .tr_approved td:nth-child(6),
    .table_reports .tr_approved td:nth-child(7),
    .table_reports .tr_approved td:nth-child(8){
        background-color:var(--approved_color) !important;
        color:white;
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
        <div class="card">
            <div class="p-2">
                <a href="{{ asset('FormTemplate.docx') }}" target="_blank" class="btn btn-info btn-sm">Download Form</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>UserName</th>
                            <th>Email</th>
                            <th>Contact No</th>
                            <th>Show Data</th>
                            <th>Assigned Date</th>
                            <th>Report</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($FinalArray)>0)
                        @foreach($FinalArray as $FAKey=>$FAItem)
                        <tr class="MainTR {{ $FAItem['ReportIsNotRead']>0 ? 'table-success HasNewStatus' : '' }}" UserID="{{ $FAItem['UserID'] }}">
                            <td>{{ str_pad($FAItem['UserID'], 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $FAItem['UserFullName'] }}</td>
                            <td>{{ $FAItem['UserEmail'] }}</td>
                            <td>{{ $FAItem['ContactNo'] }}</td>
                            <td>
                                <button class="btn btn-info btn-sm BtnShowData" ModalID="ShowDataModal" type="button">Show Data</button>
                            </td>
                            <td>{{ date('d/m/Y',strtotime($FAItem['AssignedToAccountantDate'])) }}</td>
                            <td>

                            <div class="d-flex" style="column-gap:1em">

                                @if($FAItem['ApprovalStatus']=='NoAction' || $FAItem['ApprovalStatus']=='Rejected')
                                    <button class="btn btn-warning btn-sm BtnModalSendReport" type="button">Send Report</button>
                                @elseif($FAItem['ApprovalStatus']=='Pending')
                                    <button class="btn btn-warning btn-sm" disabled><i class="fas fa-check-circle mr-1"></i>Report Sent For The Approval</button>
                                @elseif($FAItem['ApprovalStatus']=='Approved')
                                    <button class="btn btn-success btn-sm" disabled><i class="fas fa-check-circle mr-1"></i>Report Has Been Approved</button>
                                @endif

                                <button class="btn btn-info btn-sm BtnViewHistory" type="button">View History</button>

                            </div>
                               
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

        <div class="modal fade" id="SendReportModal" tabindex="-1" aria-labelledby="SendReportModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="SendReportModalLabel">Send Report</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="ModalBodySendReport" style="max-height:73vh; overflow:auto">
                        <form action="{{ route('send_report') }}" method="post" enctype="multipart/form-data" id="SendReportForm">
                            @csrf
                            <input type="hidden" name="UserID">
                            <input type="hidden" name="UserFullName">
                            <div class="form-group">
                                <label for="manager_id">Select Manager <span id="LastManagerName" class="text-primary"></span></label>
                                <select name="manager_id" required id="manager_id" class="form-control text-capitalize">
                                    @foreach($ManagerData as $key=>$item)
                                    <option value="{{ $item->id }}">{{ $item->username }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Select File</label>
                                <div class="custom-file">
                                    <input type="file" name="file" required class="custom-file-input" id="customFile">
                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="">Remarks</label>
                                <textarea name="accountant_remarks" required class="form-control"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary BtnConfirmSendReport">Send Report</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="ViewReportModal" tabindex="-1" aria-labelledby="ViewReportModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ViewReportModalLabel">View Report</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="ModalBodyViewReport" style="max-height:73vh; overflow:auto">
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="TempHTMLForReport" style="display:none"></div>
        <input type="hidden" value="{{route('creating_report')}}" id="CreatingReportURL">
        <input type="hidden" value="{{route('update_read_status')}}" id="UpdateReadStatusURL">
        <input type="hidden" value="{{url('/')}}" id="WebURL">
        <input type="hidden" value="{{ Storage::disk('reports')->url('') }}" id="ReportStorageURL">
        @endif



    </div>
</div>
@endsection

@section('javascript')
<script src="{{url('backend/app-assets/js/core/sweetalert2.js')}}"></script>
<script src="{{url('frontend/js/moment.min.js')}}"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
    <script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/polyfills.umd.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.0.6/purify.min.js"></script>

    
<script>
    window.jsPDF = window.jspdf.jsPDF;
        var html2canvas = window.html2canvas;
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

        $('.BtnConfirmSendReport').on('click',function(){
            if($('input[name="file"]')[0].files.length==0){
                Swal.fire({
                title: "Please select a file",
                icon: "warning",
                showConfirmButton: true
                });
            }else{
                Swal.fire({
                title: "Are you sure you want to send this report?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Send it!"
                }).then((result) => {
                if(result.isConfirmed){
                    $('#SendReportForm').submit();
                }else{
                    Swal.close();
                }
                });
            }
        });
        

        $('.BtnViewHistory').on('click',function(){
            var UserID = $(this).closest('.MainTR').attr('UserID');
            var FAKey = json.findIndex(obj => obj['UserID'] == UserID);
            var jsonObj = json[FAKey];

            
            if(jsonObj.ReportsHistory.length>0){
                //ModalBodyViewReport
                var HTML = '<div class="d-flex align-items-center justify-content-md-end justify-content-center mt-1 mb-2" style="column-gap:30px">'

                + '<div class="d-flex align-items-center"><div style="height: 15px;width: 15px;background-color: var(--approved_color); margin-right:8px; border-radius:50px;"></div><p class="mb-0">Approved</p></div>'

                + '<div class="d-flex align-items-center"><div style="height: 15px;width: 15px;background-color: var(--pending_color); margin-right:8px; border-radius:50px;"></div><p class="mb-0">Pending</p></div>'

                + '<div class="d-flex align-items-center"><div style="height: 15px;width: 15px;background-color: var(--rejected_color); margin-right:8px; border-radius:50px;"></div><p class="mb-0">Rejected</p></div>'

                +'</div>'
                +'<div class="table-responsive"><table class="table TableReports table_reports" UserID="'+UserID+'">'
                + '<thead>'
                + '<tr>'
                +'<th>Manager Name</th>'
                +'<th>Manager Email</th>'
                +'<th>File</th>'
                +'<th>My Remarks</th>'
                +'<th>Report Date</th>'
                +'<th>Approval Status</th>'
                +'<th>Rejection Remarks</th>'
                +'<th>Approval Status Date</th>'
                + '</tr>'
                +'</thead><tbody>';

                $.each(jsonObj.ReportsHistory, function(i, option){
                    var ApprovalStatusDateTime = '';
                    if(option.approval_status_datetime){
                        ApprovalStatusDateTime = moment(option.approval_status_datetime,'YYYY-MM-DDTHH:mm:ss').format('DD/MM/YYYY');
                    }
                    var AccountantRemarks = '';
                    if(option.accountant_remarks){
                        AccountantRemarks = option.accountant_remarks;
                    }
                    var RejectionRemarks = option.rejection_remarks==null?'':option.rejection_remarks;

                    var TRClass = '';

                    if(option.approval_status=='Approved'){
                        TRClass = 'tr_approved';
                    }
                    if(option.approval_status=='Pending'){
                        TRClass = 'tr_pending';
                    }
                    if(option.approval_status=='Rejected'){
                        TRClass = 'tr_rejected';
                    }

                    HTML += '<tr class="'+TRClass+'">'
                    + '<td>'+option.ManagerFullName+'</td>'
                    + '<td>'+option.ManagerEmail+'</td>'
                    + '<td><a href="'+$('#ReportStorageURL').val()+'/'+option.folder+'/'+option.file+'" class="btn btn-sm btn-info" target="_blank">View Report</a></td>'
                    + '<td>'+AccountantRemarks+'</td>'
                    + '<td>'+moment(option.created_at,'YYYY-MM-DDTHH:mm:ss').format('DD/MM/YYYY') +'</td>'
                    + '<td>'+option.approval_status+'</td>'
                    + '<td>'+RejectionRemarks+'</td>'
                    + '<td>'+ApprovalStatusDateTime+'</td>'
                    + '</tr>';
                });



                HTML += '</tbody></table></div>';
                $('#ModalBodyViewReport').html(HTML);
                
                $('#ViewReportModal').modal('show');
            }else{
                Swal.fire({
                    position: 'center',
                    icon: 'info',
                    title: "No Reports Found",
                    showConfirmButton: false,
                });
            }
        });

        $('#ViewReportModal').on('shown.bs.modal', function(){
            var UserID = $('.TableReports').attr('UserID');
            if($('.MainTR[UserID="'+UserID+'"]').hasClass('HasNewStatus')){
                var DataToSend = new Object();
                DataToSend._token = $('meta[name="csrf-token"]').attr('content');
                DataToSend.UserID = UserID;

                $.ajax({
                    url:'/accountant/my_forms/update_read_status',
                    method:'POST',
                    data:DataToSend,
                    success:function(e){
                        if(e.status){
                            $('.MainTR[UserID="'+UserID+'"]').removeClass('table-success HasNewStatus');
                        }else{
                            Swal.fire({
                                position: 'center',
                                icon: 'error',
                                title: e.msg,
                                showConfirmButton: true,
                            });
                        }
                    }
                    
                })
            }
        });

        $('.BtnModalSendReport').on('click',function(){
            var UserID = $(this).closest('.MainTR').attr('UserID');
            var FAKey = json.findIndex(obj => obj['UserID'] == UserID);
            $('input[name="UserID"]').val(json[FAKey].UserID);
            $('input[name="UserFullName"]').val(json[FAKey].UserFullName);
            var LastManagerName = '';
            if(json[FAKey].ReportsHistory.length>0){
                LastManagerName = '(Last report was sent to '+json[FAKey].ReportsHistory[0].ManagerFullName+')';
            }
            $('#LastManagerName').text(LastManagerName);
            $('#SendReportModal').modal('show');
        });

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

        $('.BtnConfirmAssign').on('click', function () {
            Swal.fire({
                title: "Are you sure you want to assign it?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#FormAssign').submit();
                }
            });
        });

    });
    function Convert_HTML_To_PDF() {
        Swal.fire({
            position: 'center',
            icon: 'info',
            title: "Sending the report. Please Wait...",
            showConfirmButton: false,
            allowOutsideClick: false
        });
        //height * width
        
        var doc = new jsPDF();
        // Source HTMLElement or a string containing HTML.
        $('#TempHTMLForReport').html($('#ModalBodySendReport').html());
        $('#TempHTMLForReport *').css({'letter-spacing':'0px','list-style':'none','page-break-after':'always','page-break-inside':'avoid'});

        var elementHTML = document.getElementById("TempHTMLForReport").innerHTML;
        doc.setFontSize(9);

        doc.html(elementHTML, {
            callback: function(doc) {
                // Save the PDF
                addPageNumbers(doc);
                // doc.save('document-html.pdf');
               var blob = doc.output('blob');
                var formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('file', blob);
                formData.append('UserFullName', $('#ReportUserFullName').text());
                formData.append('UserID', $('#ReportUserFullName').attr('UserID'));

                

                $.ajax({
                    url:$('#CreatingReportURL').val(),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(e){
                        if(e.status){
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: e.msg,
                                showConfirmButton: false,
                                allowOutsideClick: false
                            });
                            setTimeout(function(){
                                location.reload();
                            },1000);
                            // window.open(e.file_url, '_blank').focus();
                        }else{
                            Swal.fire({
                                position: 'center',
                                icon: 'info',
                                title: e.msg,
                                showConfirmButton: true,
                            });
                        }
                        $('#TempHTMLForReport').empty();
                    },
                    error: function(data){console.log(data)}
                });

                // $.ajax({
                //     url:$('#CreatingReportURL').val(),
                //     method:'POST',
                //     data: DataToSend,
                //     success:function(e){

                //     }
                // });
            },
            margin: [10, 10, 10, 10],
            autoPaging: 'text',
            x: 0,
            y: 0,
            width: 190, //target width in the PDF document
            windowWidth: 675 //window width in CSS pixels
        });
    }

    function addPageNumbers(pdf) {
            var PagesArr = [];
            $.each(pdf.internal.pages, function(i, option){
                if(option){
                    PagesArr.push(option);
                }
            });
            const totalPages = PagesArr.length;
            for (let i = 1; i <= totalPages; i++) {
                pdf.setPage(i);
                pdf.setFontSize(8);
                pdf.text('Page ' + i + ' of ' + totalPages, 10, 5);
                // pdf.text('Page ' + i + ' of ' + totalPages, pdf.internal.pageSize.width - 50, pdf.internal.pageSize.height - 10);
            }
        }
</script>
@stop