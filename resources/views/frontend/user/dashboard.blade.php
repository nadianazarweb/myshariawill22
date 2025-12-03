@extends('frontend.layouts.master')
@section('title')
Dashboard
@endsection
@section('Style')
<link rel="stylesheet" href="{{url('frontend/css/daterangepicker.css')}}">
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>

/*DATE RANGE PICKER STARTS*/

.custom-daterangepicker {
      width: 272px; /* Adjust the width as needed */
      /* font-family: Arial, sans-serif; */
      font-size: 14px;
      color: #333;
    }
    .custom-daterangepicker .drp-buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 10px; /* Add some spacing */
    }
    .custom-daterangepicker .drp-buttons button {
      padding: 8px 12px;
      border-radius: 4px;
      border: none;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .custom-daterangepicker .drp-buttons button.applyBtn {
      background-color: #424d7c; /* Apply color theme to Apply button */
      color: #fff;
    }
    .custom-daterangepicker .drp-buttons button.applyBtn:hover {
      background-color: #303857; /* Darken the color on hover */
    }
    .custom-daterangepicker .drp-calendar {
      margin-top: 10px; /* Add some spacing */
      width: 280px; /* Adjust the width of the calendar */
    }
    .custom-daterangepicker .calendar-table {
      border-collapse: collapse;
      width: 100%; /* Set the width of the calendar table to 100% */
    }
    .custom-daterangepicker .calendar-table th,
    .custom-daterangepicker .calendar-table td {
      padding: 5px; /* Adjust the padding as needed */
      text-align: center;
    }
    .custom-daterangepicker .calendar-table th {
      font-weight: bold;
    }
    .custom-daterangepicker .calendar-table td.available {
      cursor: pointer;
    }
    .custom-daterangepicker .calendar-table td.available:hover {
      background-color: #f0f0f0;
    }
    .custom-daterangepicker .calendar-table td.off {
      color: #ccc;
    }
    .custom-daterangepicker .calendar-table td.in-range {
      background-color: #e0e0e0;
    }
    .custom-daterangepicker .calendar-table td.active,
    .custom-daterangepicker .calendar-table td.active:hover {
      background-color: #424d7c; /* Set the color theme */
      color: #fff;
    }

/*DATE RANGE PICKER ENDS*/


    .custom_container {
        max-width: 800px;
    }

    .custom_progressbar_parent {
        max-width: 1120px;
    }



    .btn_custom_dropdown {
        width: 100%;
    }

    .btn_custom_dropdown::after {
        font-family: "Font Awesome 6 Pro";
        border: unset;
        vertical-align: unset;
        /* margin-left: 30px; */
        display: unset !important;
    }

    .btn_custom_dropdown:not(.show)::after {
        content: "\f078";
    }

    .btn_custom_dropdown.show::after {
        content: "\f077";
    }

    .logo {
        width: 100%;
        max-width: 329px;
        display: block;
        margin: auto auto 30px auto;
    }

    .main_form {
        max-width: 400px;
        width: 100%;
    }

    /* .btn_back:hover .left_arrow_icon{
    margin-right:20px !important;
  } */

    .left_arrow_icon {
        right: 100%;
        margin-right: 10px;
    }

    .right_arrow_icon {
        left: 100%;
        margin-left: 5px;
    }

    .arrow_icon {
        position: absolute;
    }

    .btn_back:hover .arrow_icon {
        margin-right: 15px;
        transition: 0.5s;
    }

    .btn_next:hover .arrow_icon {
        margin-left: 10px;
        transition: 0.5s;
    }

    .qna_main_div {
        width: 100%;
    }

    .question_heading {
        font-size: 20px;
        color: var(--custom_primary);
    }

    .question_alphabet {
        border-radius: 69px;
        padding: 5px 14px;
        background-color: rgb(234, 234, 234);
        font-size: 22px;
        pointer-events: none;
        color: var(--custom_primary);
        font-weight: bold;
        margin-right: 15px;
    }

    .option_alphabet {
        /* border: solid 1px rgb(255, 221, 216); */
        border: solid 2px var(--custom_primary);
        /* border-radius:6px; */
        border-radius: 50px;
        /* padding: 0px 15px; */
        padding: 5px;
        background-color: rgb(255, 255, 255);
        height: 25px;
        width: 25px;
        overflow: hidden;
    }

    .custom_radio {
        height: 100%;
        width: 100%;
        border-radius: 50px;
    }

    .option_label {
        position: relative;
        border: 1px solid #ccc;
        /* background-color: rgb(255, 249, 248); */
        background-color: #424d7c14;
        border-radius: 6px;
        /* padding:15px 20px 15px 20px; */
        cursor: pointer;
        /* height:70px; */
        font-size: 20px;
        color: var(--custom_primary);
        overflow: hidden;
        transition: 0.2s ease-in-out;
    }

    /* .input_radio_option:not(:checked) + .option_label:hover{
        background-color:#818bb5;
    }

    .input_radio_option:not(:checked) + .option_label:hover .option_text{
        color:white;
    } */

    .input_radio_option:not(:checked)+.option_label:hover {
        background-color: #424d7c38;
    }

    .input_radio_option:not(:checked)+.option_label:hover .option_text {
        color: black;
    }



    .nav_header {
        background-image: linear-gradient(140deg, #424D7C 0%, #35757D 72%)
    }

    .option_label_child_div {
        padding: 15px;
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
    }

    .option_text {
        margin-left: 1.5rem;
        transition: 0.3s ease-in-out;
    }

    .input_radio_option {
        position: absolute;
        z-index: -1;
        opacity: 0;
    }

    .option_label::after {
        content: '';
        position: absolute;
        top: 0;
        height: 100%;
        left: 0;
        width: 0%;
        background-color: var(--custom_primary);
        transition: 0.3s ease-in-out;
    }

    .input_radio_option:checked+.option_label .option_alphabet>.custom_radio {
        background-color: var(--custom_primary);
    }

    .input_radio_option:checked+.option_label {
        border: 1px solid var(--custom_primary);
        background-color: #424d7c4a;
    }

    /* .input_radio_option:checked+.option_label::after {
        width: 100%;
    } */

    /* .input_radio_option:checked+.option_label .option_text {
        color: #f5f5f5;
    } */

    .options_main_div {
        opacity: 0;
        animation: show_options_animation 0.8s forwards;
    }

    @keyframes show_options_animation {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .refreshing_icon_div {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #ffffffb2;
        z-index: 1;
    }

    .refreshing_icon {
        font-size: 4em;
    }

    .custom_textarea,
    .custom_input,
    .custom_select {
        width: 100%;
        border-color: var(--custom_primary) !important;
        border-radius: 6px !important;
        border-width: 3px !important;
        padding: 15px !important;
    }

    /* select{
        border: 3px solid var(--custom_primary) !important;
        padding: 15px !important;
    } */

    .custom_progressbar {
        /* border-radius: 50px; */
        border-radius: 0.375rem;
        /* max-width: 1000px;
        margin: auto; */
        text-align: center;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        /* box-shadow: 0em 0.1em 0.3em 0em #838383; */
        column-gap: 10px;
    }



    @media screen and (min-width:768px) {
        /* .q_section_div:not(:last-child) {
            border-right: 3px inset black;
        } */

        #ui-datepicker-div {
            width: 400px;
        }

        .q_section_div:first-child {
            border-top-left-radius: 0.375rem;
            border-bottom-left-radius: 0.375rem;
        }

        .q_section_div:last-child {
            border-top-right-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
        }
    }

    @media screen and (max-width:767px) {
        /* .q_section_div:not(:last-child) {
            border-bottom: 3px inset black;
        } */

        .custom_progressbar {
            flex-direction: column;
            max-width: 492px;
            display: block;
            margin: auto;
        }

        .q_section_div{
            justify-content:start !important;
        }

    }


    .q_section_div {
        flex: 1;
        position: relative;
        z-index: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        background-color: #35757d;
        display: flex;
        align-items: center;
        border: 4px solid transparent;
    }

    .q_section_div_child {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        background-color: var(--custom_primary);
        z-index: -1;
        transition: all 0.5s ease-in-out 0s;
    }

    .q_section_div_child.w-100+.section_info>.q_section_check {
        display: inline-block !important;
    }

    .q_section_div_child.w-100+.section_info>.section_percentage {
        display: none;
    }

    .q_section_check {
        display: none;
        font-size: 1.2em;
    }

    .q_section_text {
        font-size: 0.8em;
        text-shadow: 1px 1px black;
    }

    .active_q_section {
        color: #f9cb59;
        border-color: var(--custom_primary);
    }

    .section_info {
        height: 27px;
        width: 27px;
        display: flex;
        align-items: center;
    }

    .section_percentage {
        font-size: 0.7em;
        border-radius: 50%;
        background-color: white;
        color: black;
        height: 90%;
        width: 90%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0;
    }

    .custom_pointer_none {
        pointer-events: none !important;
    }

    .custom_pointer_all {
        pointer-events: all;
    }

    .ul_num_of_children {
        background-color: #7184d5;
        width: 100%;
        max-width: 100% !important;
    }

    .ul_num_of_children>li {
        color: white;
    }

    .ul_num_of_children>li:hover,
    .ul_num_of_children>li:active {
        background-color: transparent;
        color: white;
    }

    .table_children {
        max-width: 260px;
        border-collapse: separate;
        border-spacing: 18px 0px;
    }

    .div_incre_decre_data_for {
        /* max-width: 1000px; */
        /* display: block; */
        /* margin: auto; */
        /* display: flex; */
        /* column-gap: 2em; */
        /* row-gap: 2em; */
        /* flex-wrap: wrap; */
        /* flex-direction: column; */
    }

    /* .div_incre_decre_data_for_child {
        flex: 1;
    } */

    footer {
        background-color: var(--custom_primary);
        color: #fff;
        text-align: center;
        padding: 10px;
        width: 100%;
        font-size: 0.8em;
        position: fixed;
        bottom: 0;
        z-index: 1;
    }

    .btn_incre_decre {
        /* font-size:2.5em; */
        display: flex;
        align-items: center;
        height: 100%;
        background-color: var(--custom_primary);
        color: white;
        padding: 0px 15px;
        cursor: pointer;
    }




    .ui-state-default:not(.ui-state-active):not(.ui-state-hover) {
        background-color: #424d7c14 !important;
    }

    .ui-state-default.ui-state-active {
        background: var(--custom_primary);
        color: white;
    }

    .ui-state-default.ui-state-hover:not(.ui-state-active) {
        background: #35757d;
        color: white;
    }

    .tooltip_info{
        color:var(--custom_primary);
        cursor:pointer;
    }
</style>
@endsection

@section('MainSection')
@php
$Alphabets = range('A','Z');
@endphp
<div style="position:relative;margin-bottom:150px; margin-top:150px">
    <div>
        <h1 class="mb-0 text-center text-light p-4 fw-bold nav_header position-fixed w-100 top-0 shadow"
            style="z-index:1001">
            Sharia Compliant Form</h1>
        <div>
            <div class="custom_progressbar_parent container">
                <div class="custom_progressbar" style="">
                    @foreach($QSData as $item)
                    <div class="py-2 px-2 q_section_div QSectionDiv" QSectionID="{{ $item->id }}">
                        <!-- <div class="q_section_div_child" style="width:100%;"></div> -->
                        <div class="q_section_div_child QSectionDivChild"></div>
                        <div class="section_info me-2">
                            <i class="fas fa-circle-check q_section_check"></i>
                            <p class="section_percentage SectionPercentage p-1" TotalPercentage="0">0%</p>
                        </div>
                        <p class="mb-0 q_section_text">{{ strtoupper($item->title) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="container custom_container">


                <div id="AppendQuestionsHere"
                    class="position-relative p-4 shadow my-4 rounded d-flex align-items-center justify-content-center flex-column"
                    style="min-height:198.2px">
                </div>

                <div id="AppendNextBackBtnsHere"
                    class="d-flex justify-content-md-end justify-content-center custom_pointer_none">
                    <button class="btn btn-sm custom_btn_primary rounded btn_back me-1 BtnBack custom_pointer_all">
                        <span class="position-relative ms-4">
                            <i class="fa-regular fa-arrow-left left_arrow_icon arrow_icon"></i>
                            <span>Back</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>




    </div>

    <footer>
    <p class="mb-0">&copy; {{date('Y')}} - My Sharia Will - All Rights Reserved | Created with <i class="fas fa-heart text-danger"></i> by <a href="https://www.optimizedtechandbi.co.uk" target="_blank" class="text-white">Optimized Tech & Bi</a>
    </p>
    </footer>

</div>

<div class="modal fade" id="ReviewAlQuestionsModal" tabindex="-1" aria-labelledby="ReviewAlQuestionsModal"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ReviewAlQuestionsModal">Review All Questions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height:73vh; overflow:auto">
                <ul class="list-group ModalListGroup">

                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn custom_btn_primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('Script')
<script src="{{url('backend/app-assets/js/core/sweetalert2.js')}}"></script>
<script src="{{url('frontend/js/moment.min.js')}}"></script>
<script src="{{url('frontend/js/daterangepicker.min.js')}}"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
{{-- <script src="{{url('frontend/js/jquery.mask.min.js')}}"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>


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
                // $('TD[IncreDecreGetDataItem="Sort Code"] .InputGetIncreDecreData').mask('99-99-99');
                // $('TD[IncreDecreGetDataItem="Account Number"] .InputGetIncreDecreData').mask('99999999');

                $('TD[IncreDecreGetDataItem="Sort Code"] .InputGetIncreDecreData').inputmask('99-99-99');
                $('TD[IncreDecreGetDataItem="Account Number"] .InputGetIncreDecreData').inputmask('99999999');
            }

        }
    }

    function AutoLoadIncreDecreTable(PreDefinedValuesArr) {
        var IncreDecreGetData = $('.TableChildren').attr('IncreDecreGetData');
        if (IncreDecreGetData != "") {
            $('.IncreDecreInput').each(function () {
                var IncreDecreDataFor = $(this).closest('tr').attr('IncreDecreDataForItem');
                var Index = PreDefinedValuesArr.findIndex(obj => obj['For'] == IncreDecreDataFor);
                if (Index >= 0) {
                    if (PreDefinedValuesArr[Index].Data.length > 0) {
                        var NumberOfRows = $(this).val();
                        console.log('num of rows are ', NumberOfRows);
                        if (NumberOfRows > 0) {
                            for (var i = 0; i < NumberOfRows; i++) {
                                if (PreDefinedValuesArr[Index].Data[i]) {
                                    InsertingIncreDecreTable(IncreDecreDataFor, IncreDecreGetData, PreDefinedValuesArr[Index].Data[i].TDValues);
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    var PropertyOwnershipOptions = ['Fully Owned','Joint Tenants','Tenants In Common'];
    function InsertingIncreDecreTable(IncreDecreDataFor, IncreDecreGetData, PreDefinedValuesObj = null) {
        if ($('.DivIncreDecreDataForChild[IncreDecreDataFor="' + IncreDecreDataFor + '"] tbody.TBodyIncreDecreDataFor tr').length < 10) {

            var Index = $('.DivIncreDecreDataForChild[IncreDecreDataFor="' + IncreDecreDataFor + '"] tbody.TBodyIncreDecreDataFor tr').length;

            var TBodyTR = '<tr><td>' + parseInt(Index + 1) + '</td>';
                $.each(IncreDecreGetData.split(';'), function (i, option) {
                var PreAttemptedValue = "";

                if (PreDefinedValuesObj != null) {
                    var PreDefinedIndex = PreDefinedValuesObj.findIndex(obj => obj['name'] == option);
                    PreAttemptedValue = PreDefinedValuesObj[PreDefinedIndex].value;
                }
                if(IncreDecreDataFor=="Wife" && option.trim()=="Civil Marriage"){
                    var CivilMarriageRadioIndex = $('.DivIncreDecreDataForChild[IncreDecreDataFor="Wife"] .TBodyIncreDecreDataFor TR').length;
                    var CheckedCivilMarriage = '';
                    if($('TD[IncreDecreGetDataItem="'+option+'"]').length==0){
                        CheckedCivilMarriage = 'checked';
                    }
                    if(PreAttemptedValue == "Is Civil Marriage"){
                        CheckedCivilMarriage = 'checked';
                    }
                    TBodyTR += '<td IncreDecreGetDataItem="' + option + '">'
                        // +'<input type="radio" class="form-control input_get_incre_decre_data InputGetIncreDecreData" value="' + PreAttemptedValue + '">'
                        +`<div class="form-check">
  <input class="form-check-input InputGetIncreDecreData" type="radio" name="`+option+`" id="CivilMarriageInput_`+CivilMarriageRadioIndex+`" `+CheckedCivilMarriage+`>
  <label class="form-check-label" for="CivilMarriageInput_`+CivilMarriageRadioIndex+`">
    Civil Marriage
  </label>
</div>
`
                        +'</td>';
                    CivilMarriageRadioIndex++;
                }
                else if(IncreDecreDataFor=="Property" && option.trim()=="Ownership"){

                    TBodyTR += '<td IncreDecreGetDataItem="' + option + '">';

                    TBodyTR += '<select class="form-select InputGetIncreDecreData" aria-label="Default select example">';
                        for(var pi=0;pi<PropertyOwnershipOptions.length;pi++){
                            var Selected = '';
                            if(PreAttemptedValue == PropertyOwnershipOptions[pi]){
                                Selected = 'selected';
                            }
                            TBodyTR += '<option value="'+PropertyOwnershipOptions[pi]+'" '+Selected+'>'+PropertyOwnershipOptions[pi]+'</option>';
                        }
                    TBodyTR += '</select>';


                    TBodyTR += '</td>';
                }else if(IncreDecreDataFor=="Property" && option.trim()=="Property Type"){
                    var PropertyTypeIndex = $('.DivIncreDecreDataForChild[IncreDecreDataFor="Property"] .TBodyIncreDecreDataFor TR').length;

                    var ResidentialChecked = '';
                    var InvestmentChecked = '';

                    if(PreAttemptedValue == 'Residential'){
                        ResidentialChecked = 'checked';
                    }

                    if(PreAttemptedValue == 'Investment'){
                        InvestmentChecked = 'checked';
                    }


                    TBodyTR += '<td IncreDecreGetDataItem="' + option + '">';

                    TBodyTR += `<div class="form-check form-check-inline">
  <input class="form-check-input InputGetIncreDecreData RadioPropertyType" type="radio" name="PropertyType_`+PropertyTypeIndex+`" id="PropertyTypeResidentialInput_`+PropertyTypeIndex+`" value="Residential" `+ResidentialChecked+`>
  <label class="form-check-label" for="PropertyTypeResidentialInput_`+PropertyTypeIndex+`">Residential</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input InputGetIncreDecreData RadioPropertyType" type="radio" name="PropertyType_`+PropertyTypeIndex+`" id="PropertyTypeInvestmentInput_`+PropertyTypeIndex+`" value="Investment" `+InvestmentChecked+`>
  <label class="form-check-label" for="PropertyTypeInvestmentInput_`+PropertyTypeIndex+`">Investment</label>
</div>`;


                    TBodyTR += '</td>';
                    PropertyTypeIndex++;
                }
                else{
                    TBodyTR += '<td IncreDecreGetDataItem="' + option + '"><input type="text" class="form-control input_get_incre_decre_data InputGetIncreDecreData" value="' + PreAttemptedValue + '" placeholder="Enter ' + option.trim() + '"></td>';
                }

            });
            TBodyTR += '</tr>';
            if ($('.DivIncreDecreDataFor').length == 0) {
                $('#AppendQuestionsHere').append('<div class="div_incre_decre_data_for DivIncreDecreDataFor" IncreDecreGetData="' + IncreDecreGetData + '"></div>');
            }
            if ($('.DivIncreDecreDataForChild[IncreDecreDataFor="' + IncreDecreDataFor + '"]').length == 0) {
                var THeadTR = '<tr><th></th>';
                $.each(IncreDecreGetData.split(';'), function (i, option) {
                    THeadTR += '<th>' + option + '</th>';
                });
                THeadTR += '</tr>';

                $('.DivIncreDecreDataFor').append('<div class="div_incre_decre_data_for_child DivIncreDecreDataForChild shadow p-4 mt-4 rounded" IncreDecreDataFor="' + IncreDecreDataFor + '">'
                    + '<div class="table-responsive">'
                    + '<h4 class="text-center">' + IncreDecreDataFor + '</h4>'
                    + '<table class="custom_table">'
                    + '<thead>'
                    // + '<tr>'
                    // + '<th colspan="100%" class="text-center" style="font-size:1.4em">' + IncreDecreDataFor + '</th>'
                    // + '</tr>'
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
            if($('.RadioPropertyType').length>0){
                        if($('.RadioPropertyType[value="Residential"]:checked').length>0){
                            $('.RadioPropertyType[value="Residential"]:not(:checked)').prop('disabled',true);
                        }
                    }
        }
    }

    $(function(){
        $(document.body).on('change','.RadioPropertyType',function(){
            if($(this).val()=='Residential'){
                $('.RadioPropertyType[value="Residential"]').not(this).prop('disabled',true);
            }else if($(this).val()=='Investment'){
                if(!$(this).closest('TD').find('.RadioPropertyType[value="Residential"]').is(':disabled')){
                    $('.RadioPropertyType[value="Residential"]').prop('disabled',false);
                }
            }
        });
    });

    function IncreDecreTableAppend(BtnThis, IncreDecre) {
        var IncreDecreGetData = $('.TableChildren').attr('IncreDecreGetData');
        if (IncreDecreGetData != "") {
            var IncreDecreDataFor = BtnThis.closest('tr').attr('IncreDecreDataForItem');
            if (IncreDecre == "Increment") {

                InsertingIncreDecreTable(IncreDecreDataFor, IncreDecreGetData);


            } else if (IncreDecre == "Decrement") {
                if ($('.DivIncreDecreDataForChild[IncreDecreDataFor="' + IncreDecreDataFor + '"] tbody.TBodyIncreDecreDataFor tr').length > 1) {
                    $('.DivIncreDecreDataForChild[IncreDecreDataFor="' + IncreDecreDataFor + '"] tbody.TBodyIncreDecreDataFor tr:last-child').remove();
                } else {
                    $('.DivIncreDecreDataForChild[IncreDecreDataFor="' + IncreDecreDataFor + '"]').remove();
                }
            }

        }
    }

    var IsEdit = false;

    $(function () {

        // $(document.body).on('keyup','TD[IncreDecreGetDataItem="Sort Code"] .InputGetIncreDecreData', function(event){
        //     var input = event.target;
        //     var value = input.value.replace(/\D/g, ''); // Remove non-digit characters
        //     // Check if the value matches the pattern without hyphens
        //     var formattedValue = '';

        //     value = value.slice(0, 6);

        //     // Format the value with hyphens at appropriate positions
        //     for (var i = 0; i < value.length; i++) {
        //         if (i !== 0 && i % 2 === 0) {
        //             formattedValue += '-';
        //         }
        //         formattedValue += value[i];
        //     }
        //     $(this).val(formattedValue);
        // });

        // $(document.body).on('keyup','TD[IncreDecreGetDataItem="Account Number"] .InputGetIncreDecreData', function(event){
        //     var input = event.target;
        //     var value = input.value.replace(/\D/g, ''); // Remove non-digit characters
        //     // Check if the value matches the pattern without hyphens

        //     value = value.slice(0, 8);

        //     $(this).val(value);
        //});

        // $(document.body).on('keyup','.IsDateInput', function(e){
        //     //To accomdate for backspacing, we detect which key was pressed - if backspace, do nothing:
        //     if(e.which !== 8) {
        //         var numChars = $(this).val().length;
        //         if(numChars === 2 || numChars === 5){
        //             var thisVal = $(this).val();
        //             thisVal += '/';
        //             $(this).val(thisVal);
        //         }
        //     }
        // });

        // $(document.body).on('input','.IsDateInput', function() {
        //     var currentVal = $(this).val();
        //     var formattedVal = currentVal.replace(/\D/g, '');

        //     if (formattedVal.length > 1) {
        //     formattedVal = formattedVal.replace(/(\d{2})(\d{2})(\d{4})/, '$1/$2/$3');
        //     } else if (formattedVal.length > 3) {
        //     formattedVal = formattedVal.replace(/(\d{2})(\d{2})(\d{2})(\d{1,2})/, '$1/$2/$3$4');
        //     }

        //     $(this).val(formattedVal);
        // });
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

        $(document.body).on('click', '.BtnReviewQuestions', function () {

            $.ajax({
                url: '/customer/review_all_questions',
                method: 'GET',
                beforeSend: function () {
                    Swal.fire({
                        position: 'center',
                        title: "Please Wait...",
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                },
                success: function (e) {
                    $('.ModalListGroup').empty();
                    if (e && e.length > 0 && e[0] && e[0].AttemptedAnswers) {
                    $.each(e[0].AttemptedAnswers, function (i, option) {
                        var QuestionID = option.QuestionID;
                        var QuestionTitle = option.QuestionTitle;
                        var ParentQuestionSortID = option.ParentQuestionSortID;


                        var URLAnchor = '<i class="fas fa-edit ml-2 text-info BtnEditQuestion" ParentQuestionSortID="' + ParentQuestionSortID + '" QuestionID="' + QuestionID + '" style="cursor:pointer"></i>';


                        $('.ModalListGroup').append('<li class="list-group-item LIParentQuestion" ParentQuestionID="' + QuestionID + '"><b>' + SubAlphabets[option.ParentQuestionSortID] + '</b> - ' + QuestionTitle + ' ' + URLAnchor + '</li>');
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
                                                                                    + '<table class="custom_table">'

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
                    } else {
                        $('.ModalListGroup').append('<li class="list-group-item">No attempted answers found.</li>');
                    }
                    Swal.close();
                    $('#ReviewAlQuestionsModal').modal('show');
                }
            });
        });


        $(document.body).on('click', '.BtnEditQuestion', function () {
            var QuestionID = $(this).attr('QuestionID');
            var ParentQuestionSortID = $(this).attr('ParentQuestionSortID');
            IsEdit = true;
            BackQuestion(ParentQuestionSortID, false, QuestionID, 'true');

            // BackQuestion(ParentSortID, NoMoreQuestions, ParentQuestionID, IsRelatedQuestion);


            $('#ReviewAlQuestionsModal').modal('hide');

        });

        $(document.body).on('click', '.BtnUpdate', function () {
            $('.TextAreaValue').removeClass('border border-danger border-3');
            var isGoodToGo = true;
            var QuestionOptionTypeID = $('.QNAMainDiv').attr('QuestionOptionTypeID');
            var IsRequired = $('.QNAMainDiv').attr('IsRequired');



            if (QuestionOptionTypeID == 1) {
                if (IsRequired == "1") {
                    if ($('.InputRadioOption:checked').length == 0) {
                        isGoodToGo = false;
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: "Please Select An Option...",
                            showConfirmButton: false,
                            timer: 1000
                        });
                    }
                }
            }

            if (QuestionOptionTypeID == 2 || QuestionOptionTypeID == 4 || QuestionOptionTypeID == 5) {
                if (IsRequired == "1") {
                    if ($('.TextAreaValue').length > 0) {
                        if ($('.TextAreaValue').val().trim() == "") {
                            isGoodToGo = false;
                            $('.TextAreaValue').addClass('border border-danger border-3');
                        }
                    } else {
                        if ($('.CurrentSavedAddress').length > 0) {
                            $('#AppendQuestionsHere').append('<input type="hidden" value="' + $('.CurrentSavedAddress').text() + '" class="TextAreaValue">');
                        } else {
                            isGoodToGo = false;
                        }
                    }
                }
            }

            var IncrementDecrementArray = [];

            if (QuestionOptionTypeID == 6) {
                if ($('.IncreDecreInput').length > 0) {
                    $('.IncreDecreInput').each(function () {
                        if (parseInt($(this).val()) > 0) {
                            var InputNumberOf = $(this).attr('InputNumberOf');
                            var Index = IncrementDecrementArray.push({ For: InputNumberOf, Total: $(this).val(), Data: [] }) - 1;

                            if ($('.DivIncreDecreDataFor').length > 0) {
                                var IncreDecreGetData = $('.DivIncreDecreDataFor').attr('IncreDecreGetData');
                                if (IncreDecreGetData != "") {

                                    var IncreDecreSplittedData = IncreDecreGetData.split(';');

                                    if (IncreDecreSplittedData.length > 0) {

                                        if ($('.DivIncreDecreDataForChild[IncreDecreDataFor="' + InputNumberOf + '"]').length > 0) {

                                            $('.DivIncreDecreDataForChild[IncreDecreDataFor="' + InputNumberOf + '"]').find('tbody.TBodyIncreDecreDataFor').find('tr').each(function (TRIndex) {
                                                var TRThis = $(this);
                                                var TRArrIndex = IncrementDecrementArray[Index].Data.push({ TDValues: [] }) - 1;
                                                $.each(IncreDecreSplittedData, function (i, option) {
                                                    var Value = TRThis.find('td[IncreDecreGetDataItem="' + option + '"]').find('.InputGetIncreDecreData').val();
                                                    if(InputNumberOf=="Wife"){
                                                            if(option.trim()=="Civil Marriage"){
                                                                console.log('iss civill marriage');
                                                                if(TRThis.find('td[IncreDecreGetDataItem="' + option + '"]').find('.InputGetIncreDecreData').is(':checked')){
                                                                    Value = 'Is Civil Marriage';
                                                                }else{
                                                                    Value = 'Is Not Civil Married';
                                                                }
                                                            }
                                                        }

                                                        if(InputNumberOf=="Property"){
                                                            if(option.trim()=="Property Type"){
                                                                Value = TRThis.find('td[IncreDecreGetDataItem="' + option + '"]').find('.InputGetIncreDecreData:checked').val();
                                                            }
                                                        }
                                                    IncrementDecrementArray[Index].Data[TRArrIndex].TDValues.push({ name: option, value: Value });
                                                });
                                            });

                                        }
                                    }

                                }
                            }

                        }
                    });
                }

            }

            if (isGoodToGo) {

                var ParentTextValue = 'NULL';
                var RelatedTextValue = 'NULL';
                var ParentSortID = $('.QNAMainDiv').attr('ParentQuestionSortID');
                var QuestionID = $('.QNAMainDiv').attr('ParentQuestionID');
                var RelatedSortID = 'NULL';
                var RelatedQuestionID = 'NULL';
                var ParentOptionID = 'NULL';
                var ConcatRQNA = 'NULL';

                var IsRelatedQuestion = $('.QNAMainDiv').attr('IsRelatedQuestion');


                var SelectedOptionID = $('.InputRadioOption:checked').length > 0 ? $('.InputRadioOption:checked').val() : '';
                var ConcatQNA = 'NULL';
                if (IsRelatedQuestion == "false") {
                    if (QuestionOptionTypeID == 1) {
                        // ConcatQNA = QuestionID + '_' + SelectedOptionID;
                        ConcatQNA = { ParentQuestionID: QuestionID, SelectedOptionID: SelectedOptionID };
                    }
                    if (QuestionOptionTypeID == 2 || QuestionOptionTypeID == 4 || QuestionOptionTypeID == 5) {
                        ParentTextValue = $('.TextAreaValue').length > 0 ? $('.TextAreaValue').val() : '';
                        ConcatQNA = { ParentQuestionID: QuestionID, ParentTextValue: ParentTextValue };
                    }
                } else {
                    RelatedSortID = $('.QNAMainDiv').attr('RelatedSortID');
                    RelatedQuestionID = $('.QNAMainDiv').attr('RelatedQuestionID');
                    ParentOptionID = $('.QNAMainDiv').attr('ParentOptionID');
                    // ConcatRQNA = QuestionID + '_' + ParentOptionID + '_' + RelatedQuestionID + '_' + SelectedOptionID;

                    if (QuestionOptionTypeID == 1) {
                        ConcatRQNA = { ParentQuestionID: QuestionID, ParentOptionID: ParentOptionID, RelatedQuestionID: RelatedQuestionID, SelectedOptionID: SelectedOptionID };
                    }
                    if (QuestionOptionTypeID == 2 || QuestionOptionTypeID == 4 || QuestionOptionTypeID == 5) {
                        // ConcatQNA = {QuestionID:QuestionID,Value:RelatedTextValue};
                        RelatedTextValue = $('.TextAreaValue').val();
                        ConcatRQNA = { ParentQuestionID: QuestionID, ParentOptionID: ParentOptionID, RelatedQuestionID: RelatedQuestionID, RelatedTextValue: RelatedTextValue };
                    }
                    if (QuestionOptionTypeID == 6) {
                        // RelatedTextValue = $('.TextAreaValue').val();
                        RelatedTextValue = IncrementDecrementArray.length > 0 ? JSON.stringify(IncrementDecrementArray) : '';
                        ConcatRQNA = { ParentQuestionID: QuestionID, ParentOptionID: ParentOptionID, RelatedQuestionID: RelatedQuestionID, RelatedTextValue: RelatedTextValue };
                    }

                }
                UpdateQuestion(ParentSortID, ConcatQNA, RelatedSortID, ConcatRQNA, QuestionOptionTypeID);
            } else {
                if ($('.TextAreaValue:not(.IsDateInput)').length > 0) {
                    $('.TextAreaValue:not(.IsDateInput)').focus();
                }
            }

        });


        // NextQuestion(-1);
        ResumeQuestion();
        ResetFields();

        $(document.body).on('change', '.InputRadioOption', function () {
            var BtnNextUpdateClass = $('.BtnNextUpdate').attr('BtnNextUpdate');
            // setTimeout(function () {
            $('.' + BtnNextUpdateClass).trigger('click');
            // }, 300);
        });

        $(document.body).on('click', '.BtnNext', function () {
            $('.TextAreaValue').removeClass('border border-danger border-3');
            var isGoodToGo = true;
            var QuestionOptionTypeID = $('.QNAMainDiv').attr('QuestionOptionTypeID');
            var IsRequired = $('.QNAMainDiv').attr('IsRequired');



            if (QuestionOptionTypeID == 1) {
                if (IsRequired == "1") {
                    if ($('.InputRadioOption:checked').length == 0) {
                        isGoodToGo = false;
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: "Please Select An Option...",
                            showConfirmButton: false,
                            timer: 1000
                        });
                    }
                }
            }

            if (QuestionOptionTypeID == 2 || QuestionOptionTypeID == 4 || QuestionOptionTypeID == 5) {
                if (IsRequired == "1") {
                    if ($('.TextAreaValue').length > 0) {
                        if ($('.TextAreaValue').val().trim() == "") {
                            isGoodToGo = false;
                            $('.TextAreaValue').addClass('border border-danger border-3');
                            Swal.fire({
                                position: 'center',
                                icon: 'error',
                                title: "Please fill the required field",
                                showConfirmButton: false,
                                timer: 1000
                            });
                        }
                    } else {
                        if ($('.CurrentSavedAddress').length > 0) {
                            $('#AppendQuestionsHere').append('<input type="hidden" value="' + $('.CurrentSavedAddress').text() + '" class="TextAreaValue">');
                        } else {
                            isGoodToGo = false;
                            Swal.fire({
                                position: 'center',
                                icon: 'error',
                                title: "Please enter your address",
                                showConfirmButton: false,
                                timer: 1000
                            });
                        }
                    }
                }
            }

            var IncrementDecrementArray = [];

            if (QuestionOptionTypeID == 6) {
                if ($('.IncreDecreInput').length > 0) {
                    $('.IncreDecreInput').each(function () {
                        if (parseInt($(this).val()) > 0) {
                            var InputNumberOf = $(this).attr('InputNumberOf');
                            var Index = IncrementDecrementArray.push({ For: InputNumberOf, Total: $(this).val(), Data: [] }) - 1;

                            if ($('.DivIncreDecreDataFor').length > 0) {
                                var IncreDecreGetData = $('.DivIncreDecreDataFor').attr('IncreDecreGetData');
                                if (IncreDecreGetData != "") {

                                    var IncreDecreSplittedData = IncreDecreGetData.split(';');

                                    if (IncreDecreSplittedData.length > 0) {
                                        if ($('.DivIncreDecreDataForChild[IncreDecreDataFor="' + InputNumberOf + '"]').length > 0) {
                                                $('.DivIncreDecreDataForChild[IncreDecreDataFor="' + InputNumberOf + '"]').find('tbody.TBodyIncreDecreDataFor').find('tr').each(function (TRIndex) {
                                                    var TRThis = $(this);
                                                    var TRArrIndex = IncrementDecrementArray[Index].Data.push({ TDValues: [] }) - 1;
                                                    $.each(IncreDecreSplittedData, function (i, option) {
                                                        var Value = TRThis.find('td[IncreDecreGetDataItem="' + option + '"]').find('.InputGetIncreDecreData').val();
                                                        if(InputNumberOf=="Wife"){
                                                            if(option.trim()=="Civil Marriage"){
                                                                if(TRThis.find('td[IncreDecreGetDataItem="' + option + '"]').find('.InputGetIncreDecreData').is(':checked')){
                                                                    Value = 'Is Civil Marriage';
                                                                }else{
                                                                    Value = 'Is Not Civil Married';
                                                                }
                                                            }
                                                        }

                                                        if(InputNumberOf=="Property"){
                                                            if(option.trim()=="Property Type"){
                                                                Value = TRThis.find('td[IncreDecreGetDataItem="' + option + '"]').find('.InputGetIncreDecreData:checked').val();
                                                            }
                                                        }

                                                        IncrementDecrementArray[Index].Data[TRArrIndex].TDValues.push({ name: option, value: Value });
                                                    });
                                                });
                                        }
                                    }

                                }
                            }

                        }
                    });
                }

            }




            if (isGoodToGo) {

                var ParentTextValue = 'NULL';
                var RelatedTextValue = 'NULL';
                var ParentSortID = $('.QNAMainDiv').attr('ParentQuestionSortID');
                var QuestionID = $('.QNAMainDiv').attr('ParentQuestionID');
                var RelatedSortID = 'NULL';
                var RelatedQuestionID = 'NULL';
                var ParentOptionID = 'NULL';
                var ConcatRQNA = 'NULL';

                var IsRelatedQuestion = $('.QNAMainDiv').attr('IsRelatedQuestion');


                var SelectedOptionID = $('.InputRadioOption:checked').length > 0 ? $('.InputRadioOption:checked').val() : '';
                var ConcatQNA = 'NULL';
                if (IsRelatedQuestion == "false") {
                    if (QuestionOptionTypeID == 1) {
                        // ConcatQNA = QuestionID + '_' + SelectedOptionID;
                        ConcatQNA = { ParentQuestionID: QuestionID, SelectedOptionID: SelectedOptionID };
                    }
                    if (QuestionOptionTypeID == 2 || QuestionOptionTypeID == 4 || QuestionOptionTypeID == 5) {
                        ParentTextValue = $('.TextAreaValue').length > 0 ? $('.TextAreaValue').val() : '';
                        ConcatQNA = { ParentQuestionID: QuestionID, ParentTextValue: ParentTextValue };
                    }
                } else {
                    RelatedSortID = $('.QNAMainDiv').attr('RelatedSortID');
                    RelatedQuestionID = $('.QNAMainDiv').attr('RelatedQuestionID');
                    ParentOptionID = $('.QNAMainDiv').attr('ParentOptionID');
                    // ConcatRQNA = QuestionID + '_' + ParentOptionID + '_' + RelatedQuestionID + '_' + SelectedOptionID;

                    if (QuestionOptionTypeID == 1) {
                        ConcatRQNA = { ParentQuestionID: QuestionID, ParentOptionID: ParentOptionID, RelatedQuestionID: RelatedQuestionID, SelectedOptionID: SelectedOptionID };
                    }
                    if (QuestionOptionTypeID == 2 || QuestionOptionTypeID == 4 || QuestionOptionTypeID == 5) {
                        // ConcatQNA = {QuestionID:QuestionID,Value:RelatedTextValue};
                        RelatedTextValue = $('.TextAreaValue').val();
                        ConcatRQNA = { ParentQuestionID: QuestionID, ParentOptionID: ParentOptionID, RelatedQuestionID: RelatedQuestionID, RelatedTextValue: RelatedTextValue };
                    }
                    if (QuestionOptionTypeID == 6) {
                        // RelatedTextValue = $('.TextAreaValue').val();
                        RelatedTextValue = IncrementDecrementArray.length > 0 ? JSON.stringify(IncrementDecrementArray) : '';
                        ConcatRQNA = { ParentQuestionID: QuestionID, ParentOptionID: ParentOptionID, RelatedQuestionID: RelatedQuestionID, RelatedTextValue: RelatedTextValue };
                    }

                }
                NextQuestion(ParentSortID, ConcatQNA, RelatedSortID, ConcatRQNA, QuestionOptionTypeID);
            } else {
                if ($('.TextAreaValue:not(.IsDateInput)').length > 0) {
                    $('.TextAreaValue:not(.IsDateInput)').focus();
                }
            }

        });

        $('.BtnBack').on('click', function () {
            var NoMoreQuestions = true;
            var ParentSortID = 'NULL';
            var IsRelatedQuestion = 'NULL';
            var ParentQuestionID = 'NULL';
            if ($('.NoMoreQuestions').length == 0) {
                NoMoreQuestions = false;
                ParentSortID = $('.QNAMainDiv').attr('ParentQuestionSortID');
                IsRelatedQuestion = $('.QNAMainDiv').attr('IsRelatedQuestion');
                ParentQuestionID = $('.QNAMainDiv').attr('ParentQuestionID')
            }
            if (((IsRelatedQuestion == 'false' && ParentSortID > 0) || IsRelatedQuestion == 'true') || NoMoreQuestions == true) {
                BackQuestion(ParentSortID, NoMoreQuestions, ParentQuestionID, IsRelatedQuestion);
            }

        });

        $(document.body).on('keyup', '.InputPostalCode', function (e) {
            if (e.which == 13) {
                $('.BtnSearchAddress').trigger('click');
            }
        });

        $(document.body).on('keyup', 'input.TextAreaValue, textarea.TextAreaValue', function (e) {
            if (e.which == 13) {
                $('.BtnNextUpdate:not(.custom_pointer_none)').trigger('click');
            }
        })

        $(document.body).on('click', '.BtnSearchAddress', function () {
            if ($('.InputPostalCode').val().trim().length > 0) {
                var InputValue = $('.InputPostalCode').val().trim();
                $('.InputPostalCode').val(InputValue);
                if ($('select[ForValue="' + InputValue + '"]').length == 0) {
                    $('#AppendAddressSelectHere').empty();
                    $('.BtnSearchAddress').prop('disabled', true);

                    $.ajax({
                        url: 'https://api.ideal-postcodes.co.uk/v1/autocomplete/addresses?api_key=ak_ldd2b2rxD5QMOVjzXyuhV7p6ekzDM&q=' + InputValue,
                        method: 'GET',
                        beforeSend: function () {
                            Swal.fire({
                                position: 'center',
                                icon: 'info',
                                title: "Please Wait...",
                                showConfirmButton: false,
                                allowOutsideClick: false
                            });
                        },
                        success: function (e) {
                            if (e.code == 2000) {
                                if (e.result.hits.length > 0) {
                                    var Select = '<select ForValue="' + InputValue + '" class="form-select TextAreaValue mt-2 custom_select">';
                                    $.each(e.result.hits, function (i, option) {
                                        Select += '<option value="' + option.suggestion + '">' + option.suggestion + '</option>';
                                    });
                                    Select += '</select>';
                                    $('#AppendAddressSelectHere').html(Select);
                                    Swal.close();
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "No addresses found in the provided postal code",
                                    });
                                }
                            }
                            $('.BtnSearchAddress').prop('disabled', false);

                        }
                    });

                }


            } else {
                console.log('please enter something');
            }
        });

    });
    var Alphabets = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    Alphabets = Alphabets.split('');
    function NextQuestion(ParentSortID, SelectedOptionID = 'NULL', RelatedSortID = 'NULL', RelatedSelectedOptionID = 'NULL', QuestionOptionTypeID = 'NULL') {
        $('.BtnNext, .BtnBack').addClass('custom_pointer_none');
        var DataToSend = new Object();
        DataToSend._token = $('meta[name="csrf-token"]').attr('content');
        DataToSend.SortID = ParentSortID;
        DataToSend.QuestionOptionID = SelectedOptionID;
        DataToSend.RelatedSortID = RelatedSortID;
        DataToSend.RelatedQuestionOptionID = RelatedSelectedOptionID;
        DataToSend.QuestionOptionTypeID = QuestionOptionTypeID;
        console.log(DataToSend);
        // return false;
        $.ajax({
            url: '/next_question',
            method: 'POST',
            data: DataToSend,
            beforeSend: function () {
                $('#AppendQuestionsHere').append('<div class="text-center refreshing_icon_div"><i class="fas fa-sync fa-spin refreshing_icon"></i></div>');
            },
            success: AppendingHtml
        });
    }

    function UpdateQuestion(ParentSortID, SelectedOptionID = 'NULL', RelatedSortID = 'NULL', RelatedSelectedOptionID = 'NULL', QuestionOptionTypeID = 'NULL') {
        $('.BtnNext, .BtnBack').addClass('custom_pointer_none');
        var DataToSend = new Object();
        DataToSend._token = $('meta[name="csrf-token"]').attr('content');
        DataToSend.SortID = ParentSortID;
        DataToSend.QuestionOptionID = SelectedOptionID;
        DataToSend.RelatedSortID = RelatedSortID;
        DataToSend.RelatedQuestionOptionID = RelatedSelectedOptionID;
        DataToSend.QuestionOptionTypeID = QuestionOptionTypeID;

        // return false;
        $.ajax({
            url: '/update_question',
            method: 'POST',
            data: DataToSend,
            beforeSend: function () {
                $('#AppendQuestionsHere').append('<div class="text-center refreshing_icon_div"><i class="fas fa-sync fa-spin refreshing_icon"></i></div>');
            },
            success: function (e) {
                if (e.has_data) {
                    if (e.related_data != "") {
                        $('.BtnNext, .BtnUpdate').remove();
AppendingHtml(e);

                    } else {
                        if(e.Question.id==6){
                            $('.BtnNext, .BtnUpdate').remove();
                            AppendingHtml(e);
}else{
    DoYouWantToSubmit();
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Answer Updated Successfully',
                            showConfirmButton: true,
                        });
                        IsEdit = false;
                        $('.BtnBack').removeClass('custom_pointer_none');
                        $('.BtnBack').addClass('custom_pointer_all');
                        $('.BtnNext, .BtnUpdate').remove();
}

                    }

                } else {
                    DoYouWantToSubmit();
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'Answer Updated Successfully',
                        showConfirmButton: true,
                    });
                    IsEdit = false;
                    $('.BtnBack').removeClass('custom_pointer_none');
                    $('.BtnBack').addClass('custom_pointer_all');
                    $('.BtnNext, .BtnUpdate').remove();
                }

            }
        });
    }

    function BackQuestion(SortID, NoMoreQuestions, ParentQuestionID, IsRelatedQuestion) {
        $('.BtnNext, .BtnBack').addClass('custom_pointer_none');
        $.ajax({
            url: '/back_question/' + SortID + '/' + NoMoreQuestions + '/' + ParentQuestionID + '/' + IsRelatedQuestion,
            method: 'GET',
            beforeSend: function () {
                $('#AppendQuestionsHere').append('<div class="text-center refreshing_icon_div"><i class="fas fa-sync fa-spin refreshing_icon"></i></div>');
            },
            success: AppendingHtml

        });
    }

    function ResumeQuestion() {
        $('.BtnNext, .BtnBack').addClass('custom_pointer_none');
        $.ajax({
            url: '/resume_question',
            method: 'GET',
            beforeSend: function () {
                $('#AppendQuestionsHere').append('<div class="text-center refreshing_icon_div"><i class="fas fa-sync fa-spin refreshing_icon"></i></div>');
            },
            success: AppendingHtml
        });
    }



    function AppendingHtml(e) {
        // $('.q_section_div_child').addClass('w-100');
        $.each(e.q_section_progress, function (i, option) {
            var SectionID = option.SectionID;
            if (option.TotalPercentage == "100") {
                $('.QSectionDiv[QSectionID="' + SectionID + '"] .QSectionDivChild').addClass('w-100');
            } else {
                $('.QSectionDiv[QSectionID="' + SectionID + '"] .SectionPercentage').text(option.TotalPercentage + '%');
                $('.QSectionDiv[QSectionID="' + SectionID + '"] .QSectionDivChild').css('width', option.TotalPercentage + '%');
            }
        });
        $('.BtnNext').remove();
        if (e.status) {
            if (e.is_locked == false) {
                if (e.has_data) {
                    var BtnNextText = "Next";
                    if (e.is_related_question == false) {
                        if (e.Question.is_required == 0) {
                            BtnNextText = "Next / Skip";
                        }
                    } else {
                        if (e.related_data.related_question.RelatedQuestionIsRequired == 0) {
                            BtnNextText = "Next / Skip";
                        }
                    }

                    var NextClass = 'BtnNext';
                    if (IsEdit) {
                        NextClass = 'BtnUpdate';
                    }

                    $('#AppendNextBackBtnsHere').append('<button class="btn btn-sm custom_btn_primary btn_next ' + NextClass + ' custom_pointer_all BtnNextUpdate" BtnNextUpdate="' + NextClass + '">'
                        + '<span class="position-relative me-4">'
                        + '<span>' + BtnNextText + '</span>'
                        + '<i class="fa-regular fa-arrow-right right_arrow_icon arrow_icon"></i>'
                        + '</span>'
                        + '</button>');
                    if (e.is_related_question == false) {
                        var TooltipInfo = '';
                        if(e.Question.tooltip_info){
                            TooltipInfo = '<i class="fa-solid fa-circle-exclamation tooltip_info ms-2" data-bs-html="true" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="'+e.Question.tooltip_info+'"></i>';
                        }
                        var IsRequiredText = e.Question.is_required == 1 ? '<span class="text-danger ms-2">*</span>' : '';
                        $('.QSectionDiv').removeClass('active_q_section');
                        $('.QSectionDiv[QSectionID="' + e.Question.questionnaire_section_id + '"]').addClass('active_q_section');
                        var HTML = '<div class="QNAMainDiv qna_main_div" IsRequired="' + e.Question.is_required + '" IsRelatedQuestion="' + e.is_related_question + '" ParentQuestionID="' + e.Question.id + '" ParentQuestionSortID="' + e.Question.sort_id + '" QuestionOptionTypeID="' + e.Question.question_option_type_id + '">'
                            + '<p class="question_heading d-flex align-items-center mb-4">'
                            + '<span class="question_alphabet">' + SubAlphabets[e.Question.sort_id] + '</span><span class="question_heading">' + e.Question.title + IsRequiredText + '</span>'

                            +TooltipInfo

                            +'</p>'

                        if (e.Question.question_option_type_id == 1) {

                            + '<div class="options_main_div">'



                            if (e.Options.length > 0) {
                                var AlreadySelectedOptionID = '';
                                if (e.AttemptedQuestion != "") {
                                    AlreadySelectedOptionID = e.AttemptedQuestion.option_id;
                                }
                                $.each(e.Options, function (i, option) {

                                    var Checked = '';
                                    if (AlreadySelectedOptionID == option.id) {
                                        Checked = 'checked';
                                    }

                                    HTML += '<div class="position-relative">'
                                        + '<input type="radio" id="Option_' + option.id + '" ' + Checked + ' class="input_radio_option InputRadioOption" value="' + option.id + '" name="option[' + e.Question.id + ']">'
                                        + '<label for="Option_' + option.id + '" class="d-block mb-2 option_label">'
                                        + '<div class="option_label_child_div">'
                                        + '<div class="option_alphabet"><div class="custom_radio"></div></div><span class="option_text">' + option.title + '</span>'
                                        + '</div>'
                                        + '</label>'
                                        + '</div>';
                                });
                            }

                            HTML += '</div>'//options_main_div
                        }
                        else if (e.Question.question_option_type_id == 2) {
                            var AlreadyEnteredValue = "";
                            if (e.AttemptedQuestion != "" && e.AttemptedQuestion != null) {
                                if (e.AttemptedQuestion.text_value != "" && e.AttemptedQuestion.text_value != null) {
                                    AlreadyEnteredValue = e.AttemptedQuestion.text_value;
                                }
                            }
                            if (e.Question.is_address == 0) {

                                HTML += '<textarea rows="5" class="TextAreaValue custom_textarea" placeholder="Type Your Answer Here">' + AlreadyEnteredValue + '</textarea>';
                            } else if (e.Question.is_address == 1) {
                                var CurrentlySavedAddress = '';
                                if (AlreadyEnteredValue != "") {
                                    CurrentlySavedAddress = '<p class="mb-3">Currently Saved Address: <span class="fw-bold CurrentSavedAddress">' + AlreadyEnteredValue + '</span></p>';
                                }
                                HTML += CurrentlySavedAddress + '<div class="input-group"><input type="text" placeholder="Enter Postal Code" class="InputPostalCode custom_input form-control"><button class="btn btn custom_btn_primary BtnSearchAddress"><i class="fas fa-search me-2"></i>Search Address</button></div><div id="AppendAddressSelectHere"></div>';
                            }
                        } else if (e.Question.question_option_type_id == 4) {
                            var AlreadyEnteredValue = "";
                            if (e.AttemptedQuestion != "" && e.AttemptedQuestion != null) {
                                if (e.AttemptedQuestion.text_value != "" && e.AttemptedQuestion.text_value != null) {
                                    AlreadyEnteredValue = e.AttemptedQuestion.text_value;
                                }
                            }
                            HTML += '<input type="text" class="TextAreaValue custom_input" placeholder="Type Your Answer Here" value="' + AlreadyEnteredValue + '">';

                        } else if (e.Question.question_option_type_id == 5) {
                            var AlreadyEnteredValue = "";
                            if (e.AttemptedQuestion != "" && e.AttemptedQuestion != null) {
                                if (e.AttemptedQuestion.text_value != "" && e.AttemptedQuestion.text_value != null) {
                                    AlreadyEnteredValue = e.AttemptedQuestion.text_value;
                                }
                            }
                            HTML += '<input type="text" class="IsDateInput TextAreaValue form-control custom_input" placeholder="Type Your Answer Here" value="' + AlreadyEnteredValue + '" OriginalValue="' + AlreadyEnteredValue + '">';

                        }
                        + '</div>';//QNAMainDiv

                        $('#AppendQuestionsHere').html(HTML);

                    } else {

                        var HTML = '';

                        var TooltipInfo = '';
                        if(e.related_data.related_question.TooltipInfo){
                            TooltipInfo = '<i class="fa-solid fa-circle-exclamation tooltip_info ms-2" data-bs-html="true" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="'+e.related_data.related_question.TooltipInfo+'"></i>';
                        }
                        var IsRequiredText = e.related_data.related_question.RelatedQuestionIsRequired == 1 ? '<span class="text-danger ms-2">*</span>' : '';
                        if (e.related_data.related_question.RelatedQuestionOptionTypeID == 1 || e.related_data.related_question.RelatedQuestionOptionTypeID == 2 || e.related_data.related_question.RelatedQuestionOptionTypeID == 4 || e.related_data.related_question.RelatedQuestionOptionTypeID == 5) {



                            HTML = '<div class="QNAMainDiv qna_main_div" ParentOptionID="' + e.related_data.related_question.ParentOptionID + '" IsRequired="' + e.related_data.related_question.RelatedQuestionIsRequired + '" IsRelatedQuestion="' + e.is_related_question + '" ParentQuestionID="' + e.related_data.related_question.ParentQuestionID + '" ParentQuestionSortID="' + e.related_data.related_question.ParentSortID + '" QuestionOptionTypeID="' + e.related_data.related_question.RelatedQuestionOptionTypeID + '" RelatedQuestionID="' + e.related_data.related_question.RelatedQuestionID + '" RelatedSortID="' + e.related_data.related_question.RelatedSortID + '">'
                                + '<p class="question_heading d-flex align-items-center mb-4">'
                                + '<span class="question_alphabet">' + SubAlphabets[e.related_data.related_question.ParentSortID] + (parseInt(e.related_data.related_question.RelatedSortID) + 1) + '</span><span class="question_heading">' + e.related_data.related_question.RelatedQuestionTitle + IsRequiredText + '</span>'
                                + TooltipInfo
                                +'</p>';
                            if (e.related_data.related_question.RelatedQuestionOptionTypeID == 1) {

                                + '<div class="options_main_div">';

                                var AlreadySelectedRelatedOptionID = '';
                                if (e.AttemptedRelatedQuestion != "" && e.AttemptedRelatedQuestion != null) {
                                    AlreadySelectedRelatedOptionID = e.AttemptedRelatedQuestion.related_option_id;
                                }

                                $.each(e.related_data.related_options, function (i, option) {

                                    var Checked = '';
                                    if (AlreadySelectedRelatedOptionID == option.id) {
                                        Checked = 'checked';
                                    }

                                    HTML += '<div class="position-relative">'
                                        + '<input type="radio" id="Option_' + option.id + '" ' + Checked + ' class="input_radio_option InputRadioOption" value="' + option.id + '" name="option[' + option.related_question_id + ']">'
                                        + '<label for="Option_' + option.id + '" class="d-block mb-2 option_label">'
                                        + '<div class="option_label_child_div">'
                                        + '<div class="option_alphabet"><div class="custom_radio"></div></div><span class="option_text">' + option.title + '</span>'
                                        + '</div>'
                                        + '</label>'
                                        + '</div>';

                                });

                                HTML += '</div>'//options_main_div
                            }
                            else if (e.related_data.related_question.RelatedQuestionOptionTypeID == 2) {
                                var AlreadyEnteredValue = "";
                                if (e.AttemptedRelatedQuestion != "" && e.AttemptedRelatedQuestion != null) {
                                    if (e.AttemptedRelatedQuestion.related_text_value != "" && e.AttemptedRelatedQuestion.related_text_value != null) {
                                        AlreadyEnteredValue = e.AttemptedRelatedQuestion.related_text_value;
                                    }
                                }
                                HTML += '<textarea rows="5" class="TextAreaValue custom_textarea" placeholder="Type Your Answer Here">' + AlreadyEnteredValue + '</textarea>';
                            } else if (e.related_data.related_question.RelatedQuestionOptionTypeID == 4) {
                                var AlreadyEnteredValue = "";
                                if (e.AttemptedRelatedQuestion != "" && e.AttemptedRelatedQuestion != null) {
                                    if (e.AttemptedRelatedQuestion.related_text_value != "" && e.AttemptedRelatedQuestion.related_text_value != null) {
                                        AlreadyEnteredValue = e.AttemptedRelatedQuestion.related_text_value;
                                    }
                                }
                                //HTML += '<textarea rows="5" class="TextAreaValue" placeholder="Type Your Answer Here">' + AlreadyEnteredValue + '</textarea>';

                                HTML += '<input type="text" class="TextAreaValue custom_input" value="' + AlreadyEnteredValue + '">'

                            } else if (e.related_data.related_question.RelatedQuestionOptionTypeID == 5) {
                                var AlreadyEnteredValue = "";
                                if (e.AttemptedRelatedQuestion != "" && e.AttemptedRelatedQuestion != null) {
                                    if (e.AttemptedRelatedQuestion.related_text_value != "" && e.AttemptedRelatedQuestion.related_text_value != null) {
                                        AlreadyEnteredValue = e.AttemptedRelatedQuestion.related_text_value;
                                    }
                                }
                                //HTML += '<textarea rows="5" class="TextAreaValue" placeholder="Type Your Answer Here">' + AlreadyEnteredValue + '</textarea>';

                                HTML += '<input type="text" class="IsDateInput TextAreaValue form-control custom_input" value="' + AlreadyEnteredValue + '" OriginalValue="' + AlreadyEnteredValue + '">'

                            }

                        } else if (e.related_data.related_question.RelatedQuestionOptionTypeID == 3) {

                            HTML = '<div class="QNAMainDiv qna_main_div" ParentOptionID="' + e.related_data.related_question.ParentOptionID + '" IsRequired="' + e.related_data.related_question.RelatedQuestionIsRequired + '" IsRelatedQuestion="' + e.is_related_question + '" ParentQuestionID="' + e.related_data.related_question.ParentQuestionID + '" ParentQuestionSortID="' + e.related_data.related_question.ParentSortID + '" QuestionOptionTypeID="' + e.related_data.related_question.RelatedQuestionOptionTypeID + '" RelatedQuestionID="' + e.related_data.related_question.RelatedQuestionID + '" RelatedSortID="' + e.related_data.related_question.RelatedSortID + '">'
                                // + '<p class="question_heading d-flex align-items-center mb-0">'
                                // + e.related_data.related_question.RelatedQuestionTitle +IsRequiredText+ '</p>';

                                + '<p class="question_heading d-flex align-items-center mb-4">'
                                + '<span class="question_alphabet">' + SubAlphabets[e.related_data.related_question.ParentSortID] + (parseInt(e.related_data.related_question.RelatedSortID) + 1) + '</span><span class="question_heading">' + e.related_data.related_question.RelatedQuestionTitle + IsRequiredText + '</span>'
                                + TooltipInfo
                                +'</p>';

                            $('.BtnNext').remove();

                        } else if (e.related_data.related_question.RelatedQuestionOptionTypeID == 6) {

                            if (e.related_data.related_question.IncreDecreDataFor != "" || e.related_data.related_question.IncreDecreGetData != "") {

                                var AlreadyEnteredValue = [];

                                if (e.AttemptedRelatedQuestion != "" && e.AttemptedRelatedQuestion != null) {
                                    if (e.AttemptedRelatedQuestion.related_text_value != "" && e.AttemptedRelatedQuestion.related_text_value != null) {
                                        AlreadyEnteredValue = JSON.parse(e.AttemptedRelatedQuestion.related_text_value);
                                    }
                                }


                                HTML = '<div class="QNAMainDiv qna_main_div" ParentOptionID="' + e.related_data.related_question.ParentOptionID + '" IsRelatedQuestion="' + e.is_related_question + '" ParentQuestionID="' + e.related_data.related_question.ParentQuestionID + '" ParentQuestionSortID="' + e.related_data.related_question.ParentSortID + '" QuestionOptionTypeID="' + e.related_data.related_question.RelatedQuestionOptionTypeID + '" RelatedQuestionID="' + e.related_data.related_question.RelatedQuestionID + '" RelatedSortID="' + e.related_data.related_question.RelatedSortID + '" IsRequired="' + e.related_data.related_question.RelatedQuestionIsRequired + '">';


                                var IncreDecreGetData = "";

                                if (e.related_data.related_question.IncreDecreGetData) {
                                    IncreDecreGetData = e.related_data.related_question.IncreDecreGetData;
                                }

                                HTML += '<p class="question_heading d-flex align-items-center mb-4">'
                                + '<span class="question_alphabet">' + SubAlphabets[e.related_data.related_question.ParentSortID] + (parseInt(e.related_data.related_question.RelatedSortID) + 1) + '</span><span class="question_heading">' + e.related_data.related_question.RelatedQuestionTitle + IsRequiredText + '</span>'
                                + TooltipInfo
                                +'</p>'

                                +'<table class="custom_table TableChildren" IncreDecreGetData="' + IncreDecreGetData + '">';


                                $.each(e.related_data.related_question.IncreDecreDataFor.split(';'), function (i, option) {
                                    var AlreadyAttemptedIndex = -1;
                                    var AlreadyAttemptedValue = "0";

                                    if (AlreadyEnteredValue.length > 0) {
                                        AlreadyAttemptedIndex = AlreadyEnteredValue.findIndex(obj => obj['For'] == option.trim());
                                        if (AlreadyAttemptedIndex >= 0) {
                                            AlreadyAttemptedValue = AlreadyEnteredValue[AlreadyAttemptedIndex].Total;
                                        }
                                    }


                                    HTML += '<tr IncreDecreDataForItem="' + option.trim() + '">'
                                        + '<td style="width:69%; padding:17px">'
                                        + '<p class="mb-0">'
                                        + '<i class="fas fa-user me-2"></i>' + option.trim() + '</p>'
                                        + '</td>'
                                        + '<td style="width:31%">'
                                        + '<div class="input-group" style="flex-wrap:nowrap">'
                                        + '<div class="input-group-prepend">'
                                        // + '<button class="btn custom_btn_primary rounded-0 BtnIncreDecre btn_incre_decre" IncreDecre="Decrement">-</button>'
                                        + '<i class="fa-regular fa-minus btn_incre_decre BtnIncreDecre btn_incre_decre" IncreDecre="Decrement"></i>'
                                        + '</div>'
                                        + '<input type="text" InputNumberOf="' + option.trim() + '" class="form-control rounded-0 IncreDecreInput custom_pointer_none text-center" style="min-width:37px;" value="' + AlreadyAttemptedValue + '">'
                                        + '<div class="input-group-append">'
                                        // + '<button class="btn custom_btn_primary rounded-0 BtnIncreDecre btn_incre_decre" IncreDecre="Increment">+</button>'
                                        + '<i class="fa-regular fa-plus btn_incre_decre BtnIncreDecre btn_incre_decre" IncreDecre="Increment"></i>'
                                        + '</div>'
                                        + '</div>'
                                        + '</td>'
                                        + '</tr>';
                                });

                                HTML += '</table>';

                            }

                        }
                        + '</div>';//QNAMainDiv

                        $('#AppendQuestionsHere').html(HTML);
                        AutoLoadIncreDecreTable(AlreadyEnteredValue);
                        if($('TR[incredecredataforitem="Wife"]').length>0){
                            if($('.DivIncreDecreDataFor').length==0){
                                $('.IncreDecreInput[InputNumberOf="Wife"]').val(1);
                                $('TR[incredecredataforitem="Wife"] .BtnIncreDecre').trigger('click');
                            }
                        }
                    }
                    $('.BtnNext, .BtnBack').removeClass('custom_pointer_none');
                } else {
                    DoYouWantToSubmit();
                }
            } else {
                $('#AppendQuestionsHere').html('<h1 class="text-center mb-0">Your queries are submitted</h1>');
                $('.BtnBack').remove();
                window.location.href = "{{ url('/customer/forms') }}";
            }


        } else {
            alert(e.msg);
        }

        if ($('.IsDateInput').length > 0) {
            // $(".IsDateInput").mask('39/99/9999',{

            //     translation: {
            //         '3': { pattern: /[0-3]/ }
            //     }

            // });
            $(".IsDateInput").inputmask('datetime', {
      inputFormat: 'dd/mm/yyyy', // Change this to your desired date format
      placeholder: '__/__/____' // Placeholder for the input
    });
            var OriginalValue = $('.IsDateInput').attr('OriginalValue');
            var StartDate = moment().subtract(19, 'years');
            if ($('.IsDateInput').val() != "") {
                StartDate = moment($('.IsDateInput').val(), 'DD/MM/YYYY').format('DD/MM/YYYY');
            }

            var maxDate = new Date();
            maxDate.setFullYear(maxDate.getFullYear() - 19);

            var defaultDate = new Date();
            defaultDate.setFullYear(defaultDate.getFullYear() - 19);
            console.log(defaultDate);
            $('.IsDateInput').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'DD/MM/YYYY' // Set date format to 'dd/mm/yy'
                },
                maxDate:defaultDate,
                startDate: maxDate,
            });

            $('.IsDateInput').data('daterangepicker').container.addClass('custom-daterangepicker');
            // $('.IsDateInput').datepicker({
            //     // defaultDate:defaultDate,
            //     maxDate: maxDate, // Set the maximum date
            //     dateFormat: 'dd/mm/yy', // Set your desired date format
            //     showButtonPanel: true, // Display button panel for easier navigation
            //     onClose: function (dateText, inst) {

            //         if (!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(dateText)) {
            //             // If invalid, set the input to the maximum date
            //             if (OriginalValue == "") {
            //                 $(this).datepicker('setDate', maxDate);
            //             } else {
            //                 $('.IsDateInput').val(OriginalValue);
            //             }
            //         } else {

            //             // Check if the entered date is valid
            //             var enteredDate = $.datepicker.parseDate('dd/mm/yy', dateText);

            //             if (isNaN(enteredDate.getTime())) {
            //                 // If invalid, set the input to the maximum date
            //                 if (OriginalValue == "") {
            //                     $(this).datepicker('setDate', defaultDate);
            //                 } else {
            //                     $('.IsDateInput').val(OriginalValue);
            //                 }
            //             } else {
            //                 if (enteredDate.getTime() > maxDate.getTime()) {
            //                     if (OriginalValue == "") {
            //                         $(this).datepicker('setDate', maxDate);
            //                     } else {
            //                         $('.IsDateInput').val(OriginalValue);
            //                     }
            //                 }

            //             }
            //         }
            //     },
            // });
            if (OriginalValue == "") {
                $(".IsDateInput").val($.datepicker.formatDate('dd/mm/yy', defaultDate));
            } else {
                $(".IsDateInput").val(OriginalValue);
            }

            // $(".IsDateInput").mask('99/99/9999');




        }

        if ($('.TextAreaValue:not(.IsDateInput)').length > 0) {
            $('.TextAreaValue:not(.IsDateInput)').focus();
        }

        if (IsEdit) {
            $('.BtnBack').removeClass('custom_pointer_all');
            $('.BtnBack').addClass('custom_pointer_none');
        }

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }

    function DoYouWantToSubmit() {
        $('#AppendQuestionsHere').html('<h1 class="text-center mb-0 NoMoreQuestions">Do you want to Submit the queries?</h1><form method="post" action="/submit_query" class="text-center mt-4">@csrf<button class="btn btn-lg custom_btn_primary" type="submit">Yes</button><button class="btn btn-lg custom_btn_primary ms-3 mt-md-0 mt-1 BtnReviewQuestions" type="button">Review / Edit All Questions</button></form>');
        $('.BtnNext').prop('disabled', true);
        $('.BtnBack').removeClass('custom_pointer_none');
        $('.QSectionDiv').removeClass('active_q_section');
    }

    function ResetFields() {
        $('.InputRadioOption').prop('checked', false);
    }
</script>

@endsection
