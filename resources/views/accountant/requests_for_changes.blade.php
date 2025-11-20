@extends('accountant.layouts.master')
@section('css')
<style>
    .cursor_pointer {
        cursor: pointer;
    }
</style>
@stop

@section('title')
<title>Requests For Changes</title>
@stop
@section('body')
<div class="content-wrapper container-xxl p-0">
    <div class="content-header row"></div>
    <div class="content-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pending-tab" data-toggle="tab" href="#pending"
                                    aria-controls="pending" role="tab" aria-selected="true">Pending <span
                                        class="badge badge-danger ml-1">{{ $pendingCount }}</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="fulfilled-tab" data-toggle="tab" href="#fulfilled"
                                    aria-controls="fulfilled" role="tab" aria-selected="false">Fulfilled <span
                                        class="badge badge-success ml-1">{{ $fulfilledCount }}</span></a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="pending" aria-labelledby="pending-tab" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>UserName</th>
                                                <th>Email</th>
                                                <th>Contact</th>
                                                <th>Request</th>
                                                <th>Show Data</th>
                                                <th>Status</th>
                                                <th>Request Date</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($RequestChanges)>0)
                                            @foreach($RequestChanges as $key=>$item)
                                            @if($item->RequestFulfillmentStatus=="Pending")
                                            <tr class="MainTR" UserID="{{ $item->UserID }}">
                                                <td>{{ $item->UserFullName }}</td>
                                                <td>{{ $item->UserEmail }}</td>
                                                <td>{{ $item->ContactNo }}</td>
                                                <td>{{ $item->RequestRemarks }}</td>
                                                <td><button class="btn btn-info BtnShowData">Show Data</button></td>
                                                <td>{{ $item->RequestFulfillmentStatus }}</td>
                                                <td>{{ date('d/m/Y h:i', strtotime($item->created_at)) }}</td>
                                                <td>
                                                    <button
                                                        class="btn btn-success btn-sm rounded-0 BtnConfirmFulfillment"
                                                        RequestChangeID="{{$item->RequestChangesID}}">Mark As
                                                        Fulfilled</button>
                                                    <form method="post" action="{{route('mark_as_fulfilled')}}"
                                                        id="FormRequest_{{ $item->RequestChangesID }}">@csrf<input
                                                            type="hidden" name="request_change_id"
                                                            value="{{$item->RequestChangesID}}"></form>
                                                </td>
                                            </tr>
                                            @endif
                                            @endforeach
                                            @else

                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="fulfilled" role="tabpanel" aria-labelledby="fulfilled-tab">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>UserName</th>
                                                <th>Email</th>
                                                <th>Contact</th>
                                                <th>Request</th>
                                                <th>Status</th>
                                                <th>Request Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($RequestChanges)>0)
                                            @foreach($RequestChanges as $key=>$item)
                                            @if($item->RequestFulfillmentStatus=="Fulfilled")
                                            <tr>
                                                <td>{{ $item->UserFullName }}</td>
                                                <td>{{ $item->UserEmail }}</td>
                                                <td>{{ $item->ContactNo }}</td>
                                                <td>{{ $item->RequestRemarks }}</td>
                                                <td>{{ $item->RequestFulfillmentStatus }}</td>
                                                <td>{{ date('d/m/Y h:i', strtotime($item->created_at)) }}</td>
                                            </tr>
                                            @endif
                                            @endforeach
                                            @else

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
    var json = <?= json_encode($FinalArray) ?>;
    $('.BtnShowData').on('click', function () {
            $('.ModalListGroup').empty();
            var UserID = $(this).closest('.MainTR').attr('UserID');
           
            var FAKey = json.findIndex(obj => obj['UserID'] == UserID);
            $.each(json[FAKey].AttemptedAnswers, function (i, option) {
                var QuestionID = option.QuestionID;
                var QuestionTitle = option.QuestionTitle;
                var ParentQuestionSortID = option.ParentQuestionSortID;

                var EditURL = "{{ route('edit_attempted_answer', ['UserID'=>':UserID','QuestionID'=>':QuestionID']) }}";
                EditURL = EditURL.replace(':UserID',UserID);
                EditURL = EditURL.replace(':QuestionID',QuestionID);
                $('.ModalListGroup').append('<li class="list-group-item LIParentQuestion" ParentQuestionID="' + QuestionID + '"><b>' + SubAlphabets[option.ParentQuestionSortID] + '</b> - ' + QuestionTitle + ' <a class="fas fa-edit ml-2 text-info" href="'+EditURL+'"></a></li>');
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

    $('.BtnConfirmFulfillment').on('click', function () {
        var RequestChangeID = $(this).attr('RequestChangeID');

        Swal.fire({
            title: "Mark as fulfilled?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes"
        }).then((result) => {
            if (result.isConfirmed) {
                $('#FormRequest_' + RequestChangeID).submit();
                Swal.fire({
                    position: 'center',
                    icon: 'info',
                    title: "Please Wait...",
                    showConfirmButton: false,
                    allowOutsideClick: false
                });
            }
        });
    });
</script>
@stop