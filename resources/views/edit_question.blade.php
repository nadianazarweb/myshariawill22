@extends('/layouts/master')
@section('css')
<style>
    /* .ul_questions>li:first-child .btn_remove_question{
        display:none;
    }
    .ul_questions>li:not(:first-child) .div_question_input{
        display:flex;
    } */

    .ul_questions>li:first-child .question_input{
        border-radius:0.357rem !important;
    }

    .ul_questions>li:first-child .div_btn_remove{
        display:none;
    }

    .option_div:nth-child(1) .btn_remove_option, .option_div:nth-child(2) .btn_remove_option, .li_child_option:nth-child(1) .btn_child_remove_option, .li_child_option:nth-child(2) .btn_child_remove_option{
        display:none;
    }

    .option_div::after{
        content:'';
    }

    .invalid_input{
        border:2px solid var(--danger) !important;
    }

    .not_removable{
        display:none !important;
    }


</style>
@stop

@section('title')
    <title>Edit Question</title>
@stop
@section('body')
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row"></div>
        <div class="content-body">
            <form action="">
                <div>
                    <label for="questionnaire_section_id">Select Questionnaire Section:</label>
                    <select name="questionnaire_section_id" class="form-control mb-2" id="questionnaire_section_id">
                        @foreach($QSData as $key=>$item)
                        <option value="{{ $item->id }}" {{ $FinalArray['ParentQuestionnaireSectionID']==$item->id ? 'selected':'' }}>{{ $item->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="question_for_gender_id">This Question Is For:</label>
                    <select name="question_for_gender_id" class="form-control mb-2" id="question_for_gender_id">
                        @foreach($QuestionForGenders as $key=>$item)
                        <option value="{{ $item->id }}" {{ $FinalArray['ParentQuestionForGenderID']==$item->id ? 'selected':'' }}>{{ $item->title }}</option>
                        @endforeach
                    </select>
                </div>

                <ul class="list-group ULQuestions ul_questions">

                </ul>
                <button class="btn btn-success btn-sm BtnSave" type="button">Save</button>
            </form>

        </div>
    </div>
@endsection

@section('javascript')
<script src="{{url('backend/app-assets/js/core/sweetalert2.js')}}"></script>
<script>
    var json = <?= json_encode($FinalArray); ?>;
       var QuestionOptionType = <?= $questionoptiontypedata ?>;
        var QuestionTypeHTMLParent = '';
        $.each(QuestionOptionType, function(i, option){
            if(option.for_question=="Parent" || option.for_question == "Both"){
                var Selected = '';
                if(json.ParentQuestionOptionTypeID==option.id){
                    Selected = 'selected';
                }
                QuestionTypeHTMLParent += '<option value="'+option.id+'" '+Selected+'>'+option.title+'</option>';
            }
        });

        function ChildOptionTypeChanger(This, OptionIndex, RQIndex){
            if(OptionIndex!=="" && RQIndex!==""){
                    var RQObj = json.Options[OptionIndex].RelatedQuestions[RQIndex];
                    if(RQObj.RelatedQuestionOptionTypeID=="1"){
                        ChildRadio($('.ChildQuestion[RelatedQuestionID="'+RQObj.RelatedQuestionID+'"]').closest('.LIQuestion'),OptionIndex,RQIndex);
                    }
                    if(RQObj.RelatedQuestionOptionTypeID=="6"){
                        var IncreDecreGetData = "";
                        if(RQObj.RelatedIncreDecreGetData){
                            IncreDecreGetData = RQObj.RelatedIncreDecreGetData;
                        }
                        $('.ChildQuestion[RelatedQuestionID="'+RQObj.RelatedQuestionID+'"]').closest('.LIQuestion').find('input.ChildQuestion').after('<input type="text" value="'+RQObj.RelatedIncreDecreDataFor+'" class="form-control IncreDecreDataFor MainInput" placeholder="Boys; Girls">'
                    +'<input type="text" class="form-control IncreDecreGetData MainInput" value="'+IncreDecreGetData+'" placeholder="Name; Age">');
                    }

            }else{

                var OptionType = This.val();
                This.closest('.LIQuestion').find('.ULOptions').empty();
                if(OptionType == "1"){
                    ChildRadio(This.closest('.LIQuestion'),OptionIndex,RQIndex);
                }

                if(OptionType=="6"){
                    This.closest('.LIQuestion').find('input.ChildQuestion').after('<input type="text" class="form-control IncreDecreDataFor MainInput" placeholder="Boys; Girls">'
                    +'<input type="text" class="form-control IncreDecreGetData MainInput" placeholder="Name; Age">');
                }else{
                    This.closest('.LIQuestion').find('input.IncreDecreDataFor').remove();
                    This.closest('.LIQuestion').find('input.IncreDecreGetData').remove();
                }
            }
        }

        function AddRelatedQuestion(This, OptionIndex){

            if(OptionIndex != ""){

                if(json.Options[OptionIndex].RelatedQuestions.length>0){
                    $.each(json.Options[OptionIndex].RelatedQuestions, function(RQIndex, RQOption){
                        var QuestionTypeHTMLChild = '';
                        $.each(QuestionOptionType, function(i, option){
                            if(option.for_question=="Child" || option.for_question == "Both"){
                                var Selected = '';
                                if(option.id==RQOption.RelatedQuestionOptionTypeID){
                                    Selected = 'selected';
                                }
                                QuestionTypeHTMLChild += '<option value="'+option.id+'" '+Selected+'>'+option.title+'</option>';
                            }
                        });

                        var RelatedIsRequired = "";

                        if(RQOption.RelatedIsRequired=="1"){
                            RelatedIsRequired = "checked";
                        }

                        var RelatedTooltipInfo = '';
                        if(RQOption.RelatedTooltipInfo){
                            RelatedTooltipInfo = RQOption.RelatedTooltipInfo;
                        }

                        This.closest('.OptionDiv').find('.ULRelatedQuestion').append('<li class="list-unstyled LIQuestion mt-1">'
                        +'<div class="input-group">'
                        + '<div class="input-group-prepend">'
                        +'<span class="input-group-text fas fa-arrows d-flex align-items-center justify-content-center"></span>'
                        +'</div>'
                        + '<div class="input-group-prepend">'
                        +'<select class="form-control ChildOptionType ChildQuestionOptionType rounded-0" RelatedQuestionID="'+RQOption.RelatedQuestionID+'" style="pointer-events:none">'
                        +QuestionTypeHTMLChild
                        +'</select>'
                        +'</div>'
                        +'<input type="text" value="'+RQOption.RelatedQuestionTitle+'" RelatedQuestionID="'+RQOption.RelatedQuestionID+'" class="form-control question_input ChildQuestion MainInput" style="border-radius:0 !important;flex:3" placeholder="Enter Question">'
                        +'<input type="text" class="form-control TooltipInput" value="'+RelatedTooltipInfo+'" placeholder="Enter Tooltip Info">'
                        +'<div class="input_group_append"><label class="input-group-text h-100"><input type="checkbox" aria-label="Checkbox for following text input" '+RelatedIsRequired+' class="ChildQuestionIsRequired mr-1">Is Required</label></div>'
                        +'<div class="input-group-append">'
                        +'<button class="btn btn-outline-danger BtnRemoveLIQuestion not_removable" type="button">'
                        +'<i class="fas fa-trash"></i>'
                        +'</button>'
                        +'</div>'
                        +'</div>'

                        + '<ul class="ULOptions"></ul>'

                +'</li>');
                $('.ChildOptionType[RelatedQuestionID="'+RQOption.RelatedQuestionID+'"]').trigger('change');
                ChildOptionTypeChanger($('.ChildOptionType[RelatedQuestionID="'+RQOption.RelatedQuestionID+'"]'), OptionIndex,RQIndex);
                // This.closest('.OptionDiv').find('.ULRelatedQuestion').find('.LIQuestion:last-child').find('.ChildOptionType').trigger('change');

                    });
                }

            }else{

                var QuestionTypeHTMLChild = '';
                $.each(QuestionOptionType, function(i, option){
                    if(option.for_question=="Child" || option.for_question == "Both"){
                        QuestionTypeHTMLChild += '<option value="'+option.id+'">'+option.title+'</option>';
                    }
                });

                This.closest('.OptionDiv').find('.ULRelatedQuestion').append('<li class="list-unstyled LIQuestion mt-1">'
                +'<div class="input-group">'
                + '<div class="input-group-prepend">'
                +'<span class="input-group-text fas fa-arrows d-flex align-items-center justify-content-center"></span>'
                +'</div>'
                + '<div class="input-group-prepend">'
                +'<select class="form-control ChildOptionType ChildQuestionOptionType rounded-0">'
                +QuestionTypeHTMLChild
                +'</select>'
                +'</div>'
                +'<input type="text" class="form-control question_input ChildQuestion MainInput" RelatedQuestionID="" style="border-radius:0 !important" placeholder="Enter Question">'
                +'<input type="text" class="form-control TooltipInput" placeholder="Enter Tooltip Info">'
                +'<div class="input_group_append"><label class="input-group-text h-100"><input type="checkbox" aria-label="Checkbox for following text input" class="ChildQuestionIsRequired mr-1">Is Required</label></div>'
                +'<div class="input-group-append">'
                +'<button class="btn btn-outline-danger BtnRemoveLIQuestion" type="button">'
                +'<i class="fas fa-trash"></i>'
                +'</button>'
                +'</div>'
                +'</div>'

                + '<ul class="ULOptions"></ul>'

                +'</li>');

                This.closest('.OptionDiv').find('.ULRelatedQuestion').find('.LIQuestion:last-child').find('.ChildOptionType').trigger('change');

                This.closest('.OptionDiv').find('.ULRelatedQuestion').sortable();
            }
        }

    $(function(){

        $(document.body).on('click','.BtnSave', function(){
            $('.MainInput').removeClass('invalid_input');
            if($('.ListGroup').length>0){
                var QuestionSectionID = $('#questionnaire_section_id').val();
                var QuestionForGenderID = $('#question_for_gender_id').val();
                var isGoodToGo = true;
                var DataToSend = [];
                $('.ListGroup').each(function(i){
                    var MainQuestionSortID = i;
                    var MainQuestionOptionTypeID = $(this).find('.MainQuestionOptionType').val();
                    var MainQuestion = $(this).find('.MainQuestion').val().trim();
                    var MainQuestionTooltip = $(this).find('.TooltipInput').val().trim();
                    var MainQuestionIsRequired = $(this).find('.MainQuestionIsRequired').prop('checked') ? 1 : 0;

                    var MainQuestionID = $(this).find('.MainQuestion').attr('ParentQuestionID');


                    if(MainQuestion==""){
                        isGoodToGo = false;
                        $(this).find('.MainQuestion').addClass('invalid_input');
                    }

                    var Index = DataToSend.push(
                        {
                            QuestionSectionID:QuestionSectionID,
                            QuestionForGenderID:QuestionForGenderID,
                            MainQuestionID:MainQuestionID,
                            MainQuestionOptionTypeID:MainQuestionOptionTypeID,
                            MainQuestion:MainQuestion,
                            MainQuestionTooltip:MainQuestionTooltip,
                            MainQuestionIsRequired:MainQuestionIsRequired,
                            MainQuestionSortID:MainQuestionSortID,
                            MainOptions:[]}
                        ) - 1;

                    if($(this).find('.MainOption').length>0){
                        $(this).find('.MainOption').each(function(j){
                            var MainOptionTitle = $(this).val().trim();
                            var MainOptionSortID = j;
                            var MainOptionID = "";

                            if($(this).attr('ParentOptionID')){
                                MainOptionID = $(this).attr('ParentOptionID');
                            }

                            if(MainOptionTitle==""){
                                isGoodToGo = false;
                                $(this).addClass('invalid_input');
                            }
                            var MainOptionIndex = DataToSend[Index].MainOptions.push(
                                {
                                    MainOptionID:MainOptionID,
                                    MainOptionTitle:MainOptionTitle,
                                    MainOptionSortID:MainOptionSortID,
                                    RelatedQuestions:[]
                                }
                                ) - 1;

                            if($(this).closest('.OptionDiv').find('.ULRelatedQuestion').find('.LIQuestion').length>0){
                                $(this).closest('.OptionDiv').find('.ULRelatedQuestion').find('.LIQuestion').each(function(k){

                                    var ChildQuestionID = $(this).find('.ChildQuestion').attr('RelatedQuestionID');
                                    var ChildQuestionTitle = $(this).find('.ChildQuestion').val().trim();
                                    var ChildQuestionTooltip = $(this).find('.TooltipInput').val().trim();
                                    var ChildQuestionIsRequired = $(this).find('.ChildQuestionIsRequired').prop('checked') ? 1 : 0;


                                    var ChildQuestionSortID = k;

                                    if(ChildQuestionTitle==""){
                                        isGoodToGo = false;
                                        $(this).find('.ChildQuestion').addClass('invalid_input');
                                    }

                                    var ChildQuestionOptionType = $(this).find('.ChildQuestionOptionType').val();

                                    var IncreDecreDataFor = "";
                                    var IncreDecreGetData = "";
                                    if(ChildQuestionOptionType=="6"){
                                        IncreDecreDataFor = $(this).find('.IncreDecreDataFor').val().trim();
                                        IncreDecreGetData = $(this).find('.IncreDecreGetData').val().trim();

                                        if($(this).find('.IncreDecreDataFor').val().trim()==""){
                                            isGoodToGo = false;
                                            $(this).find('.IncreDecreDataFor').addClass('invalid_input');
                                        }

                                        // if($(this).find('.IncreDecreGetData').val().trim()==""){
                                        //     isGoodToGo = false;
                                        //     $(this).find('.IncreDecreGetData').addClass('invalid_input');
                                        // }
                                    }

                                    var RelatedQuestionIndex = DataToSend[Index].MainOptions[MainOptionIndex].RelatedQuestions.push(
                                        {
                                        ChildQuestionID:ChildQuestionID,
                                        ChildQuestionOptionType:ChildQuestionOptionType,
                                        ChildQuestionTitle:ChildQuestionTitle,
                                        ChildQuestionTooltip:ChildQuestionTooltip,
                                        IncreDecreDataFor:IncreDecreDataFor,
                                        IncreDecreGetData:IncreDecreGetData,
                                        ChildQuestionIsRequired:ChildQuestionIsRequired,
                                        ChildQuestionSortID:ChildQuestionSortID,
                                        ChildOptions:[]}
                                        ) - 1;

                                    if($(this).find('.ULOptions').find('.LIChildOption').length>0){

                                        $(this).find('.ULOptions').find('.LIChildOption').each(function(l){
                                            var ChildOptionID = $(this).find('.ChildOption').attr('RelatedOptionID');
                                            var ChildOptionTitle = $(this).find('.ChildOption').val().trim();
                                            var ChildOptionSortID = l;
                                            if(ChildOptionTitle==""){
                                                isGoodToGo = false;
                                                $(this).find('.ChildOption').addClass('invalid_input');
                                            }

                                            DataToSend[Index].MainOptions[MainOptionIndex].RelatedQuestions[RelatedQuestionIndex].ChildOptions.push(
                                                {
                                                    ChildOptionID:ChildOptionID,
                                                    ChildOptionTitle:ChildOptionTitle,
                                                    ChildOptionSortID:ChildOptionSortID
                                                }
                                                );
                                        });

                                    }

                                });
                            }


                        });
                    }

                });


                if(isGoodToGo){

                    var Obj = new Object();
                    Obj.questionnaire_section_id = $('select[name="questionnaire_section_id"]').val();
                    Obj.Data = JSON.stringify(DataToSend);
                    Obj._token = $('meta[name="csrf-token" ]').attr('content');

                    $.ajax({
                        url:'/dashboard/questions/update_question',
                        method:'POST',
                        beforeSend:function(){
                            Swal.fire({
                            position: 'center',
                            icon: 'info',
                            title: "Please Wait...",
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });
                        },
                        data:Obj,
                        success:function(e){
                            Swal.close();
                            if(e.status){
                                Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: e.message+'. Redirecting..',
                                showConfirmButton: false,
                                allowOutsideClick: false
                                });

                                window.location = e.redirect_url;

                            }else{

                                Swal.fire({
                                    position: 'center',
                                    icon: 'error',
                                    title: e.message,
                                    showConfirmButton: false,
                                    allowOutsideClick: false
                                });

                            }
                        }
                    });

                }


            }

        });



        $(document.body).on('change', '.OptionType', function(){
            var OptionType = $(this).val();
            $(this).closest('.ListGroup').find('.AppendOptionsHere').empty();
            if(OptionType=="1"){
                RadioOption($(this).closest('.ListGroup'));
            }
        });

        $(document.body).on('click', '.BtnRemoveQuestion',function(){
            $(this).closest('.ListGroup').remove();
        });

        $(document.body).on('click','.BtnRemoveOption', function(){
            $(this).closest('.OptionDiv').remove();
        });

        $(document.body).on('click','.BtnRemoveLIQuestion',function(){
            $(this).closest('.LIQuestion').remove();
        });
            AppendQuestion();
        $(document.body).on('click','.BtnAddMoreOptions',function(){
            RadioOption($(this).closest('.ListGroup'));
        });




        $(document.body).on('click','.BtnAddRelatedQuestion',function(){
            var OptionIndex = '';
            AddRelatedQuestion($(this), OptionIndex);
        });



        $(document.body).on('change','.ChildOptionType',function(){
            var OptionIndex = '';
            var RQIndex = '';
            ChildOptionTypeChanger($(this),OptionIndex, RQIndex);
        });

        $(document.body).on('click','.BtnChildAddMoreOptions', function(){
            var OptionIndex = '';
            var RQIndex = '';
            ChildRadio($(this).closest('.LIQuestion'), OptionIndex,RQIndex);
        });

        $(document.body).on('click','.BtnChildRemoveOption', function(){
            $(this).closest('.LIChildOption').remove();
        });

        $('.ULQuestions').sortable({
            update:function(){
                console.log('dropped');
            }
        });
    });

    function RadioOption(This){
        if(This.find('.AppendOptionsHere').find('.RadioMainParentDiv').length==0){

            var OptionDivHTML = '';
        if(json.Options.length>0){
            $.each(json.Options, function(i, option){
                OptionDivHTML += '<li class="OptionDiv mb-1 option_div position-relative">'
                +'<ul class="list-unstyled">'
                +'<li>'
                + '<div class="input-group">'
                + '<div class="input-group-prepend">'
                +'<span class="input-group-text fas fa-arrows d-flex align-items-center justify-content-center"></span>'
                +'</div>'
                +'<input type="text" class="form-control MainOption MainInput" ParentOptionID="'+option.ParentOptionID+'" value="'+option.ParentOptionTitle+'" placeholder="Enter Option">'
                +'<div class="input-group-append">'
                +'<button class="btn btn-outline-primary BtnAddRelatedQuestion" OptionIndex="'+i+'" type="button"><i class="fas fa-plus mr-1"></i>Add Related Question</button>'
                +'<button class="btn btn-outline-danger BtnRemoveOption btn_remove_option" type="button"><i class="fas fa-trash"></i></button>'
                +'</div>'
                +'</div>'

                + '<ul class="ULRelatedQuestion">'



                +'</ul>'

                +'</li>'
                +'</ul>'
                +'</li>';
            });
        }

            This.find('.AppendOptionsHere').append('<ul class="RadioMainParentDiv list-unstyled">'
            + OptionDivHTML
            +'</ul><button class="btn btn-primary btn-sm BtnAddMoreOptions" type="button">Add More Options</button>');

            $('.BtnAddRelatedQuestion').each(function(){
                if($(this).attr('OptionIndex')!=""){
                    var OptionIndex = $(this).attr('OptionIndex');
                    AddRelatedQuestion($(this),OptionIndex);
                }
            });

        }else{

            var OptionDivHTML = '<li class="OptionDiv mb-1 option_div position-relative">'
                +'<ul class="list-unstyled">'
                +'<li>'
                + '<div class="input-group">'
                + '<div class="input-group-prepend">'
                +'<span class="input-group-text fas fa-arrows d-flex align-items-center justify-content-center"></span>'
                +'</div>'
                +'<input type="text" class="form-control MainOption MainInput" placeholder="Enter Option">'
                +'<div class="input-group-append">'
                +'<button class="btn btn-outline-primary BtnAddRelatedQuestion" OptionIndex="" type="button"><i class="fas fa-plus mr-1"></i>Add Related Question</button>'
                +'<button class="btn btn-outline-danger BtnRemoveOption btn_remove_option" type="button"><i class="fas fa-trash"></i></button>'
                +'</div>'
                +'</div>'

                + '<ul class="ULRelatedQuestion">'



                +'</ul>'

                +'</li>'
                +'</ul>'
                +'</li>';

            This.find('.AppendOptionsHere').find('.RadioMainParentDiv').append(OptionDivHTML);
        }

        $('.RadioMainParentDiv').sortable();

    }

    function ChildRadio(This, OptionIndex, RQIndex){

        if(OptionIndex!=="" && RQIndex!==""){
            var RQObj = json.Options[OptionIndex].RelatedQuestions[RQIndex];
            if(RQObj.RelatedOptions.length>0){
                var HTML = '';
                $.each(RQObj.RelatedOptions, function(i, option){
                    HTML += '<li class="list-unstyled mt-1 LIChildOption li_child_option">'
                    +'<div class="input-group">'
                    +'<div class="input-group-prepend">'
                    +'<span class="input-group-text fas fa-arrows d-flex align-items-center justify-content-center"></span>'
                    +'</div>'
                    +'<input type="text" class="form-control ChildOption MainInput" RelatedOptionID="'+option.RelatedOptionID+'" value="'+option.RelatedOptionTitle+'" placeholder="Enter Option">'

                    +'<div class="input-group-append">'
                    +'<button class="btn btn-outline-danger btn_child_remove_option BtnChildRemoveOption not_removable" type="button"><i class="fas fa-trash"></i></button>'
                    +'</div>'

                    +'</div>'


                    +'</li>';
                });

                var LIBtnChildAddMoreOptions = '<li class="list-unstyled LIChildAddMoreOptions mt-1"><button class="btn btn-primary btn-sm BtnChildAddMoreOptions" type="button">Add More Options</button></li>';

                if(This.find('.ULOptions').find('.LIChildOption').length==0){
                    HTML = HTML+LIBtnChildAddMoreOptions;
                    This.find('.ULOptions').append(HTML);
                }else{
                    $(HTML).insertBefore(This.find('.ULOptions').find('.LIChildAddMoreOptions'));
                }


            }
        }else{

            var HTML = '<li class="list-unstyled mt-1 LIChildOption li_child_option">'
            +'<div class="input-group">'
            +'<div class="input-group-prepend">'
            +'<span class="input-group-text fas fa-arrows d-flex align-items-center justify-content-center"></span>'
            +'</div>'
            +'<input type="text" class="form-control ChildOption MainInput" RelatedOptionID="" placeholder="Enter Option">'

            +'<div class="input-group-append">'
            +'<button class="btn btn-outline-danger btn_child_remove_option BtnChildRemoveOption" type="button"><i class="fas fa-trash"></i></button>'
            +'</div>'

            +'</div>'


            +'</li>';

            var LIBtnChildAddMoreOptions = '<li class="list-unstyled LIChildAddMoreOptions mt-1"><button class="btn btn-primary btn-sm BtnChildAddMoreOptions" type="button">Add More Options</button></li>';

            if(This.find('.ULOptions').find('.LIChildOption').length==0){
                HTML = HTML+HTML+LIBtnChildAddMoreOptions;
                This.find('.ULOptions').append(HTML);
            }else{
                $(HTML).insertBefore(This.find('.ULOptions').find('.LIChildAddMoreOptions'));
            }

            This.find('.ULOptions').sortable( { items: "> li:not(:last)" } );
        }

    }

    function AppendQuestion(){
        var IsRequiredChecked = '';

            if(json.ParentIsRequired==1){
                IsRequiredChecked = 'checked';
            }

            var ParentTooltipInfo = '';
            if(json.ParentTooltipInfo){
                ParentTooltipInfo = json.ParentTooltipInfo;
            }

            $('.ULQuestions').append('<li class="list-group-item mb-2 ListGroup" style="border:4px solid var(--secondary)">'
            +'<div class="input-group" style="flex:10">'
            + '<div class="input-group-prepend">'
            + '<select class="form-control OptionType MainQuestionOptionType" style="pointer-events:none" style="flex:1">'
            + QuestionTypeHTMLParent
            +'</select>'
            +'</div>'
            +'<input type="text" class="form-control question_input MainQuestion MainInput" value="'+json.ParentQuestionTitle+'" ParentQuestionID="'+json.ParentQuestionID+'" placeholder="Enter Question" style="flex:3">'
            +'<input type="text" class="form-control TooltipInput" value="'+ParentTooltipInfo+'" placeholder="Enter Tooltip Info">'
            +'<div class="input_group_append"><label class="input-group-text h-100"><input type="checkbox" aria-label="Checkbox for following text input" '+IsRequiredChecked+' class="MainQuestionIsRequired mr-1">Is Required</label></div>'
            +'<div class="input-group-append div_btn_remove">'
            +'<button class="btn btn-outline-danger BtnRemoveQuestion" type="button"><i class="fas fa-trash"></i></button>'
            +'</div>'

            // +'</div>'

            +'</div>'

            +'<ul>'

            +'<li class="list-unstyled mt-2">'

            + '<div class="AppendOptionsHere">'

            +'</div>'

            +'</li>'



            +'</ul>'
            +'</li>');

            $('.ListGroup:last-child .OptionType').trigger('change');
    }





</script>
@stop
