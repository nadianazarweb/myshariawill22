<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Option;
use App\Models\RelatedQuestion;
use App\Models\RelatedOption;
use App\Models\AttemptedQuestionOption;
use Auth;
use App\Models\User;
use App\Models\RequestChange;
use App\Models\ReportsHistory;
use Mail;
use Illuminate\Support\Facades\Storage;


class AccountantController extends Controller
{
    public function index()
    {
        $view = 'accountant.dashboard';
        $user = $this->login_check();
        $ApprovedReportsCount = ReportsHistory::join('users AS U', 'U.id', '=', 'reports_histories.user_id')->where(['U.assigned_to_accountant_id' => $user->id, 'reports_histories.approval_status' => 'Approved'])->count();
        $TotalFormsCount = User::where(['assigned_to_accountant_id' => $user->id])->count();

        $PendingRequestsCount = RequestChange::join('users AS U', 'U.id', '=', 'request_changes.user_id')->where(['U.assigned_to_accountant_id' => $user->id, 'request_changes.request_fulfillment_status' => 'Pending'])->count();

        return view($view, compact('user', 'ApprovedReportsCount', 'TotalFormsCount', 'PendingRequestsCount'));
    }

    public function login_check()
    {
        if (Auth::check()) {
            $user = Auth()->user();
        }
        return $user;
    }

    public function my_forms()
    {
        $view = 'accountant.my_forms';
        $user = $this->login_check();
        $FinalArray = array();

        $AttemptedByUsers = AttemptedQuestionOption::join('users AS U', 'U.id', '=', 'attempted_question_options.user_id')
            ->where(['U.assigned_to_accountant_id' => $user->id])
            ->groupBy(['U.id', 'U.name', 'U.contact_no', 'U.email', 'U.assigned_to_accountant_id', 'U.is_locked'])->get(['U.id AS UserID', 'U.name AS UserFullName', 'U.queries_submission_datetime AS QueriesSubmissionDateTime', 'U.email AS UserEmail', 'U.contact_no AS ContactNo', 'U.assigned_to_accountant_id AS AssignedToAccountantID','U.assigned_to_accountant_date AS AssignedToAccountantDate', 'U.is_locked AS IsLocked']);

        foreach ($AttemptedByUsers as $AUKey => $AUItem):
            $ReportsHistory = ReportsHistory::join('users AS U', 'U.id', '=', 'reports_histories.sent_to_manager_id')->where(['reports_histories.user_id' => $AUItem->UserID])->orderBy('reports_histories.id', 'DESC')->get(
                [
                    'reports_histories.id',
                    'reports_histories.folder',
                    'reports_histories.file',
                    'reports_histories.accountant_remarks',
                    'reports_histories.approval_status',
                    'reports_histories.approval_status_datetime',
                    'reports_histories.rejection_remarks',
                    'reports_histories.is_read',
                    'reports_histories.created_at',
                    'U.id AS ManagerID',
                    'U.name AS ManagerFullName',
                    'U.email AS ManagerEmail',
                ]
            );

            $ReportIsNotRead = ReportsHistory::join('users AS U', 'U.id', '=', 'reports_histories.sent_to_manager_id')->where(['reports_histories.user_id' => $AUItem->UserID, 'reports_histories.is_read' => '0'])->count();

            $LastUserReportStatus = '';
            if (count($ReportsHistory) == 0):
                $LastUserReportStatus = 'NoAction';
            else:
                $LastUserReportStatus = $ReportsHistory[0]->approval_status;
            endif;

            array_push($FinalArray, [
                'UserID' => $AUItem->UserID,
                'UserFullName' => $AUItem->UserFullName,
                'ApprovalStatus' => $LastUserReportStatus,
                'QueriesSubmissionDateTime' => $AUItem->QueriesSubmissionDateTime,
                'UserEmail' => $AUItem->UserEmail,
                'ContactNo' => $AUItem->ContactNo,
                'AssignedToAccountantID' => $AUItem->AssignedToAccountantID,
                'AssignedToAccountantDate' => $AUItem->AssignedToAccountantDate,
                'AccountantName' => $AUItem->AccountantName,
                'IsLocked' => $AUItem->IsLocked,
                'AttemptedAnswers' => [],
                'ReportsHistory' => $ReportsHistory->toArray(),
                'ReportIsNotRead' => $ReportIsNotRead
            ]);
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
        usort($FinalArray, function ($a, $b) {
            return $b['ReportIsNotRead'] - $a['ReportIsNotRead'];
        });
        //Gathering Manager Data
        $ManagerData = User::where(['role_id' => 8])->get();
        return view($view, compact('user', 'FinalArray', 'ManagerData'));
    }

