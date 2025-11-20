@extends('accountant.layouts.master')
@section('css')
<style>
    .cursor_pointer {
        cursor: pointer;
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
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active BtnTab" id="approved-tab" data-toggle="tab" href="#approved"
                                    aria-controls="approved" role="tab" aria-selected="true">Approved<span
                                        class="badge badge-danger ml-1"></span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link BtnTab" id="rejected-tab" data-toggle="tab" href="#rejected"
                                    aria-controls="rejected" role="tab" aria-selected="false">Rejected<span
                                        class="badge badge-success ml-1"></span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link BtnTab" id="pending-tab" data-toggle="tab" href="#pending"
                                    aria-controls="pending" role="tab" aria-selected="false">Pending<span
                                        class="badge badge-success ml-1"></span></a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link BtnTab" id="report_history-tab" data-toggle="tab" href="#report_history"
                                    aria-controls="report_history" role="tab" aria-selected="false">Report History<span
                                        class="badge badge-success ml-1"></span></a>
                            </li>

                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="approved" aria-labelledby="approved-tab" role="tabpanel">
                            </div>

                            <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                                
                            </div>

                            <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                                
                            </div>

                            <div class="tab-pane fade" id="report_history" role="tabpanel" aria-labelledby="report_history-tab">
                                
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
@endsection

@section('javascript')
<script src="{{url('backend/app-assets/js/core/sweetalert2.js')}}"></script>

<script>
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

    $(function(){
        ReportsRender('#approved');
        $('.BtnTab').on('shown.bs.tab', function(){
            var TabID = $(this).attr('href');
            ReportsRender(TabID);
        });
    })

    function ReportsRender(TabID){
        // $(TabID).html('<h1>hello whatsup</h1>');
        var TabAttrID = $(TabID).attr("id");

        var url = "{{ route('fetch_reports', ['ReportType'=>':ReportType']) }}";
        url = url.replace(':ReportType',TabAttrID);

        $.ajax({
            url:url,
            method:'GET',
            success:function(e){
                if(e.status){
                    if(e.Data.length>0){
                        var RejectedRemarksTH = '';
                        if(TabAttrID=='rejected'){
                            RejectedRemarksTH = '<th>Remarks</th>';
                        }
                        var HTML = '<div class="table-responsive">'
                        +'<table class="table">'
                        +'<thead>'
                        +'<tr>'
                        +'<th>UserName</th>'
                        +'<th>Email</th>'
                        +'<th>Contact No</th>'
                        +'<th>Submission Date</th>'
                        +'<th>File</th>'
                        +RejectedRemarksTH
                        +'</tr>'
                        +'</thead>'
                        $.each(e.Data, function(i, option){
                            var RejectedRemarksTD = '';
                            if(TabAttrID=='rejected'){
                                RejectedRemarksTD = '<td>'+option.RejectionRemarks+'</td>';
                            }
                            HTML += '<tr>'
                            +'<td>'+option.UserFullName+'</td>'
                            +'<td>'+option.UserEmail+'</td>'
                            +'<td>'+option.UserContactNo+'</td>'
                            +'<td>'+option.QuerySubmissionDateTime+'</td>'
                            +'<td><a href="'+option.ReportFileName+'" target="_blank" class="btn btn-info">View Report</a></td>'
                            +RejectedRemarksTD
                            +'</tr>';
                        });

                        HTML += '</table></div>';

                        $(TabID).html(HTML);
                    }else{
                        $(TabID).html('<h3 class="text-center">No Data Found</h3>');
                    }
                }
            }
        })
    }
</script>
@stop