@extends('layouts.master')
@section('css')
<style>
    :root {
        --pending_color: #ff9f43;
        --rejected_color: #ea5455;
        --approved_color: #28c76f;
        --is_delayed_color: #ffc107;
        --due_date_passed_color: #ea5455;
    }

    #ModalListGroup ul {
        /* list-style:none !important; */
    }

    .table_reports tr th:nth-child(1),
    .table_reports tr th:nth-child(2),
    .table_reports tr th:nth-child(3),
    .table_reports tr th:nth-child(4),
    .table_reports tr th:nth-child(5) {
        background-color: #9086f3 !important;
        color: white;
    }

    .table_reports tr td:nth-child(1),
    .table_reports tr td:nth-child(2),
    .table_reports tr td:nth-child(3),
    .table_reports tr td:nth-child(4),
    .table_reports tr td:nth-child(5) {
        background-color: #eeecff !important;
    }

    .table_reports .tr_rejected td:nth-child(6),
    .table_reports .tr_rejected td:nth-child(7),
    .table_reports .tr_rejected td:nth-child(8) {
        background-color: var(--rejected_color) !important;
        color: white;
    }

    .table_reports .tr_pending td:nth-child(6),
    .table_reports .tr_pending td:nth-child(7),
    .table_reports .tr_pending td:nth-child(8) {
        background-color: var(--pending_color) !important;
        color: white;
    }

    .table_reports .tr_approved td:nth-child(6),
    .table_reports .tr_approved td:nth-child(7),
    .table_reports .tr_approved td:nth-child(8) {
        background-color: var(--approved_color) !important;
        color: white;
    }

    tr.is_delayed td:first-child {
        background-color: var(--is_delayed_color) !important;
        color: white;
    }

    tr.due_date_passed td:first-child {
        background-color: var(--due_date_passed_color) !important;
        color: white;
    }

    tr.approved_reports_class td:first-child {
        background-color: var(--approved_color) !important;
        color: white;
    }

    .cursor_pointer {
        cursor: pointer;
    }
    .forms_table th, .forms_table td{
        vertical-align:middle !important;
    }
</style>
@stop