    public function update_read_status(Request $req)
    {
        $response = ['status' => false, 'msg' => ''];
        try {
            $UserID = $req->UserID;
            if (ReportsHistory::where('user_id', $UserID)->update(['is_read' => 1])):
                $response['status'] = true;
            endif;
        } catch (\Exception $ex) {
            $response['msg'] = $this->ExceptionMessage($ex);
        }
        return $response;
    }

    public function assign_to_accountant(Request $req)
    {
        $ResponseMSG = '';
        $ResponseStatus = '';
        try {

            $user = $this->login_check();
            $UpdateUser = User::find($req->user_id);
            $UpdateUser->assigned_to_accountant_id = $user->id;
            if ($UpdateUser->save()):
                $ResponseStatus = 'success_msg';
                $ResponseMSG = 'Assigned Successfully';
            else:
                $ResponseStatus = 'failure_msg';
                $ResponseMSG = 'Some Error Occured';
            endif;

        } catch (\Exception $ex) {
            $ResponseMSG = 'failure_msg';
            $ResponseStatus = $this->ExceptionMessage($ex);

        }
        return redirect()->route('accountant_all_forms')->with($ResponseStatus, $ResponseMSG);
    }

    public function requests_for_changes()
    {
        $view = 'accountant.requests_for_changes';
        $user = $this->login_check();

        $RequestChanges = RequestChange::join('users AS U', 'U.id', '=', 'request_changes.user_id')->where(['U.is_locked' => '1', 'assigned_to_accountant_id' => $user->id])->orderBy('request_changes.created_at', 'DESC')->get(['request_changes.id AS RequestChangesID', 'request_changes.user_id AS UserID', 'U.name AS UserFullName', 'U.email AS UserEmail', 'U.contact_no AS ContactNo', 'request_changes.request_remarks AS RequestRemarks', 'request_changes.request_fulfillment_status AS RequestFulfillmentStatus', 'request_changes.created_at']);
        $statusCounts = array_count_values(array_column(json_decode(json_encode($RequestChanges), true), 'RequestFulfillmentStatus'));
        $pendingCount = $statusCounts['Pending'] ?? 0;
        $fulfilledCount = $statusCounts['Fulfilled'] ?? 0;

        $FetchUserIDArrOfRequests = array_column(json_decode(json_encode($RequestChanges), true), 'UserID');

        $FetchUserIDArrOfRequests = array_unique($FetchUserIDArrOfRequests);

        $user = $this->login_check();
        $FinalArray = array();

        $AttemptedByUsers = AttemptedQuestionOption::join('users AS U', 'U.id', '=', 'attempted_question_options.user_id')
            ->where(['U.assigned_to_accountant_id' => $user->id])
            ->whereIn('U.id', $FetchUserIDArrOfRequests)
            ->groupBy(['U.id', 'U.name', 'U.contact_no', 'U.email', 'U.assigned_to_accountant_id', 'U.is_locked'])->get(['U.id AS UserID', 'U.name AS UserFullName', 'U.email AS UserEmail', 'U.contact_no AS ContactNo', 'U.assigned_to_accountant_id AS AssignedToAccountantID', 'U.is_locked AS IsLocked']);

        foreach ($AttemptedByUsers as $AUKey => $AUItem):
            array_push($FinalArray, [
                'UserID' => $AUItem->UserID,
                'UserFullName' => $AUItem->UserFullName,
                'UserEmail' => $AUItem->UserEmail,
                'ContactNo' => $AUItem->ContactNo,
                'AssignedToAccountantID' => $AUItem->AssignedToAccountantID,
                'AccountantName' => $AUItem->AccountantName,
                'IsLocked' => $AUItem->IsLocked,
                'AttemptedAnswers' => []
            ]);
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
        return view($view, compact('user', 'RequestChanges', 'pendingCount', 'fulfilledCount', 'FinalArray'));
    }

    public function mark_as_fulfilled(Request $req)
    {
        // Mail::send('emails.verification', compact('url'), function ($message)use($Email) {
        //     $message->to($Email);
        //     $message->from(config('mail.from.address'));
        //     $message->subject('Verify Your Email');
        // });
        $ResponseMSG = '';
        $ResponseStatus = '';
        try {

            $UserDetails = RequestChange::join('users AS U', 'U.id', '=', 'request_changes.user_id')->where('request_changes.id', $req->request_change_id)->first(['U.email AS UserEmail', 'request_changes.request_remarks']);

            if ($UserDetails != null):
                $UserEmail = $UserDetails->UserEmail;
                $RequestRemarks = $UserDetails->request_remarks;

                Mail::send('emails.customer_request_fulfilled', compact('RequestRemarks'), function ($message) use ($UserEmail) {
                    $message->to($UserEmail);
                    $message->from(config('mail.from.address'));
                    $message->subject('Request Fulfilled');
                });

                $MarkFulfilled = RequestChange::find($req->request_change_id);
                $MarkFulfilled->request_fulfillment_status = 'Fulfilled';
                if ($MarkFulfilled->save()):
                    $ResponseStatus = 'success_msg';
                    $ResponseMSG = 'Fulfilled Successfully';
                else:
                    $ResponseStatus = 'failure_msg';
                    $ResponseMSG = 'Some Error Occured';
                endif;

            endif;


        } catch (\Exception $ex) {
            $ResponseStatus = 'failure_msg';
            $ResponseMSG = $this->ExceptionMessage($ex);
        }
        return redirect()->route('requests_for_changes')->with($ResponseStatus, $ResponseMSG);
    }

