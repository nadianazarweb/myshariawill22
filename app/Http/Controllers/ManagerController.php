<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Appointment;
use App\Models\ReportsHistory;
use App\Models\AttemptedQuestionOption;
use Mail;
use Illuminate\Support\Facades\Storage;



class ManagerController extends Controller
{
    public function index()
    {
        $view = 'manager.dashboard';
        $user = $this->login_check();

        $ApprovedReportsCount = ReportsHistory::where(['reports_histories.approval_status' => 'Approved', 'reports_histories.sent_to_manager_id' => $user->id])->count();
        $PendingReportsCount = ReportsHistory::where(['reports_histories.approval_status' => 'Pending', 'reports_histories.sent_to_manager_id' => $user->id])->count();

        return view($view, compact('user', 'ApprovedReportsCount', 'PendingReportsCount'));
    }

    public function login_check()
    {
        if (Auth::check()) {
            $user = Auth()->user();
        }
        return $user;
    }

    public function reports()
    {
        $view = 'manager.reports';
        $user = $this->login_check();

        $FinalArray = array();

        // $AttemptedByUsers = AttemptedQuestionOption::join('users AS U', 'U.id', '=', 'attempted_question_options.user_id')
        //     ->where(['U.assigned_to_accountant_id' => $user->id])
        //     ->groupBy(['U.id', 'U.name', 'U.contact_no', 'U.email', 'U.assigned_to_accountant_id', 'U.is_locked'])->get(['U.id AS UserID', 'U.name AS UserFullName', 'U.queries_submission_datetime AS QueriesSubmissionDateTime', 'U.email AS UserEmail', 'U.contact_no AS ContactNo', 'U.assigned_to_accountant_id AS AssignedToAccountantID', 'U.is_locked AS IsLocked']);

        $AttemptedByUsers = ReportsHistory::join('users AS U', 'U.id', '=', 'reports_histories.user_id')->where(['reports_histories.sent_to_manager_id' => $user->id])
            ->groupBy(['U.id', 'U.name', 'U.contact_no', 'U.email', 'U.assigned_to_accountant_id', 'U.is_locked'])->get(['U.id AS UserID', 'U.name AS UserFullName', 'U.queries_submission_datetime AS QueriesSubmissionDateTime', 'U.email AS UserEmail', 'U.contact_no AS ContactNo', 'U.assigned_to_accountant_id AS AssignedToAccountantID', 'U.is_locked AS IsLocked']);

        foreach ($AttemptedByUsers as $AUKey => $AUItem) :
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
                    'U.email AS ManagerEmail'
                ]
            );

            $ReportIsNotRead = ReportsHistory::join('users AS U', 'U.id', '=', 'reports_histories.sent_to_manager_id')->where(['reports_histories.user_id' => $AUItem->UserID, 'reports_histories.is_read' => '0'])->count();

            $LastUserReportStatus = '';
            if (count($ReportsHistory) == 0) :
                $LastUserReportStatus = 'NoAction';
            else :
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
                'AccountantName' => $AUItem->AccountantName,
                'IsLocked' => $AUItem->IsLocked,
                'AttemptedAnswers' => [],
                'ReportsHistory' => $ReportsHistory->toArray(),
                'ReportIsNotRead' => $ReportIsNotRead
            ]);
        endforeach;

        foreach ($FinalArray as $FAKey => $FAItem) :
            $UserID = $FAItem['UserID'];
            $attempted_data = AttemptedQuestionOption::join('questions AS Q', 'Q.id', '=', 'attempted_question_options.question_id')
                ->where('attempted_question_options.user_id', $UserID)
                ->groupBy(['attempted_question_options.question_id', 'Q.title', 'Q.sort_id'])
                ->orderBy('Q.sort_id', 'ASC')
                ->get(['attempted_question_options.question_id AS QuestionID', 'Q.title AS QuestionTitle', 'Q.question_option_type_id AS ParentQuestionOptionTypeID', 'Q.sort_id AS ParentQuestionSortID']);
            if (count($attempted_data) > 0) :
                foreach ($attempted_data as $key => $item) :
                    array_push($FinalArray[$FAKey]['AttemptedAnswers'], ['QuestionID' => $item->QuestionID, 'QuestionTitle' => $item->QuestionTitle, 'ParentQuestionSortID' => $item->ParentQuestionSortID, 'ParentQuestionOptionTypeID' => $item->ParentQuestionOptionTypeID, 'AttemptedOptions' => []]);
                endforeach;
            endif;
        endforeach;

        foreach ($FinalArray as $FAKey => $FAItem) :
            $UserID = $FAItem['UserID'];
            foreach ($FAItem['AttemptedAnswers'] as $AKey => $AItem) :
                $QuestionID = $AItem['QuestionID'];

                $attempted_options = AttemptedQuestionOption::join('options AS O', 'O.id', '=', 'attempted_question_options.option_id', 'left')
                    ->where(['attempted_question_options.user_id' => $UserID, 'attempted_question_options.question_id' => $QuestionID])
                    ->groupBy(['attempted_question_options.option_id', 'O.title', 'attempted_question_options.text_value'])
                    ->get(['attempted_question_options.option_id AS OptionID', 'O.title AS OptionTitle', 'attempted_question_options.text_value AS TextValue']);

                if (count($attempted_options) > 0) :
                    foreach ($attempted_options as $AOKey => $AOItem) :
                        $OptionID = $AOItem->OptionID;
                        $OptionTitle = $AOItem->OptionTitle;
                        $TextValue = $AOItem->TextValue;

                        array_push($FinalArray[$FAKey]['AttemptedAnswers'][$AKey]['AttemptedOptions'], ['OptionID' => $OptionID, 'OptionTitle' => $OptionTitle, 'TextValue' => $TextValue, 'RelatedQuestions' => []]);

                    endforeach;
                endif;

            endforeach;
        endforeach;

        foreach ($FinalArray as $FAKey => $FAItem) :
            $UserID = $FAItem['UserID'];
            foreach ($FAItem['AttemptedAnswers'] as $AKey => $AItem) :
                foreach ($AItem['AttemptedOptions'] as $AOKey => $AOItem) :

                    $OptionID = $AOItem['OptionID'];
                    if ($OptionID != "") :

                        $attempted_related_questions = AttemptedQuestionOption::join('related_questions AS RQ', 'RQ.id', '=', 'attempted_question_options.related_question_id', 'left')
                            ->where(['attempted_question_options.user_id' => $UserID, 'attempted_question_options.option_id' => $OptionID])
                            ->where('attempted_question_options.related_question_id', '<>', 'NULL')
                            ->groupBy(['attempted_question_options.related_question_id', 'RQ.sort_id', 'RQ.title', 'attempted_question_options.text_value'])
                            ->orderBy('RQ.sort_id', 'ASC')
                            ->get(['attempted_question_options.related_question_id AS RelatedQuestionID', 'RQ.sort_id AS RelatedQuestionSortID', 'RQ.question_option_type_id AS RelatedQuestionOptionTypeID', 'RQ.title AS RelatedQuestionTitle']);

                        if (count($attempted_related_questions) > 0) :
                            foreach ($attempted_related_questions as $ARQKey => $ARQItem) :

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


        foreach ($FinalArray as $FAKey => $FAItem) :
            $UserID = $FAItem['UserID'];
            foreach ($FAItem['AttemptedAnswers'] as $AKey => $AItem) :
                foreach ($AItem['AttemptedOptions'] as $AOKey => $AOItem) :
                    if (count($AOItem['RelatedQuestions']) > 0) :
                        foreach ($AOItem['RelatedQuestions'] as $RQKey => $RQItem) :

                            $RelatedQuestionID = $RQItem['RelatedQuestionID'];

                            $attempted_related_options = AttemptedQuestionOption::join('related_options AS RO', 'RO.id', '=', 'attempted_question_options.related_option_id', 'left')
                                ->where(['attempted_question_options.related_question_id' => $RelatedQuestionID, 'attempted_question_options.user_id' => $UserID])
                                ->get(['attempted_question_options.related_option_id AS RelatedOptionID', 'RO.title AS RelatedOptionTitle', 'attempted_question_options.related_text_value AS RelatedTextValue']);

                            if (count($attempted_related_options) > 0) :

                                foreach ($attempted_related_options as $AROKey => $AROItem) :
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


        $ReportsHistoryData = ReportsHistory::join('users AS U', 'U.id', '=', 'reports_histories.user_id')->where(['reports_histories.sent_to_manager_id' => $user->id, 'approval_status' => 'Pending'])->orderBy('reports_histories.created_at', 'ASC')->get(['reports_histories.id', 'reports_histories.folder', 'reports_histories.file', 'reports_histories.created_at', 'U.id AS UserID', 'U.username']);
        return view($view, compact('user', 'ReportsHistoryData', 'FinalArray'));
    }

    public function approved_reports()
    {
        $view = 'manager.approved_reports';
        $user = $this->login_check();

        $FinalArray = array();

        $AttemptedByUsers = ReportsHistory::join('users AS U', 'U.id', '=', 'reports_histories.user_id')->where(['reports_histories.sent_to_manager_id' => $user->id])
            ->groupBy(['U.id', 'U.name', 'U.contact_no', 'U.email', 'U.assigned_to_accountant_id', 'U.is_locked'])->get(['U.id AS UserID', 'U.name AS UserFullName', 'U.queries_submission_datetime AS QueriesSubmissionDateTime', 'U.email AS UserEmail', 'U.contact_no AS ContactNo', 'U.assigned_to_accountant_id AS AssignedToAccountantID', 'U.is_locked AS IsLocked']);

            foreach ($AttemptedByUsers as $AUKey => $AUItem) :
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
                        'U.email AS ManagerEmail'
                    ]
                );

                $ReportIsNotRead = ReportsHistory::join('users AS U', 'U.id', '=', 'reports_histories.sent_to_manager_id')->where(['reports_histories.user_id' => $AUItem->UserID, 'reports_histories.is_read' => '0'])->count();

                $LastUserReportStatus = '';
                if (count($ReportsHistory) == 0) :
                    $LastUserReportStatus = 'NoAction';
                else :
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
                    'AccountantName' => $AUItem->AccountantName,
                    'IsLocked' => $AUItem->IsLocked,
                    'AttemptedAnswers' => [],
                    'ReportsHistory' => $ReportsHistory->toArray(),
                    'ReportIsNotRead' => $ReportIsNotRead
                ]);
            endforeach;

            foreach ($FinalArray as $FAKey => $FAItem) :
                $UserID = $FAItem['UserID'];
                $attempted_data = AttemptedQuestionOption::join('questions AS Q', 'Q.id', '=', 'attempted_question_options.question_id')
                    ->where('attempted_question_options.user_id', $UserID)
                    ->groupBy(['attempted_question_options.question_id', 'Q.title', 'Q.sort_id'])
                    ->orderBy('Q.sort_id', 'ASC')
                    ->get(['attempted_question_options.question_id AS QuestionID', 'Q.title AS QuestionTitle', 'Q.question_option_type_id AS ParentQuestionOptionTypeID', 'Q.sort_id AS ParentQuestionSortID']);
                if (count($attempted_data) > 0) :
                    foreach ($attempted_data as $key => $item) :
                        array_push($FinalArray[$FAKey]['AttemptedAnswers'], ['QuestionID' => $item->QuestionID, 'QuestionTitle' => $item->QuestionTitle, 'ParentQuestionSortID' => $item->ParentQuestionSortID, 'ParentQuestionOptionTypeID' => $item->ParentQuestionOptionTypeID, 'AttemptedOptions' => []]);
                    endforeach;
                endif;
            endforeach;

            foreach ($FinalArray as $FAKey => $FAItem) :
                $UserID = $FAItem['UserID'];
                foreach ($FAItem['AttemptedAnswers'] as $AKey => $AItem) :
                    $QuestionID = $AItem['QuestionID'];

                    $attempted_options = AttemptedQuestionOption::join('options AS O', 'O.id', '=', 'attempted_question_options.option_id', 'left')
                        ->where(['attempted_question_options.user_id' => $UserID, 'attempted_question_options.question_id' => $QuestionID])
                        ->groupBy(['attempted_question_options.option_id', 'O.title', 'attempted_question_options.text_value'])
                        ->get(['attempted_question_options.option_id AS OptionID', 'O.title AS OptionTitle', 'attempted_question_options.text_value AS TextValue']);

                    if (count($attempted_options) > 0) :
                        foreach ($attempted_options as $AOKey => $AOItem) :
                            $OptionID = $AOItem->OptionID;
                            $OptionTitle = $AOItem->OptionTitle;
                            $TextValue = $AOItem->TextValue;

                            array_push($FinalArray[$FAKey]['AttemptedAnswers'][$AKey]['AttemptedOptions'], ['OptionID' => $OptionID, 'OptionTitle' => $OptionTitle, 'TextValue' => $TextValue, 'RelatedQuestions' => []]);

                        endforeach;
                    endif;

                endforeach;
            endforeach;

            foreach ($FinalArray as $FAKey => $FAItem) :
                $UserID = $FAItem['UserID'];
                foreach ($FAItem['AttemptedAnswers'] as $AKey => $AItem) :
                    foreach ($AItem['AttemptedOptions'] as $AOKey => $AOItem) :

                        $OptionID = $AOItem['OptionID'];
                        if ($OptionID != "") :

                            $attempted_related_questions = AttemptedQuestionOption::join('related_questions AS RQ', 'RQ.id', '=', 'attempted_question_options.related_question_id', 'left')
                                ->where(['attempted_question_options.user_id' => $UserID, 'attempted_question_options.option_id' => $OptionID])
                                ->where('attempted_question_options.related_question_id', '<>', 'NULL')
                                ->groupBy(['attempted_question_options.related_question_id', 'RQ.sort_id', 'RQ.title', 'attempted_question_options.text_value'])
                                ->orderBy('RQ.sort_id', 'ASC')
                                ->get(['attempted_question_options.related_question_id AS RelatedQuestionID', 'RQ.sort_id AS RelatedQuestionSortID', 'RQ.question_option_type_id AS RelatedQuestionOptionTypeID', 'RQ.title AS RelatedQuestionTitle']);

                            if (count($attempted_related_questions) > 0) :
                                foreach ($attempted_related_questions as $ARQKey => $ARQItem) :

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

            foreach ($FinalArray as $FAKey => $FAItem) :
                $UserID = $FAItem['UserID'];
                foreach ($FAItem['AttemptedAnswers'] as $AKey => $AItem) :
                    foreach ($AItem['AttemptedOptions'] as $AOKey => $AOItem) :
                        if (count($AOItem['RelatedQuestions']) > 0) :
                            foreach ($AOItem['RelatedQuestions'] as $RQKey => $RQItem) :

                                $RelatedQuestionID = $RQItem['RelatedQuestionID'];

                                $attempted_related_options = AttemptedQuestionOption::join('related_options AS RO', 'RO.id', '=', 'attempted_question_options.related_option_id', 'left')
                                    ->where(['attempted_question_options.related_question_id' => $RelatedQuestionID, 'attempted_question_options.user_id' => $UserID])
                                    ->get(['attempted_question_options.related_option_id AS RelatedOptionID', 'RO.title AS RelatedOptionTitle', 'attempted_question_options.related_text_value AS RelatedTextValue']);

                                if (count($attempted_related_options) > 0) :

                                    foreach ($attempted_related_options as $AROKey => $AROItem) :
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
        $ReportsHistoryData = ReportsHistory::join('users AS U', 'U.id', '=', 'reports_histories.user_id')->where(['reports_histories.sent_to_manager_id' => $user->id, 'approval_status' => 'Approved'])->orderBy('reports_histories.created_at', 'ASC')->get(['reports_histories.id', 'reports_histories.folder', 'reports_histories.file', 'reports_histories.created_at', 'U.id AS user_id', 'U.username', 'U.contact_no']);
        return view($view, compact('user', 'ReportsHistoryData','FinalArray'));
    }

    public function update_approval_status(Request $req)
    {
        $ResponseMSG = '';
        $ResponseStatus = '';

        try {
            if ($req->approval_status == "approved") :

                $UserDetails = ReportsHistory::join('users AS U', 'U.id', '=', 'reports_histories.user_id')->where('reports_histories.id', $req->report_history_id)->first(['U.name AS UserFullName', 'U.email AS UserEmail', 'reports_histories.folder', 'reports_histories.file']);

                if ($UserDetails != null) :
                    $UserEmail = $UserDetails->UserEmail;
                    $UserFullName = $UserDetails->UserFullName;
                    $url = $UserDetails->folder . '/' . $UserDetails->file;


                    $this->send_report_approved_email($url, $UserEmail, $UserFullName);
                    $ReportHistoryUpdate = ReportsHistory::find($req->report_history_id);
                    $ReportHistoryUpdate->approval_status = 'Approved';
                    $ReportHistoryUpdate->approval_status_datetime = date('Y-m-d h:i:s');
                    $ReportHistoryUpdate->is_read = 0;
                    if ($ReportHistoryUpdate->save()) :
                        $ResponseMSG = 'Report Has Been Approved Successfully';
                        $ResponseStatus = 'success_msg';
                    else :
                        $ResponseMSG = 'Some Error Occured';
                        $ResponseStatus = 'failure_msg';
                    endif;

                endif;

            endif;

            if ($req->approval_status == "rejected") :
                $ReportHistoryUpdate = ReportsHistory::find($req->report_history_id);
                $ReportHistoryUpdate->approval_status = 'Rejected';
                $ReportHistoryUpdate->approval_status_datetime = date('Y-m-d h:i:s');
                $ReportHistoryUpdate->is_read = 0;
                $ReportHistoryUpdate->rejection_remarks = $req->rejection_remarks;
                if ($ReportHistoryUpdate->save()) :
                    $ResponseMSG = 'Report Has Been Rejected';
                    $ResponseStatus = 'success_msg';
                else :
                    $ResponseMSG = 'Some Error Occured';
                    $ResponseStatus = 'failure_msg';
                endif;
            endif;
        } catch (\Exception $ex) {
            $ResponseStatus = 'failure_msg';
            $ResponseMSG = $this->ExceptionMessage($ex);
        }
        return redirect()->route('manager_reports')->with($ResponseStatus, $ResponseMSG);
    }



    public function update_appointment_approval_status(Request $req)
    {
        $ResponseMSG = '';
        $ResponseStatus = '';

        try {
            if ($req->approval_status == "approved") :

                $UserDetails = Appointment::join('users AS U', 'U.id', '=', 'appointments.user_id')->where('appointments.id', $req->appointment_id)->first(['U.email AS UserEmail', 'appointments.for_date', 'appointments.for_time_slot_start', 'appointments.for_time_slot_end']);

                if ($UserDetails != null) :

                    $this->send_appointment_approved_email($UserDetails);

                    $AppointmentUpdate = Appointment::find($req->appointment_id);
                    $AppointmentUpdate->approval_status = 'Approved';
                    if ($AppointmentUpdate->save()) :
                        $ResponseMSG = 'Appointment Has Been Approved Successfully';
                        $ResponseStatus = 'success_msg';
                    else :
                        $ResponseMSG = 'Some Error Occured';
                        $ResponseStatus = 'failure_msg';
                    endif;

                endif;

            endif;

            if ($req->approval_status == "rejected") :
                $ReportHistoryUpdate = ReportsHistory::find($req->report_history_id);
                $ReportHistoryUpdate->approval_status = 'Rejected';
                $ReportHistoryUpdate->approval_status_datetime = date('Y-m-d h:i:s');
                $ReportHistoryUpdate->is_read = 0;
                $ReportHistoryUpdate->rejection_remarks = $req->rejection_remarks;
                if ($ReportHistoryUpdate->save()) :
                    $ResponseMSG = 'Report Has Been Rejected';
                    $ResponseStatus = 'success_msg';
                else :
                    $ResponseMSG = 'Some Error Occured';
                    $ResponseStatus = 'failure_msg';
                endif;
            endif;
        } catch (\Exception $ex) {
            $ResponseStatus = 'failure_msg';
            $ResponseMSG = $this->ExceptionMessage($ex);
        }
        return redirect()->route('manager_appointments')->with($ResponseStatus, $ResponseMSG);
    }

    public function send_email(Request $req)
    {
        $ResponseMSG = '';
        $ResponseStatus = '';
        try {

            $UserDetails = ReportsHistory::join('users AS U', 'U.id', '=', 'reports_histories.user_id')->where(['reports_histories.approval_status' => 'Approved', 'reports_histories.user_id' => $req->user_id])->first(['U.name AS UserFullName', 'U.email AS UserEmail', 'reports_histories.folder', 'reports_histories.file']);

            $UserEmail = $UserDetails->UserEmail;
            $UserFullName = $UserDetails->UserFullName;
            $url = $UserDetails->folder . '/' . $UserDetails->file;
            $this->send_report_approved_email($url, $UserEmail, $UserFullName);

            $ResponseStatus = 'success_msg';
            $ResponseMSG = 'Email has been sent to the customer';
        } catch (\Exception $ex) {
            $ResponseStatus = 'failure_msg';
            $ResponseMSG = $this->ExceptionMessage($ex);
        }
        return redirect()->route('manager_approved_reports')->with($ResponseStatus, $ResponseMSG);
    }

    public function send_report_approved_email($url, $UserEmail, $UserFullName)
    {
        // $url = str_replace("/","\\",$url);
        // $HowToFile = asset('HOW_TO_SIGN_YOUR_WILL.docx');
        // $HowToFile = public_path('assets/HOW_TO_SIGN_YOUR_WILL.docx');
        // $Report = public_path($url);

        $HowToFile = Storage::disk('reports')->path('HOW_TO_SIGN_YOUR_WILL.docx');
        $Report = Storage::disk('reports')->path($url);

        $Extension = pathinfo($Report, PATHINFO_EXTENSION);
        $MimeType = Storage::disk('reports')->mimeType($url);

        Mail::send('emails.report_approved', compact('url', 'UserFullName'), function ($message) use ($UserEmail, $HowToFile, $Report, $Extension, $MimeType) {
            $message->to($UserEmail);
            $message->from(config('mail.from.address'));
            $message->subject('Your Report Is Ready');
            $message->attach($HowToFile, [
                'as' => 'HOW_TO_SIGN_YOUR_WILL.docx',
                'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]);
            $message->attach($Report, [
                'as' => 'Report.' . $Extension,
                'mime' => $MimeType,
            ]);
        });
    }

    public function send_appointment_approved_email($UserDetails)
    {
        $UserEmail = $UserDetails->UserEmail;
        $ForDate = $UserDetails->for_date;
        $TimeSlotStart = $UserDetails->for_time_slot_start;
        $TimeSlotEnd = $UserDetails->for_time_slot_end;
        Mail::send('emails.appointment_approved', compact('ForDate', 'TimeSlotStart', 'TimeSlotEnd'), function ($message) use ($UserEmail) {
            $message->to($UserEmail);
            $message->from(config('mail.from.address'));
            $message->subject('Your Appointment Has Been Approved');
        });
    }

    public function send_mark_as_arrived_email($UserDetails)
    {
        $UserEmail = $UserDetails->UserEmail;
        Mail::send('emails.mark_as_arrived', [], function ($message) use ($UserEmail) {
            $message->to($UserEmail);
            $message->from(config('mail.from.address'));
            $message->subject('Thank you for being punctual');
        });
    }

    public function appointments()
    {
        $view = 'manager.appointments';
        $user = $this->login_check();

        $AppointmentData = Appointment::join('users AS U', 'U.id', '=', 'appointments.user_id')->where(['appointments.for_manager_id' => $user->id])->orderBy('appointments.for_date', 'DESC')->orderBy('appointments.has_arrived', 'ASC')->get(['U.name', 'U.email', 'U.contact_no', 'appointments.id', 'appointments.for_date', 'appointments.for_time_slot_start', 'appointments.for_time_slot_end', 'appointments.has_arrived']);

        $statusCounts = array_count_values(array_column(json_decode(json_encode($AppointmentData), true), 'has_arrived'));
        $notarrivedCount = $statusCounts['0'] ?? 0;
        $arrivedCount = $statusCounts['1'] ?? 0;


        $GetAppointmentsRequest = new Request;
        $GetAppointmentsRequest->Month = date('m');
        $GetAppointmentsRequest->Year = date('Y');
        $AppointmentData = $this->get_appointments($GetAppointmentsRequest);

        return view($view, compact('user', 'AppointmentData', 'notarrivedCount', 'arrivedCount'));
    }

    public function get_appointments(Request $req)
    {
        $user = $this->login_check();
        return $AppointmentData = Appointment::join('users AS U', 'U.id', '=', 'appointments.user_id')->where(['appointments.for_manager_id' => $user->id])->whereYear('appointments.for_date', $req->Year)->whereMonth('appointments.for_date', $req->Month)->orderBy('appointments.for_date', 'DESC')->orderBy('appointments.has_arrived', 'ASC')->get(['U.name', 'U.email', 'U.contact_no', 'appointments.id', 'appointments.for_date', 'appointments.for_time_slot_start', 'appointments.for_time_slot_end', 'appointments.has_arrived', 'appointments.approval_status']);
    }

    public function mark_as_arrived(Request $req)
    {
        $ResponseMSG = '';
        $ResponseStatus = '';
        try {

            $UserDetails = Appointment::join('users AS U', 'U.id', '=', 'appointments.user_id')->where('appointments.id', $req->appointment_id)->first(['U.email AS UserEmail']);

            if ($UserDetails != null) :
                $this->send_mark_as_arrived_email($UserDetails);
                $MarkArrived = Appointment::find($req->appointment_id);
                $MarkArrived->has_arrived = '1';
                if ($MarkArrived->save()) :
                    $ResponseStatus = 'success_msg';
                    $ResponseMSG = 'Marked As Arrived Successfully';
                else :
                    $ResponseStatus = 'failure_msg';
                    $ResponseMSG = 'Some Error Occured';
                endif;
            endif;
        } catch (\Exception $ex) {
            $ResponseStatus = 'failure_msg';
            $ResponseMSG = $this->ExceptionMessage($ex);
        }
        return redirect()->back()->with($ResponseStatus, $ResponseMSG);
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