@section('title')
<title>All Forms</title>
@stop
@section('body')
<div class="content-wrapper container-xxl p-0">
    <div class="content-header row"></div>
    <div class="content-body">
        <div class="card">
            <div class="d-flex flex-md-row flex-column align-items-center justify-content-md-start justify-content-center p-2"
                style="column-gap:30px">
                <div>
                    <h1 style="margin:0">Filters</h1>
                </div>

                <div class="d-flex flex-md-row flex-column mt-1 mt-md-0 align-items-center" style="column-gap: 30px;row-gap: 20px;">
                    <div class="d-flex align-items-center cursor_pointer BtnLegendFilter">
                        <div style="height: 25px;width: 25px;background-color: var(--is_delayed_color); margin-right:8px; border-radius:50px;display:flex;align-items:center;justify-content:center; font-weight:bold; color:white"
                            class="LegendsCount" ForClass="IsDelayed">
                            0
                        </div>
                        <p class="mb-0">Assigned More Than 4 Days Ago</p>
                    </div>

                    <div class="d-flex align-items-center cursor_pointer BtnLegendFilter">
                        <div style="height: 25px;width: 25px;background-color: var(--due_date_passed_color); margin-right:8px; border-radius:50px;display:flex;align-items:center;justify-content:center; font-weight:bold; color:white"
                            class="LegendsCount" ForClass="DueDatePassed">
                            0
                        </div>
                        <p class="mb-0">Due Date Passed</p>
                    </div>

                    <div class="d-flex align-items-center cursor_pointer BtnLegendFilter">
                        <div style="height: 25px;width: 25px;background-color: var(--approved_color); margin-right:8px; border-radius:50px;display:flex;align-items:center;justify-content:center; font-weight:bold; color:white"
                            class="LegendsCount" ForClass="ApprovedReportsClass">
                            0
                        </div>
                        <p class="mb-0">Approved Reports</p>
                    </div>
                    <a href="javascript:void(0)" class="BtnShowAll d-none">Show All</a>
                </div>

                
            </div>
            <div class="table-responsive">
                <table class="table forms_table">
                    <thead>
                        <tr>
                            <th>Report</th>
                            <th>UserName</th>
                            <th>Email</th>
                            <th>Contact No</th>
                            <th>Show Data</th>
                            <th>Assign</th>
                            <th></th>
                            <th>Assigning Date</th>
                            <th>Remaining Days</th>
                            <th>Current Report Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($FinalArray)>0)
                        @foreach($FinalArray as $FAKey=>$FAItem)
                        @php
                        $IsDelayedClass = '';
                        $RemainingDays = '';
                        $DueDatePassedClass = '';

                        if($FAItem['ApprovalStatus']!="Approved"):
                        if($FAItem['AssignedToAccountantDate']!=null):
                        $date1=date_create(date('Y-m-d', strtotime($FAItem['AssignedToAccountantDate'])));
                        $date2=date_create(date('Y-m-d'));
                        $diff=date_diff($date1,$date2);
                        $days_after_assigning = $diff->format("%r%a");

                        if($days_after_assigning>=5 && $days_after_assigning<=7): $IsDelayedClass='is_delayed IsDelayed'
                            ; endif; $AssignedDate=date('Y-m-d',strtotime($FAItem['AssignedToAccountantDate']));
                            $DeadlineDate=date('Y-m-d',strtotime($AssignedDate.'+ 7 days')); $CurrentDate=date('Y-m-d');
                            $date1=date_create($DeadlineDate); $date2=date_create($CurrentDate);
                            $diff=date_diff($date2,$date1); $RemainingDays=$diff->format("%r%a");

                            if($RemainingDays<=0): $DueDatePassedClass='due_date_passed DueDatePassed' ; endif; endif;
                                endif; @endphp <tr
                                class="MainTR {{ $IsDelayedClass }} {{ $DueDatePassedClass }} {{ $FAItem['ApprovalStatus']=='Approved' ? 'ApprovedReportsClass approved_reports_class' : '' }}"
                                FAKey="{{ $FAKey }}" UserID="{{ $FAItem['UserID'] }}">
                                <td>
                                    <button class="btn btn-info btn-sm BtnViewReport" type="button">View
                                        History</button>
                                </td>
                                <td>{{ $FAItem['UserFullName'] }}</td>
                                <td>{{ $FAItem['UserEmail'] }}</td>
                                <td>{{ $FAItem['ContactNo'] }}</td>
                                <td><button class="btn btn-info BtnShowData">Show Data</button></td>
                                <td>
                                    @if($FAItem['IsLocked']=="1")

                                    @if($FAItem['AssignedToAccountantID']!="")
                                    <span class="text-success"><i class="fas fa-check"></i> Assigned To {{
                                        $FAItem['AccountantName'] }}</span>
                                    @else
                                    <button class="btn btn-success BtnAssignToAccountant" type="button">Assign</button>
                                    @endif
                                    @else
                                    <button class="btn btn-info">Pending Queries</button>
                                    @endif
                                </td>
                                <td>
                                    @if($FAItem['AssignedToAccountantID']!="")
                                    <a class="btn btn-info btn-sm"
                                        href="{{route('users',$user->ref_key)}}?uid={{ $FAItem['AssignedToAccountantID'] }}"
                                        target="_blank">Show Accountant Details</a>
                                    @endif
                                </td>
                                <td>{{ $FAItem['AssignedToAccountantDate']!="" ? date('d/m/Y',
                                    strtotime($FAItem['AssignedToAccountantDate'])) : '' }}</td>
                                <td>{{ $RemainingDays }}</td>

                                <td>
                                    @if($FAItem['ApprovalStatus']=='NoAction')
                                    Not Sent By The Accountant Yet
                                    @elseif($FAItem['ApprovalStatus']=='Rejected')
                                    Rejected By Sheikh
                                    @elseif($FAItem['ApprovalStatus']=='Pending')
                                    Report Sent For Approval To Sheikh
                                    @elseif($FAItem['ApprovalStatus']=='Approved')
                                    Approved
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
    </div>