    public function edit_attempted_answer(Request $req)
    {
        $UserID = $req->UserID;
        $QuestionID = $req->QuestionID;
        $CurrentUser = $this->login_check();

        $VerifyingUserWithAssignedAccountant = User::where(['id' => $UserID, 'assigned_to_accountant_id' => $CurrentUser->id])->first();

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
                $view = 'accountant.edit_attempted_answer';
                return view($view, compact('user', 'FinalArray'));
            else:
                return redirect()->route('requests_for_changes');
            endif;
        else:
            return redirect()->route('requests_for_changes');
        endif;
    }

    public function update_attempted_answer(Request $req)
    {
        $ResponseMSG = '';
        $ResponseStatus = '';
        try {


            $ParentQuestionID = $req->parent_question_id;
            $UserID = $req->user_id;
            if ($req->has('text_value') || $req->has('parent_option_id')):
                AttemptedQuestionOption::where(['user_id' => $UserID, 'question_id' => $ParentQuestionID])->delete();
                if ($req->has('text_value')):
                    $AttemptedQuestionOptionSave = new AttemptedQuestionOption();
                    $AttemptedQuestionOptionSave->user_id = $UserID;
                    $AttemptedQuestionOptionSave->question_id = $ParentQuestionID;
                    $AttemptedQuestionOptionSave->text_value = $req->text_value;
                    $AttemptedQuestionOptionSave->save();
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
        return redirect()->route(explode('/', $req->redirect_to)[1] == "my_forms" ? "accountant_my_forms" : explode('/', $req->redirect_to)[1])->with($ResponseStatus, $ResponseMSG);
    }

    public function send_reportold(Request $req)
    {

        $user = $this->login_check();

        $FinalArray = array();

        $AttemptedByUsers = AttemptedQuestionOption::join('users AS U', 'U.id', '=', 'attempted_question_options.user_id')
            ->where(['U.assigned_to_accountant_id' => $user->id, 'attempted_question_options.user_id' => $req->UserID])
            ->groupBy(['U.id', 'U.name', 'U.contact_no', 'U.email', 'U.assigned_to_accountant_id', 'U.is_locked'])->get(['U.id AS UserID', 'U.name AS UserFullName', 'U.email AS UserEmail', 'U.contact_no AS ContactNo', 'U.assigned_to_accountant_id AS AssignedToAccountantID', 'U.is_locked AS IsLocked']);

        foreach ($AttemptedByUsers as $AUKey => $AUItem):
            array_push($FinalArray, [
                'UserID' => $AUItem->UserID,
                'UserFullName' => $AUItem->UserFullName,
                'UserEmail' => $AUItem->UserEmail,
                'ContactNo' => $AUItem->ContactNo,
                'AssignedToAccountantID' => $AUItem->AssignedToAccountantID,
                'AccountantName' => $AUItem->AccountantName,
                'IsLocked' => $AUItem->IsLocked,
                'AttemptedAnswers' => []
            ]);
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

        return view('accountant.reports', compact('FinalArray'));

    }

    public function creating_report(Request $req)
    {
        $response = ['status' => false, 'msg' => '', 'file_url' => ''];
        try {
            $ApprovalCount = ReportsHistory::where(['user_id' => $req->UserID, 'approval_status' => 'Approved'])->get();
            if (count($ApprovalCount) == 0):
                $PendingCount = ReportsHistory::where(['user_id' => $req->UserID, 'approval_status' => 'Pending'])->get();
                if (count($PendingCount) == 0):
                    $file = $req->file('file');
                    // Move the file to the public/uploads directory
                    $filename = $req->UserFullName . ' - ' . date('dmYhis') . '.pdf';
                    $filePath = $file->move(public_path('uploads'), $filename);
                    $ReportHistory = new ReportsHistory;
                    $ReportHistory->user_id = $req->UserID;
                    $ReportHistory->file = $filename;

                    if ($ReportHistory->save()):
                        $response['status'] = true;
                        $response['file_url'] = url('uploads/' . $filename);
                        $response['msg'] = 'Report has been sent successfully';
                    endif;

                else:

                    $response['msg'] = 'You\'ve already submitted a report for the approval';

                endif;
            else:
                $response['msg'] = 'The report of this user has already been approved';
            endif;

        } catch (\Exception $ex) {
            $response['msg'] = $this->ExceptionMessage($ex);
        }


        return $response;
    }

    public function reports()
    {
        $user = $this->login_check();
        return view('accountant.reports', compact('user'));
    }

    public function send_report(Request $req)
    {
        $ResponseMSG = 'failure_msg';
        $ResponseStatus = 'Some Error Occured';
        try {
            $RequestChanges = RequestChange::where(['user_id'=>$req->UserID,'request_fulfillment_status'=>'Pending'])->get();
            if(count($RequestChanges)==0):

                $ApprovalCount = ReportsHistory::where(['user_id' => $req->UserID, 'approval_status' => 'Approved'])->get();
                if (count($ApprovalCount) == 0):
                    $PendingCount = ReportsHistory::where(['user_id' => $req->UserID, 'approval_status' => 'Pending'])->get();
                    if (count($PendingCount) == 0):
                        $file = $req->file('file');

                        $extension = $file->extension();
                        // Move the file to the public/uploads directory
                        $filename = $req->UserFullName . ' - ' . date('dmYhis') . '.' . $extension;

                        $Folder = date('Y') . '/' . date('m') . '/' . date('d') . '/';

                        $Path = Storage::disk('reports')->putFileAs($Folder, $req->file('file'), $filename);

                        $ReportHistory = new ReportsHistory;
                        $ReportHistory->user_id = $req->UserID;
                        $ReportHistory->folder = $Folder;
                        $ReportHistory->file = $filename;
                        $ReportHistory->accountant_remarks = $req->accountant_remarks;
                        $ReportHistory->sent_to_manager_id = $req->manager_id;

                        if ($ReportHistory->save()):
                            $ResponseStatus = 'success_msg';
                            $ResponseMSG = 'Report Sent Successfully';
                        endif;

                    else:

                        $ResponseStatus = 'failure_msg';
                        $ResponseMSG = 'You\'ve already submitted a report for the approval';

                    endif;
                else:
                    $ResponseStatus = 'failure_msg';
                    $ResponseMSG = 'The report of this user has already been approved';
                endif;

            else:
                $ResponseStatus = 'failure_msg';
                $ResponseMSG = 'Please fulfill the changes requested by this user';
            endif;


        } catch (\Exception $ex) {
            $ResponseStatus = 'failure_msg';
            $ResponseMSG = $this->ExceptionMessage($ex);
        }
        return redirect()->route('accountant_my_forms')->with($ResponseStatus, $ResponseMSG);
    }

    public function fetch_reports(Request $req)
    {
        $response = ['status' => false, 'msg' => '', 'Data' => []];
        try {
            $user = $this->login_check();
            $FinalArray = [];
            if ($req->ReportType == 'approved' || $req->ReportType == 'rejected' || $req->ReportType == 'pending'):
                $ReportType = '';
                if ($req->ReportType == 'approved'):
                    $ReportType = 'Approved';
                endif;
                if ($req->ReportType == 'rejected'):
                    $ReportType = 'Rejected';
                endif;
                if ($req->ReportType == 'pending'):
                    $ReportType = 'Pending';
                endif;
                $Data = ReportsHistory::join('users AS U', 'U.id', '=', 'reports_histories.user_id')->where(['U.assigned_to_accountant_id' => $user->id, 'reports_histories.approval_status' => $ReportType])->orderBy('reports_histories.updated_at', 'DESC')->get(['U.id AS UserID', 'U.name AS UserFullName', 'U.email AS UserEmail', 'U.contact_no AS UserContactNo', 'U.queries_submission_datetime AS QuerySubmissionDateTime', 'reports_histories.file AS ReportFileName', 'reports_histories.rejection_remarks AS RejectionRemarks']);

                if (count($Data) > 0):
                    foreach ($Data as $key => $item):
                        array_push($FinalArray, [
                            'UserID' => $item->UserID,
                            'UserFullName' => $item->UserFullName,
                            'UserEmail' => $item->UserEmail,
                            'UserContactNo' => $item->UserContactNo,
                            'QuerySubmissionDateTime' => $item->QuerySubmissionDateTime,
                            'ReportFileName' => url('uploads/' . $item->ReportFileName),
                            'RejectionRemarks' => $item->RejectionRemarks == null ? '' : $item->RejectionRemarks
                        ]);
                    endforeach;
                endif;
            endif;

            $response['status'] = true;
            $response['Data'] = $FinalArray;
        } catch (\Exception $ex) {
            $response['msg'] = $this->ExceptionMessage($ex);
        }
        return $response;
    }

    public function ExceptionMessage($ex)
    {
        return 'Exception | ' . $ex->getMessage() . ' On Line Number ' . $ex->getLine();
    }

    public function logout()
    {
        Auth::logout();
        \Session::forget('role_id');
        return redirect('/admin');

    }
}
