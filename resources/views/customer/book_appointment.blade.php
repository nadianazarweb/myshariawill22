@extends('customer.layouts.master')
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
</style>
@stop

@section('title')
<title>Book Apointment</title>
@stop
@section('body')
<div class="content-wrapper container-xxl p-0">
    <div class="content-header row"></div>
    <div class="content-body">
        @if($AppointmentPaymentData->is_appointment_purchased==0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <h4 class="mb-2">In order to create an appointment, you need to purchase a package of £29.99.</h4>
                            <form action="{{ route('customer_appointment_purchase') }}" method="post">
                                @csrf
                                <button class="btn btn-primary" type="submit">Buy Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @elseif($AppointmentPaymentData->is_appointment_purchased==1)
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title text-success"><i class="fas fa-check-circle mr-1"></i>Subscription Is Active</h4>
                    </div>
                    <div class="card-body">
                        <table class="w-100">
                            <tr>
                                <th>Subscription Date:</th>
                                <td>{{ date('d/m/Y',strtotime($AppointmentPaymentData->appointment_payment_date)) }}</td>
                            </tr>
                            <tr>
                                <th>Amount Paid:</th>
                                <td>£29.99</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Book an Appointment</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('customer_add_appointment') }}" class="FormAddAppointment" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="">Select Date</label>
                                <input type="text" readonly class="form-control" name="for_date"
                                    placeholder="Select Date" value="" />
                            </div>

                            <div id="AppendTimeSlotsHere">

                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Appointments</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <th>Date</th>
                                    <th>Time Slot</th>
                                    <th>Arrival Status</th>
                                    <th>Approval Status</th>
                                </thead>

                                <tbody>
                                    @if(count($AppointmentData)>0)
                                    @foreach($AppointmentData as $ADKey=>$ADItem)
                                    <tr>
                                        <td>{{ $ADItem['for_date'] }}</td>
                                        <td>{{ $ADItem['for_time_slot_start'].' - '.$ADItem['for_time_slot_end'] }}</td>
                                        <td>{{ $ADItem['has_arrived']=="0" ? 'Not Arrived' : 'Arrived' }}</td>
                                        <td>{{ $ADItem['approval_status'] }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="100%" class="text-center">No Data Found</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center mt-2">
                                {{$AppointmentData->withQueryString()->links()}}
                            </div>
                        </div>
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
<script src="{{url('frontend/js/moment.min.js')}}"></script>
<script src="{{url('frontend/js/daterangepicker.min.js')}}"></script>

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
    $(function () {
        $('input[name="for_date"]').val('');
        $('input[name="for_date"]').daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            },
            showDropdowns: true,
            minDate: moment()
        }, function (start, end, label) {
            $('#AppendTimeSlotsHere').empty();



            var DataToSend = new Object();
            DataToSend._token = "{{csrf_token()}}";
            DataToSend.for_date = moment(start).format('YYYY-MM-DD');

            var PleaseWaitSwal;
            $.ajax({
                url: '/customer/booked_time_slots',
                method: 'POST',
                beforeSend: function () {
                    PleaseWaitSwal = Swal.fire({
                        position: 'center',
                        title: "Please wait while we're fetching the time slots...",
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                },
                data: DataToSend,
                success: function (e) {
                    if (e) {
                        PleaseWaitSwal.close();

                        var Iteration = 0;
                        var HTML = '<div class="form-group">'
                            + '<label for="">Select Time Slot</label>'
                            + '<div class="position-relative parent_radio_time_slot">';
                        var Data = e.data;
                        for (var hour = 9; hour < 17; hour++) {
                            for (var minute = 0; minute < 60; minute += 30) {

                                var TimeSlotStart = hour + ':' + (minute < 10 ? '0' : '') + minute;
                                var nextHour = hour + (minute === 30 ? 1 : 0);
                                var nextMinute = (minute === 30 ? '00' : '30');
                                var TimeSlotEnd = nextHour + ':' + nextMinute;

                                var FinalTimeSlot = TimeSlotStart + ' - ' + TimeSlotEnd;
                                var Disabled = '';

                                if (moment(start).format('YYYY-MM-DD') == moment().format('YYYY-MM-DD') && hour < parseInt(moment().format('HH'))) {
                                    Disabled = 'disabled';
                                }

                                if (Data.length > 0) {
                                    for (var i = 0; i < Data.length; i++) {
                                        if (Data[i].for_time_slot_start == TimeSlotStart && Data[i].for_time_slot_end == TimeSlotEnd) {
                                            FinalTimeSlot = 'Booked';
                                            Disabled = 'disabled';
                                            break;
                                        }
                                    }
                                }


                                HTML += '<input type="radio" class="radio_time_slot" ' + Disabled + ' name="time_slot" value="' + FinalTimeSlot + '" id="radio_time_slot_' + Iteration + '"><label for="radio_time_slot_' + Iteration + '" class="label_radio_time_slot">' + FinalTimeSlot + '</label>';
                                Iteration++;
                            }
                        }

                        HTML += '</div>'
                            + '</div><div class="form-group"><button class="btn btn-info">Save</button></div>';

                        $('#AppendTimeSlotsHere').html(HTML);
                        $('input[name="time_slot"]:disabled').prop('disabled', true);

                    } else {
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: e.msg,
                            showConfirmButton: true,
                        });
                    }
                }
            });

            $('input[name="for_date"]').val(moment(start).format('YYYY-MM-DD'));
            // var years = moment().diff(start, 'years');
            // alert("You are " + years + " years old!");
        });

        $('.FormAddAppointment').on('submit', function (e) {
            if ($('.radio_time_slot:checked').length == 0) {
                e.preventDefault();
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Please select a time slot',
                    showConfirmButton: true,
                });
            }
        });
    });
</script>
@stop