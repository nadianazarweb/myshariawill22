<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Question;
use App\Models\Option;
use App\Models\RelatedQuestion;
use App\Models\RelatedOption;
use App\Models\AttemptedQuestionOption;
use App\Models\RequestChange;
use App\Models\ReportsHistory;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Stripe\Exception\CardException;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

class CustomerController extends Controller
{

    public function index()
    {
        // Check if user has completed questionnaire but hasn't paid
        $user = $this->login_check();
        $userRecord = User::find($user->user_id);
        
        if ($userRecord && $userRecord->is_locked == 1 && $userRecord->is_payment_done == 0) {
            // Redirect directly to payment instead of showing view
            return redirect()->route('payment_checkout', ['user_id' => $user->user_id]);
        }
        
        $view = 'customer.dashboard';
        $report = ReportsHistory::where(['user_id'=>$user->user_id,'approval_status'=>'Approved'])->first(['folder','file']);

        $pendingRequestsCount = RequestChange::where(['user_id'=>$user->user_id,'request_fulfillment_status'=>'Pending'])->count();
        $fulfilledRequestsCount = RequestChange::where(['user_id'=>$user->user_id,'request_fulfillment_status'=>'Fulfilled'])->count();

        return view($view, compact('user','report','pendingRequestsCount','fulfilledRequestsCount'));
    }

    public function login_check()
    {
        $userdata = new \stdClass();
        $userdata->role_id = Session::get('role_id');
        $userdata->user_id = Session::get('user_id');
        $userdata->user_name = Session::get('user_name');
        $userdata->full_name = Session::get('full_name');
        $userdata->avatar = Session::get('avatar');
        $userdata->email = Session::get('email');
        $userdata->contact_no = Session::get('contact_no');
        $userdata->role_name = Session::get('role_name');

        return $userdata;
    }

    public function change_password(Request $req)
    {
        $UserID = $this->login_check()->user_id;
        $Password = Hash::make($req->password);
        $UserUpdate = User::find($UserID);
        $UserUpdate->password = $Password;
        $UserUpdate->save();

        return redirect()->back()->with('success_msg', 'Password Changed Successfully');
    }

    public function settings()
    {
        $view = 'customer.settings';
        $user = $this->login_check();
        return view($view, compact('user'));
    }

    public function update_settings(Request $req)
    {

        $UpdateUser = User::find($this->login_check()->user_id);
        $UpdateUser->name = $req->name;
        $UpdateUser->contact_no = $req->contact_no;
        if ($req->hasFile('avatar')):
            $file = $req->file('avatar');
            $extension = $file->extension();
            $filename = $this->login_check()->user_id . ' - ' . date('dmYhis') . '.' . $extension;
            $Folder = 'users/';

            $FolderPath = public_path($Folder);
            if (!file_exists($FolderPath)):
                mkdir($FolderPath, 0777, true);
            endif;

            if (file_exists($FolderPath)):
                $filePath = $file->move($FolderPath, $filename);
                $UpdateUser->avatar = $Folder . $filename;

                Session::put('avatar', $Folder . $filename);

            endif;
        endif;
        $UpdateUser->save();

        Session::put('full_name', $req->name);
        Session::put('contact_no', $req->contact_no);

        return redirect()->back()->with('success_msg', 'Profile Updated Successfully');

    }

    public function book_appointment()
    {
        $view = 'customer.book_appointment';
        $user = $this->login_check();
        $AppointmentPaymentData = User::where('id',$user->user_id)->first(['is_appointment_purchased','appointment_payment_date']);
        $AppointmentData = Appointment::where(['user_id' => $this->login_check()->user_id])->orderBy('for_date', 'DESC')->paginate(10);
        return view($view, compact('user', 'AppointmentData','AppointmentPaymentData'));
    }