</div>
<input type="hidden" value="{{url('/')}}" id="WebURL">
@endsection

@section('javascript')
<script src="{{url('backend/app-assets/js/core/sweetalert2.js')}}"></script>
<script src="{{url('frontend/js/moment.min.js')}}"></script>

<script>
    $(function () {
        LegendsCount();
        $('.BtnLegendFilter').on('click', function () {
            var ForClass = $(this).find('.LegendsCount').attr('ForClass');
            $('.MainTR').hide();
            $('.' + ForClass).show();
            $('.BtnShowAll').removeClass('d-none');
        });

        $('.BtnShowAll').on('click', function () {
            $('.MainTR').show();
            $(this).addClass('d-none');
        });
    })
    function LegendsCount() {
        $('.LegendsCount').each(function () {
            var ForClass = $(this).attr('ForClass');
            var ForClassLength = $('.' + ForClass).length;
            $(this).text(ForClassLength);
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
    var AccJson = <?= json_encode($AccountantData) ?>;

    $(function () {

        $('.BtnViewReport').on('click', function () {
            var UserID = $(this).closest('.MainTR').attr('UserID');
            var FAKey = json.findIndex(obj => obj['UserID'] == UserID);
            var jsonObj = json[FAKey];

            if (jsonObj.ReportsHistory.length > 0) {
                //ModalBodyViewReport
                var HTML = '<div class="d-flex align-items-center justify-content-md-end justify-content-center mt-1 mb-2" style="column-gap:30px">'

                    + '<div class="d-flex align-items-center"><div style="height: 15px;width: 15px;background-color: var(--approved_color); margin-right:8px; border-radius:50px;"></div><p class="mb-0">Approved</p></div>'

                    + '<div class="d-flex align-items-center"><div style="height: 15px;width: 15px;background-color: var(--pending_color); margin-right:8px; border-radius:50px;"></div><p class="mb-0">Pending</p></div>'

                    + '<div class="d-flex align-items-center"><div style="height: 15px;width: 15px;background-color: var(--rejected_color); margin-right:8px; border-radius:50px;"></div><p class="mb-0">Rejected</p></div>'

                    + '</div>'
                    + '<div class="table-responsive"><table class="table TableReports table_reports" UserID="' + UserID + '">'
                    + '<thead>'
                    + '<tr>'
                    + '<th>Manager Name</th>'
                    + '<th>Manager Email</th>'
                    + '<th>File</th>'
                    + '<th>Accountant Remarks</th>'
                    + '<th>Report Date</th>'
                    + '<th>Approval Status</th>'
                    + '<th>Rejection Remarks</th>'
                    + '<th>Approval Status Date</th>'
                    + '</tr>'
                    + '</thead><tbody>';

                $.each(jsonObj.ReportsHistory, function (i, option) {
                    var ApprovalStatusDateTime = '';
                    if (option.approval_status_datetime) {
                        ApprovalStatusDateTime = moment(option.approval_status_datetime, 'YYYY-MM-DDTHH:mm:ss').format('DD/MM/YYYY');
                    }
                    var AccountantRemarks = '';
                    if (option.accountant_remarks) {
                        AccountantRemarks = option.accountant_remarks;
                    }
                    var RejectionRemarks = option.rejection_remarks == null ? '' : option.rejection_remarks;

                    var TRClass = '';

                    if (option.approval_status == 'Approved') {
                        TRClass = 'tr_approved';
                    }
                    if (option.approval_status == 'Pending') {
                        TRClass = 'tr_pending';
                    }
                    if (option.approval_status == 'Rejected') {
                        TRClass = 'tr_rejected';
                    }

                    HTML += '<tr class="' + TRClass + '">'
                        + '<td>' + option.ManagerFullName + '</td>'
                        + '<td>' + option.ManagerEmail + '</td>'
                        + '<td><a href="' + $('#WebURL').val() + '/' + option.folder + '/' + option.file + '" class="btn btn-sm btn-info" target="_blank">View Report</a></td>'
                        + '<td>' + AccountantRemarks + '</td>'
                        + '<td>' + moment(option.created_at, 'YYYY-MM-DDTHH:mm:ss').format('DD/MM/YYYY') + '</td>'
                        + '<td>' + option.approval_status + '</td>'
                        + '<td>' + RejectionRemarks + '</td>'
                        + '<td>' + ApprovalStatusDateTime + '</td>'
                        + '</tr>';
                });



                HTML += '</tbody></table></div>';
                $('#ModalBodyViewReport').html(HTML);

                $('#ViewReportModal').modal('show');
            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'info',
                    title: "No Reports Found",
                    showConfirmButton: false,
                });
            }
        });

        $('.BtnAssignToAccountant').on('click', function () {
            var AccSelectHtml = '';
            if (AccJson.length > 0) {
                var UserID = $(this).closest('.MainTR').attr('UserID');
                AccSelectHtml = '<form method="post" id="Assign" action="/dashboard/assign_to_accountant">@csrf<select class="form-control" name="accountant_id" id="SelectAccountant">';
                $.each(AccJson, function (i, option) {
                    AccSelectHtml += '<option value="' + option.id + '">' + option.username + '</option>';
                });
                AccSelectHtml += '</select><input type="hidden" name="user_id" value="' + UserID + '"><button class="btn btn-primary mt-1" type="submit">Assign</button></form>';

                Swal.fire({
                    title: "Select an accountant",
                    html: AccSelectHtml,
                    showConfirmButton: false,
                });

            } else {
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: "Accountant not found...",
                    showConfirmButton: false,
                    timer: 1000
                });
            }
        });

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
                        if (AttemptedOption.TextValue != null || AttemptedOption.OptionID != null) {

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
                                                    if (RelatedQuestion.RelatedQuestionOptionTypeID != 6) {
                                                        $('.ULRelatedOption[RelatedQuestionID="' + RelatedQuestion.RelatedQuestionID + '"]').append('<li>' + RelatedOption.RelatedTextValue + '</li>');
                                                    } else {
                                                        if (RelatedOption.RelatedTextValue != "") {
                                                            var RelatedTextArr = JSON.parse(RelatedOption.RelatedTextValue);
                                                            if (RelatedTextArr.length > 0) {
                                                                $.each(RelatedTextArr, function (m, RelatedTextOption) {
                                                                    var HTML = '';
                                                                    if (RelatedTextOption.Data.length > 0) {
                                                                        HTML += '<li>'
                                                                            + '<div class="table-responsive">'
                                                                            + '<table class="table table-bordered table-striped table-hover">'

                                                                            + '<thead>'

                                                                            + '<tr><th colspan="100%" style="text-align:center;font-size:1.2em">' + RelatedTextOption.For + ' | Total - <b>' + RelatedTextOption.Total + '</b></th></tr><tr>';

                                                                        $.each(RelatedTextOption.Data[0].TDValues, function (n, TDValueOption) {
                                                                            HTML += '<th>' + TDValueOption.name + '</th>';
                                                                        });

                                                                        HTML += '</tr></thead>'

                                                                            + '<tbody>';

                                                                        $.each(RelatedTextOption.Data, function (n, DataOption) {
                                                                            HTML += '<tr>';
                                                                            $.each(DataOption.TDValues, function (o, TDValueOption) {
                                                                                HTML += '<td>' + TDValueOption.value + '</td>';
                                                                            });
                                                                            HTML += '</tr>';
                                                                        });

                                                                        HTML += '</tbody>'

                                                                            + '</table>'
                                                                            + '</div>'
                                                                            + '</li>';
                                                                    } else {
                                                                        HTML += '<li>' + RelatedTextOption.Total + ' - ' + RelatedTextOption.For + '</li>';
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
                        } else {
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