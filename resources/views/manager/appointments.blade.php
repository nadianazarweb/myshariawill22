@extends('manager.layouts.master')
@section('css')
<link rel="stylesheet" href="{{url('frontend/css/daterangepicker.css')}}">

<style>
    .radio_time_slot {
        top: 0;
        left: 0;
        opacity: 0;
        position: absolute;
    }

    .label_radio_time_slot {
        padding: 0.5rem 1rem;
        background-color: #508c91;
        color: white;
        border: 4px solid transparent;
        border-radius: 0.357rem;
        cursor: pointer;
        font-size: 14px;
    }

    .radio_time_slot:checked+.label_radio_time_slot {
        border-color: #d7b663;
    }

    .radio_time_slot:disabled+.label_radio_time_slot {
        opacity: 0.8;
    }

    .parent_radio_time_slot {
        display: grid;
        grid-template-columns: repeat(auto-fit, 11em);
        gap: 12px;
        text-align: center;
    }

    .is_delayed .btn_mark_as_approved {
        display: none !important;
    }
</style>
@stop

@section('title')
<title>Book Apointment</title>
@stop
@section('body')
<div class="content-wrapper container-xxl p-0">
    <div class="content-header row"></div>
    <div class="content-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="AppointmentDetailsModal" AppointmentID="" tabindex="-1"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Appointment Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button class="btn btn-info BtnMarkAsApproved btn_mark_as_approved">Mark As Approved</button>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('manager_update_appointment_approval_status') }}" id="FormUpdateAppointmentApprovalStatus" method="post">
            @csrf
            <input type="hidden" name="appointment_id">
            <input type="hidden" name="approval_status">
        </form>

        <form action="{{ route('manager_mark_as_arrived') }}" id="FormMarkAsArrived" method="post">
            @csrf
            <input type="hidden" name="appointment_id">
        </form>


    </div>
</div>
@endsection