    public function appointment_purchase(Request $req)
    {
        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
        $session = $stripe->checkout->sessions->create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'gbp',
                        'product_data' => [
                            'name' => 'My Sharia Wills Appointment Subscription',
                        ],
                        'unit_amount' => "2999",
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            "success_url" => url('/') . '/customer/appointment_payment/success',
            'cancel_url' => url('/') . '/customer/appointment_payment/failed',
        ]);
        $session_id = $session->id;
        $User = User::find($this->login_check()->user_id);
        $User->appointment_pre_payment_session_key = $session_id;
        $User->save();
        return redirect($session->url)->with('session_id', $session_id);

    }

    public function appointment_payment_status(Request $req){
        $ResponseMSG = '';
        $ResponseStatus = '';

        try{
            if ($req->PaymentStatus == 'success'):
                if (!empty($req->session()->get('session_id'))):
                    $session_id = $req->session()->get('session_id');
                    $User = User::where(['appointment_pre_payment_session_key'=>$session_id])->first();
                    if($User!=null):
                        $UserUpdate = User::find($User->id);
                        $UserUpdate->is_appointment_purchased = 1;
                        $UserUpdate->appointment_payment_date = date('Y-m-d h:i:s');
                        $UserUpdate->appointment_pre_payment_session_key = null;
    
                        $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
                        $session = $stripe->checkout->sessions->retrieve($session_id);
                        $stripe_response = json_encode($session);
                        $UserUpdate->appointment_payment_response = $stripe_response;
                        if ($UserUpdate->save()):
                            $ResponseMSG = 'Payment Successful';
                            $ResponseStatus = 'success_msg';
                        else:
                            $ResponseStatus = 'failure_msg';
                            $ResponseMSG = 'Some Error Occured While Sending The Request';
                        endif;
    
                    endif;
                endif;
                elseif ($req->PaymentStatus == 'failed'):
                    $ResponseStatus = 'failure_msg';
                    $ResponseMSG = 'Payment Failed Due To Some Error';
                endif;
        }catch (\Exception $ex) {
            $ResponseStatus = 'failure_msg';
            $ResponseMSG = $this->ExceptionMessage($ex);
        }
        return redirect()->route('customer_book_appointment')->with($ResponseStatus, $ResponseMSG);
        
    }

    public function booked_time_slots(Request $req)
    {
        $response = ['status' => false, 'msg' => '', 'data' => []];
        try {
            $ForDate = $req->for_date;

            $AppointmentData = Appointment::whereDate('for_date', $ForDate)->get()->toArray();

            $response['status'] = true;
            $response['data'] = $AppointmentData;


        } catch (\Exception $ex) {
            $response['msg'] = $this->ExceptionMessage($ex);
        }

        return $response;
    }

    public function add_appointment(Request $req)
    {
        $ResponseMSG = '';
        $ResponseStatus = '';
        try {
            $UserID = Session::get('user_id');
            $ExplodedTimeSlot = explode(' - ', $req->time_slot);

            $ForTimeSlotStart = trim($ExplodedTimeSlot[0]);
            $ForTimeSlotEnd = trim($ExplodedTimeSlot[1]);

            $ManagerID = User::where(['role_id' => 8])->first();

            $Appointment = new Appointment;
            $Appointment->user_id = $UserID;
            $Appointment->for_manager_id = $ManagerID->id; //sheikh id, can be dynamic in the future
            $Appointment->for_date = $req->for_date;
            $Appointment->for_time_slot_start = $ForTimeSlotStart;
            $Appointment->for_time_slot_end = $ForTimeSlotEnd;

            if ($Appointment->save()):
                $ResponseStatus = 'success_msg';
                $ResponseMSG = 'Appointment Created Successfully';
            else:
                $ResponseStatus = 'failure_msg';
                $ResponseMSG = 'Some Error Occured While Sending The Request';
            endif;


        } catch (\Exception $ex) {
            $ResponseStatus = 'failure_msg';
            $ResponseMSG = $this->ExceptionMessage($ex);
        }
        return redirect()->back()->with($ResponseStatus, $ResponseMSG);
    }

    public function forms()
    {
        // Check if user has completed questionnaire but hasn't paid
        $user = $this->login_check();
        $userRecord = User::find($user->user_id);
        
        if ($userRecord && $userRecord->is_locked == 1 && $userRecord->is_payment_done == 0) {
            // Redirect directly to payment instead of showing view
            return redirect()->route('payment_checkout', ['user_id' => $user->user_id]);
        }
        
        $view = 'customer.forms';
        $FinalArray = array();

        $AttemptedByUsers = AttemptedQuestionOption::join('users AS U', 'U.id', '=', 'attempted_question_options.user_id')
            ->where('attempted_question_options.user_id', $user->user_id)
            ->groupBy(['U.id', 'U.name', 'U.is_locked'])->get(['U.id AS UserID', 'U.name AS UserFullName', 'U.is_locked AS IsLocked']);

        foreach ($AttemptedByUsers as $AUKey => $AUItem):
            $ApprovedReport = ReportsHistory::where(['user_id' => $AUItem->UserID, 'approval_status' => 'Approved'])->first(['folder', 'file']);
            if ($ApprovedReport == null):
                $ApprovedReport = '';
            endif;
            array_push($FinalArray, ['UserID' => $AUItem->UserID, 'UserFullName' => $AUItem->UserFullName, 'ApprovedReport' => $ApprovedReport, 'IsLocked' => $AUItem->IsLocked, 'AttemptedAnswers' => []]);
        endforeach;

        foreach ($FinalArray as $FAKey => $FAItem):
            $UserID = $FAItem['UserID'];
            $attempted_data = AttemptedQuestionOption::join('questions AS Q', 'Q.id', '=', 'attempted_question_options.question_id')
                ->where('attempted_question_options.user_id', $UserID)
                ->groupBy(['attempted_question_options.question_id', 'Q.title', 'Q.sort_id'])
                ->orderBy('Q.sort_id', 'ASC')
                ->get(['attempted_question_options.question_id AS QuestionID', 'Q.title AS QuestionTitle', 'Q.question_option_type_id AS ParentQuestionOptionTypeID', 'Q.sort_id AS ParentQuestionSortID']);
            if (count($attempted_data) > 0):
                foreach ($attempted_data as $key => $item):
                    array_push($FinalArray[$FAKey]['AttemptedAnswers'], ['QuestionID' => $item->QuestionID, 'QuestionTitle' => $item->QuestionTitle, 'ParentQuestionSortID' => $item->ParentQuestionSortID, 'ParentQuestionOptionTypeID' => $item->ParentQuestionOptionTypeID, 'AttemptedOptions' => []]);
                endforeach;
            endif;
        endforeach;

        foreach ($FinalArray as $FAKey => $FAItem):
            $UserID = $FAItem['UserID'];
            foreach ($FAItem['AttemptedAnswers'] as $AKey => $AItem):
                $QuestionID = $AItem['QuestionID'];

                $attempted_options = AttemptedQuestionOption::join('options AS O', 'O.id', '=', 'attempted_question_options.option_id', 'left')
                    ->where(['attempted_question_options.user_id' => $UserID, 'attempted_question_options.question_id' => $QuestionID])
                    ->groupBy(['attempted_question_options.option_id', 'O.title', 'attempted_question_options.text_value'])
                    ->get(['attempted_question_options.option_id AS OptionID', 'O.title AS OptionTitle', 'attempted_question_options.text_value AS TextValue']);

                if (count($attempted_options) > 0):
                    foreach ($attempted_options as $AOKey => $AOItem):
                        $OptionID = $AOItem->OptionID;
                        $OptionTitle = $AOItem->OptionTitle;
                        $TextValue = $AOItem->TextValue;

                        array_push($FinalArray[$FAKey]['AttemptedAnswers'][$AKey]['AttemptedOptions'], ['OptionID' => $OptionID, 'OptionTitle' => $OptionTitle, 'TextValue' => $TextValue, 'RelatedQuestions' => []]);

                    endforeach;
                endif;

            endforeach;
        endforeach;

        foreach ($FinalArray as $FAKey => $FAItem):
            $UserID = $FAItem['UserID'];
            foreach ($FAItem['AttemptedAnswers'] as $AKey => $AItem):
                foreach ($AItem['AttemptedOptions'] as $AOKey => $AOItem):

                    $OptionID = $AOItem['OptionID'];
                    if ($OptionID != ""):

                        $attempted_related_questions = AttemptedQuestionOption::join('related_questions AS RQ', 'RQ.id', '=', 'attempted_question_options.related_question_id', 'left')
                            ->where(['attempted_question_options.user_id' => $UserID, 'attempted_question_options.option_id' => $OptionID])
                            ->where('attempted_question_options.related_question_id', '<>', 'NULL')
                            ->groupBy(['attempted_question_options.related_question_id', 'RQ.sort_id', 'RQ.title', 'attempted_question_options.text_value'])
                            ->orderBy('RQ.sort_id', 'ASC')
                            ->get(['attempted_question_options.related_question_id AS RelatedQuestionID', 'RQ.sort_id AS RelatedQuestionSortID', 'RQ.question_option_type_id AS RelatedQuestionOptionTypeID', 'RQ.title AS RelatedQuestionTitle']);

                        if (count($attempted_related_questions) > 0):
                            foreach ($attempted_related_questions as $ARQKey => $ARQItem):

                                $RelatedQuestionID = $ARQItem['RelatedQuestionID'];
                                $RelatedQuestionTitle = $ARQItem['RelatedQuestionTitle'];
                                $RelatedQuestionOptionTypeID = $ARQItem['RelatedQuestionOptionTypeID'];

                                array_push($FinalArray[$FAKey]['AttemptedAnswers'][$AKey]['AttemptedOptions'][$AOKey]['RelatedQuestions'], ['RelatedQuestionID' => $RelatedQuestionID, 'RelatedQuestionSortID' => $ARQItem['RelatedQuestionSortID'], 'RelatedQuestionTitle' => $RelatedQuestionTitle, 'RelatedQuestionOptionTypeID' => $RelatedQuestionOptionTypeID, 'RelatedOptions' => []]);


                            endforeach;
                        endif;
                    endif;

                endforeach;
            endforeach;
        endforeach;


        foreach ($FinalArray as $FAKey => $FAItem):
            $UserID = $FAItem['UserID'];
            foreach ($FAItem['AttemptedAnswers'] as $AKey => $AItem):
                foreach ($AItem['AttemptedOptions'] as $AOKey => $AOItem):
                    if (count($AOItem['RelatedQuestions']) > 0):
                        foreach ($AOItem['RelatedQuestions'] as $RQKey => $RQItem):

                            $RelatedQuestionID = $RQItem['RelatedQuestionID'];

                            $attempted_related_options = AttemptedQuestionOption::join('related_options AS RO', 'RO.id', '=', 'attempted_question_options.related_option_id', 'left')
                                ->where(['attempted_question_options.related_question_id' => $RelatedQuestionID, 'attempted_question_options.user_id' => $UserID])
                                ->get(['attempted_question_options.related_option_id AS RelatedOptionID', 'RO.title AS RelatedOptionTitle', 'attempted_question_options.related_text_value AS RelatedTextValue']);

                            if (count($attempted_related_options) > 0):

                                foreach ($attempted_related_options as $AROKey => $AROItem):
                                    $RelatedOptionID = $AROItem['RelatedOptionID'];
                                    $RelatedOptionTitle = $AROItem['RelatedOptionTitle'];
                                    $RelatedTextValue = $AROItem['RelatedTextValue'];

                                    array_push($FinalArray[$FAKey]['AttemptedAnswers'][$AKey]['AttemptedOptions'][$AOKey]['RelatedQuestions'][$RQKey]['RelatedOptions'], ['RelatedOptionID' => $RelatedOptionID, 'RelatedOptionTitle' => $RelatedOptionTitle, 'RelatedTextValue' => $RelatedTextValue]);

                                endforeach;

                            endif;


                        endforeach;
                    endif;
                endforeach;
            endforeach;
        endforeach;
        // $FinalArray = json_encode($FinalArray);

        $QSubmissionDateTime = User::where('id', $user->user_id)->first(['queries_submission_datetime', 'customer_approved']);

        // echo 'Current date is '.date('Y-m-d H:i:s').'<br>';
        // echo 'submission date is '.date('Y-m-d H:i:s',strtotime($QSubmissionDateTime->queries_submission_datetime));
        return view($view, compact('user', 'FinalArray', 'QSubmissionDateTime'));
    }

    public function edit_attempted_answer(Request $req)
    {
        $UserID = Session::get('user_id');
        $QuestionID = $req->QuestionID;


        $VerifyingUserWithAssignedAccountant = User::where(['id' => $UserID, 'is_locked' => 1, 'customer_approved' => '0'])->first();

        if ($VerifyingUserWithAssignedAccountant != null):
            $qry_submission_date = date('Y-m-d H:i:s', strtotime($VerifyingUserWithAssignedAccountant->queries_submission_datetime));

            $expiration_date = date('Y-m-d H:i:s', strtotime($qry_submission_date . ' +1 day'));

            if ($expiration_date > date('Y-m-d H:i:s')):

                if ($VerifyingUserWithAssignedAccountant != null):
                    $QuestionData = Question::find($QuestionID);
                    if ($QuestionData != null):


                        $AttemptedQuestionData = AttemptedQuestionOption::where(['question_id' => $QuestionID, 'user_id' => $UserID])->get();
                        $AttemptedQuestionData = $AttemptedQuestionData->toArray();


                        $FinalArray = new \stdClass();
                        $FinalArray->UserID = $UserID;
                        $FinalArray->ParentQuestionID = $QuestionData->id;
                        $FinalArray->ParentQuestionTitle = $QuestionData->title;
                        $FinalArray->ParentQuestionOptionTypeID = $QuestionData->question_option_type_id;
                        $FinalArray->Options = [];

                        $AttemptedQuestionData = array_map(function ($item) {
                            if ($item['related_text_value'] == null && $item['related_option_id'] == null):
                                $item['QuestionType'] = 'Parent';
                            else:
                                $item['QuestionType'] = 'Child';
                            endif;
                            return $item;
                        }, $AttemptedQuestionData);

                        $FinalArray->AttemptedOptions = $AttemptedQuestionData;

                        $OptionData = Option::where(['question_id' => $QuestionID])->orderBy('sort_id', 'ASC')->get();
                        if (count($OptionData) > 0):

                            foreach ($OptionData as $ODKey => $ODItem):
                                array_push($FinalArray->Options, ['ParentOptionID' => $ODItem->id, 'ParentOptionTitle' => $ODItem->title, 'RelatedQuestions' => array()]);
                            endforeach;

                            foreach ($FinalArray->Options as $OKey => $OItem):
                                $ParentOptionID = $OItem['ParentOptionID'];
                                $RQData = RelatedQuestion::where(['option_id' => $ParentOptionID])->orderBy('sort_id', 'ASC')->get([
                                    'id AS RelatedQuestionID',
                                    'title AS RelatedQuestionTitle',
                                    'question_option_type_id AS RelatedQuestionOptionTypeID',
                                    'related_questions.incre_decre_data_for AS IncreDecreDataFor',
                                    'related_questions.incre_decre_get_data AS IncreDecreGetData'
                                ]);
                                if (count($RQData) > 0):
                                    $RQData = $RQData->toArray();

                                    $RQData = array_map(function ($item) {
                                        $item['RelatedOptions'] = [];
                                        return $item;
                                    }, $RQData);
                                    $FinalArray->Options[$OKey]['RelatedQuestions'] = $RQData;
                                endif;
                            endforeach;

                            foreach ($FinalArray->Options as $OKey => $OItem):
                                if (count($OItem['RelatedQuestions']) > 0):
                                    foreach ($OItem['RelatedQuestions'] as $RQKey => $RQItem):
                                        $ROData = RelatedOption::where(['related_question_id' => $RQItem['RelatedQuestionID']])->orderBy('sort_id', 'ASC')->get(['id AS RelatedOptionID', 'title AS RelatedOptionTitle']);
                                        if (count($ROData) > 0):
                                            $FinalArray->Options[$OKey]['RelatedQuestions'][$RQKey]['RelatedOptions'] = $ROData->toArray();
                                        endif;
                                    endforeach;
                                endif;
                            endforeach;
                        endif;

                        $user = $this->login_check();
                        $view = 'customer.edit_attempted_answer';
                        return view($view, compact('user', 'FinalArray'));
                    else:
                        return redirect()->route('customer_forms');
                    endif;
                else:
                    return redirect()->route('customer_forms');
                endif;
            else:
                return redirect()->route('customer_forms');
            endif;

        else:
            return redirect()->route('customer_forms');
        endif;
    }

    public function update_attempted_answer(Request $req)
    {
        $ResponseMSG = '';
        $ResponseStatus = '';
        try {


            $ParentQuestionID = $req->parent_question_id;
            $UserID = Session::get('user_id');
            if ($req->has('text_value') || $req->has('parent_option_id')):
                AttemptedQuestionOption::where(['user_id' => $UserID, 'question_id' => $ParentQuestionID])->delete();
                if ($req->has('text_value')):
                    $AttemptedQuestionOptionSave = new AttemptedQuestionOption();
                    $AttemptedQuestionOptionSave->user_id = $UserID;
                    $AttemptedQuestionOptionSave->question_id = $ParentQuestionID;
                    $AttemptedQuestionOptionSave->text_value = $req->text_value;
                    if ($AttemptedQuestionOptionSave->save()):
                    endif;
                endif;
                if ($req->has('parent_option_id')):
                    $ParentOptionID = $req->parent_option_id;

                    $AttemptedQuestionOptionSave = new AttemptedQuestionOption();
                    $AttemptedQuestionOptionSave->user_id = $UserID;
                    $AttemptedQuestionOptionSave->question_id = $ParentQuestionID;
                    $AttemptedQuestionOptionSave->option_id = $ParentOptionID;
                    $AttemptedQuestionOptionSave->save();


                    if ($req->has('related_question_id')):
                        if (count($req->related_question_id) > 0):
                            if ($req->has('For')):
                                if (count($req->For) > 0):
                                    $FinalArrayToJson = [];

                                    foreach ($req->For as $ForKey => $ForItem):
                                        $RelatedQuestionID = $req->related_question_id[$ForKey];
                                        array_push($FinalArrayToJson, ['RelatedQuestionID' => $RelatedQuestionID, 'MainData' => []]);
                                        $Index = count($FinalArrayToJson) - 1;
                                        foreach ($ForItem as $FIKey => $FIItem):
                                            array_push($FinalArrayToJson[$Index]['MainData'], ['For' => $FIKey, 'Total' => $FIItem['Total'], 'Data' => []]);
                                        endforeach;
                                    endforeach;

                                    if (count($FinalArrayToJson) > 0):
                                        foreach ($FinalArrayToJson as $FKey => $FItem):
                                            if (count($FItem['MainData']) > 0):

                                                foreach ($FItem['MainData'] as $MDKey => $MDItem):

                                                    $For = $MDItem['For'];

                                                    if (isset($req->For[$FKey][$For]['Data'])):
                                                        if (count($req->For[$FKey][$For]['Data']) > 0):
                                                            foreach ($req->For[$FKey][$For]['Data'] as $DKey => $DItem):
                                                                array_push($FinalArrayToJson[$FKey]['MainData'][$MDKey]['Data'], ['TDValues' => []]);
                                                                $DataIndex = count($FinalArrayToJson[$FKey]['MainData'][$MDKey]['Data']) - 1;

                                                                foreach ($DItem as $DIKey => $DIItem):
                                                                    array_push($FinalArrayToJson[$FKey]['MainData'][$MDKey]['Data'][$DataIndex]['TDValues'], ['name' => $DIKey, 'value' => $DIItem == null ? '' : $DIItem]);
                                                                endforeach;

                                                            endforeach;


                                                        endif;
                                                    endif;

                                                endforeach;

                                            endif;

                                        endforeach;

                                    endif;

                                    if (count($FinalArrayToJson) > 0):

                                        foreach ($FinalArrayToJson as $FKey => $FItem):
                                            $RelatedQuestionID = $FItem['RelatedQuestionID'];
                                            $AttemptedQuestionOptionSave = new AttemptedQuestionOption();
                                            $AttemptedQuestionOptionSave->user_id = $UserID;
                                            $AttemptedQuestionOptionSave->question_id = $ParentQuestionID;
                                            $AttemptedQuestionOptionSave->option_id = $ParentOptionID;
                                            $AttemptedQuestionOptionSave->related_question_id = $RelatedQuestionID;
                                            $AttemptedQuestionOptionSave->related_text_value = json_encode($FItem['MainData']);
                                            $AttemptedQuestionOptionSave->save();
                                        endforeach;

                                    endif;

                                    // if ($req->has('name')):
                                    //     if (count($req->name) > 0):

                                    //         foreach ($FinalArrayToJson as $FATJKey => $FATJItem):
                                    //             foreach ($req->name[$FATJItem['For']] as $FKey => $FItem):
                                    //                 array_push($FinalArrayToJson[$FATJKey]['Data'], ['TDValues' => []]);
                                    //                 $DataIndex = count($FinalArrayToJson[$FATJKey]['Data']) - 1;
                                    //                 foreach ($FItem as $FIKey => $FIItem):
                                    //                     array_push($FinalArrayToJson[$FATJKey]['Data'][$DataIndex]['TDValues'], ['name' => $FIKey, 'value' => $FIItem == null ? '' : $FIItem]);

                                    //                 endforeach;
                                    //             endforeach;


                                    //         endforeach;

                                    //     endif;
                                    // endif;

                                    // if (count($FinalArrayToJson) > 0):
                                    //     foreach ($req->related_question_id as $RQKey => $RQItem):
                                    //         $RQID = $RQItem;
                                    //         $AttemptedQuestionOptionSave = new AttemptedQuestionOption();
                                    //         $AttemptedQuestionOptionSave->user_id = $UserID;
                                    //         $AttemptedQuestionOptionSave->question_id = $ParentQuestionID;
                                    //         $AttemptedQuestionOptionSave->option_id = $ParentOptionID;
                                    //         $AttemptedQuestionOptionSave->related_question_id = $RQID;
                                    //         $AttemptedQuestionOptionSave->related_text_value = json_encode($FinalArrayToJson);
                                    //         $AttemptedQuestionOptionSave->save();

                                    //     endforeach;


                                    // endif;


                                endif;
                            else:
                                foreach ($req->related_question_id as $RQKey => $RQItem):
                                    $RQID = $RQItem;
                                    if (array_key_exists('rq_id_' . $RQID, $req->related_text_value)):
                                        $RQTV = $req->related_text_value['rq_id_' . $RQID];

                                        $AttemptedQuestionOptionSave = new AttemptedQuestionOption();
                                        $AttemptedQuestionOptionSave->user_id = $UserID;
                                        $AttemptedQuestionOptionSave->question_id = $ParentQuestionID;
                                        $AttemptedQuestionOptionSave->option_id = $ParentOptionID;
                                        $AttemptedQuestionOptionSave->related_question_id = $RQID;
                                        $AttemptedQuestionOptionSave->related_text_value = $RQTV;
                                        $AttemptedQuestionOptionSave->save();

                                    endif;
                                endforeach;
                            endif;

                        endif;
                    endif;
                endif;

                $ResponseStatus = 'success_msg';
                $ResponseMSG = 'Updated Successfully';
            endif;
        } catch (\Exception $ex) {
            $ResponseStatus = 'failure_msg';
            $ResponseMSG = $this->ExceptionMessage($ex);
        }

        return redirect()->route('customer_forms')->with($ResponseStatus, $ResponseMSG);
    }

    public function request_changes(Request $req)
    {
        $response = ['status' => false, 'msg' => ''];
        try {
            $UserID = Session::get('user_id');

            $ReportsHistory = ReportsHistory::where(['user_id'=>$UserID,'approval_status'=>'Pending'])->get();

            if(count($ReportsHistory)==0):
                $RequestChange = new RequestChange();
                $RequestChange->request_remarks = $req->request_remarks;
                $RequestChange->user_id = $UserID;
                if ($RequestChange->save()):
                    $response['status'] = true;
                    $response['msg'] = 'Request Sent Successfully';
                else:
                    $response['msg'] = 'Some Error Occured While Sending The Request';
                endif;

            else:
                $response['msg'] = 'You cant request for any changes. Your report has already been sent for the approval';
            endif;

        } catch (\Exception $ex) {
            $response['msg'] = $this->ExceptionMessage($ex);
        }
        return $response;
    }

    public function final_approve(Request $req)
    {
        $ResponseMSG = '';
        $ResponseStatus = '';
        try {
            $UserID = Session::get('user_id');

            $FinalApprove = User::find($UserID);
            $FinalApprove->customer_approved = 1;
            if ($FinalApprove->save()):
                $ResponseStatus = 'success_msg';
                $ResponseMSG = 'Approved Successfully';
            else:
                $ResponseStatus = 'failure_msg';
                $ResponseMSG = 'Some Error Occured While Sending The Request';
            endif;

        } catch (\Exception $ex) {
            $ResponseStatus = 'failure_msg';
            $ResponseMSG = $this->ExceptionMessage($ex);
        }
        return redirect()->route('customer_forms')->with($ResponseStatus, $ResponseMSG);
    }

    public function ExceptionMessage($ex)
    {
        return 'Exception | ' . $ex->getMessage() . ' On Line Number ' . $ex->getLine();
    }
}