@section('javascript')
<script src="{{url('backend/app-assets/js/core/sweetalert2.js')}}"></script>
<script src="{{url('backend/app-assets/js/fullcalendar.global.min.js')}}"></script>
<script src="{{url('frontend/js/moment.min.js')}}"></script>
<script src="{{url('frontend/js/daterangepicker.min.js')}}"></script>
<script>
    var AppointmentData = {!! json_encode($AppointmentData)!!};

    function RenderCalendar() {
        var Events = [];
        if (AppointmentData.length > 0) {
            $.each(AppointmentData, function (i, option) {
                Events.push({
                    title: option.name,
                    start: option.for_date + 'T' + option.for_time_slot_start,
                    end: option.for_date + 'T' + option.for_time_slot_end,
                    customFields: { appointment_id: option.id, name: option.name, email: option.email, contact_no: option.contact_no, for_date: option.for_date, for_time_slot_start: option.for_time_slot_start, for_time_slot_end: option.for_time_slot_end, has_arrived: option.has_arrived, approval_status: option.approval_status }
                });
            });
        }



        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                left: 'prev,today,next',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            eventClick: function (e) {
                $('.BtnMarkAsApproved').show();
                var ExtendedProps = e.event.extendedProps.customFields;
                var HasArrived = '<span class="badge badge-danger">Appointment Not Approved Yet</span>';
                $('#AppointmentDetailsModal').removeClass('is_delayed');

                var Approved = '<span class="badge badge-warning">Pending</span>';
                if (ExtendedProps.approval_status == 'Approved') {
                    $('.BtnMarkAsApproved').hide();
                    Approved = '<span class="badge badge-success">Approved</span>';

                    if (ExtendedProps.has_arrived == '1') {
                        HasArrived = '<span class="badge badge-success">Yes</span>';
                    } else if (ExtendedProps.has_arrived == '0') {
                        // HasArrived = '<span class="badge badge-danger">No</span>';
                        HasArrived = '<button class="btn btn-info btn-sm BtnMarkAsArrived">Mark As Arrived</button>';
                    }
                }

                var DelayedMsg = '';
                if (
                    moment().format('YYYY-MM-DD') > moment(ExtendedProps.for_date, 'YYYY-MM-DD').format('YYYY-MM-DD') ||
                    (moment().format('YYYY-MM-DD') == moment(ExtendedProps.for_date, 'YYYY-MM-DD').format('YYYY-MM-DD') &&
                        moment().format('HH:mm') > moment(ExtendedProps.for_time_slot_start, 'HH:mm').format('HH:mm'))
                ) {
                    DelayedMsg = '<div class="mb-1 text-center"><span class="badge badge-danger">The Date / Time of this appointment has passed</span></div>';
                    $('#AppointmentDetailsModal').addClass('is_delayed');
                }

                $('#AppointmentDetailsModal .modal-body').html(`
                `+ DelayedMsg + `
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Customer Details</th>
                    <th>Appointment</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="vertical-align:baseline">
                        <table class="w-100">
                            <tr>
                                <th class="px-0 border-top-0 border-bottom">Name</th>
                                <td class="px-0 border-top-0 border-bottom">`+ ExtendedProps.name + `</td>
                            </tr>
                            <tr>
                                <th class="px-0 border-top-0 border-bottom">Email</th>
                                <td class="px-0 border-top-0 border-bottom">`+ ExtendedProps.email + `</td>
                            </tr>
                            <tr>
                                <th class="px-0 border-top-0">Contact</th>
                                <td class="px-0 border-top-0">ABC</td>
                            </tr>
                        </table>
                    </td>
                    <td style="vertical-align:baseline">
                        <table class="w-100">
                            <tr>
                                <th class="px-0 border-top-0 border-bottom">Date</th>
                                <td class="px-0 border-top-0 border-bottom">`+ ExtendedProps.for_date + `</td>
                            </tr>
                            <tr>
                                <th class="px-0 border-top-0 border-bottom">Time Slot</th>
                                <td class="px-0 border-top-0 border-bottom">`+ ExtendedProps.for_time_slot_start + ` - ` + ExtendedProps.for_time_slot_end + `</td>
                            </tr>
                            <tr>
                                <th class="px-0 border-top-0">Arrived</th>
                                <td class="px-0 border-top-0">`+ HasArrived + `</td>
                            </tr>

                            <tr>
                                <th class="px-0 border-top-0">Appointment Request</th>
                                <td class="px-0 border-top-0">`+ Approved + `</td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
`);
                $('.BtnMarkAsApproved').on('click', function () {
                    var AppointmentID = ExtendedProps.appointment_id;
                    var ApprovalStatus = 'approved';

                    $('#FormUpdateAppointmentApprovalStatus input[name="appointment_id"]').val(AppointmentID);
                    $('#FormUpdateAppointmentApprovalStatus input[name="approval_status"]').val(ApprovalStatus);

                    Swal.fire({
                        title: "Approve this appointment?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, Approve it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#FormUpdateAppointmentApprovalStatus').submit();
                        }
                    });


                });

                $('.BtnMarkAsArrived').on('click',function(){
                    var AppointmentID = ExtendedProps.appointment_id;
                    $('#FormMarkAsArrived input[name="appointment_id"]').val(AppointmentID);
                    Swal.fire({
                        title: "Mark as arrived?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#FormMarkAsArrived').submit();
                        }
                    });
                });

                // $('#AppointmentDetailsModal').attr('AppointmentID',ExtendedProps.appointment_id);
                $('#AppointmentDetailsModal').modal('show');
            },
            initialDate: moment().format('YYYY-MM-DD'),
            navLinks: true, // can click day/week names to navigate views
            businessHours: true, // display business hours
            editable: true,
            selectable: true,
            events: Events,
        });

        calendar.render();
    }
    document.addEventListener('DOMContentLoaded', function () {
        RenderCalendar();
    });
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
        $('.BtnConfirmArrived').on('click', function () {
            var AppointmentID = $(this).attr('AppointmentID');
            Swal.fire({
                title: "Mark as arrived?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#FormRequest_' + AppointmentID).submit();
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
    })

</script>
@stop