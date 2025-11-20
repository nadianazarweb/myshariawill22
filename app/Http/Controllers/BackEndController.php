<?php

namespace App\Http\Controllers;

use App\Models\ReportsHistory;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Level;
use App\PlanType;
use App\Report;
use App\Bank;
use App\BankBranchName;
use App\BankDistrict;
use App\BankRequest;
use App\Company;
use App\CreditRequest;
use App\MobileBankingRequest;
use App\MobileRecharge;
use App\Operator;
use App\Service;
use App\Form;
use App\Models\AttemptedQuestionOption;
use App\Models\Question;
use App\Models\QuestionOptionType;
use App\Models\Option;
use App\Models\RelatedQuestion;
use App\Models\RelatedOption;
use App\Models\QuestionnaireSection;
use App\Models\QuestionForGender;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use PDO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class BackEndController extends Controller
{
    public function index()
    {
        $view = 'login';
        return view($view);
    }

    public function login_check()
    {
        if (Auth::check()) {
            $user = Auth()->user();
        }
        return $user;
    }

    public function roles()
    {
        return \TCG\Voyager\Models\Role::where('id', '!=', 1)->get();
    }

    public function login_submit(Request $request)
    {
        if (isset($request->email)) {
            $findemail = User::where('email', $request->email)->first();
            if (isset($findemail->id)) {
                if ($findemail->status == 1) {
                    if (Hash::check($request->password, $findemail->password)) {
                        Auth::loginUsingId($findemail->id);
                        $request->session()->put('role_id', $findemail->role_id);
                        $redirecting = '/dashboard/home';
                        if ($findemail->role_id == 7):
                            $redirecting = '/accountant/dashboard';
                        elseif ($findemail->role_id == 8):
                                $redirecting = '/manager/dashboard';
                        endif;
                        return response()->json([
                            "status" => "success",
                            "message" => "Please wait we are redirecting you",
                            "redirect" => $redirecting
                        ]);
                    } else {
                        return response()->json([
                            "status" => "danger",
                            "message" => "Incorrect password",
                        ]);
                    }
                } else {
                    return response()->json([
                        "status" => "warning",
                        "message" => "Sorry! Your account is blocked. Please contact support",
                    ]);
                }
            }
            $findusername = User::where('username', $request->email)->first();
            if (isset($findusername->id)) {
                if ($findusername->status == 1) {
                    if (Hash::check($request->password, $findusername->password)) {
                        Auth::loginUsingId($findusername->id);
                        return response()->json([
                            "status" => "success",
                            "message" => "Please wait we are redirecting you",
                            "redirect" => "/dashboard/home"
                        ]);
                    } else {
                        return response()->json([
                            "status" => "danger",
                            "message" => "Incorrect password",
                        ]);
                    }
                } else {
                    return response()->json([
                        "status" => "warning",
                        "message" => "Sorry! Your account is blocked. Please contact support",
                    ]);
                }
            }
            $findcontact = User::where('contact_no', $request->email)->first();
            if (isset($findcontact->id)) {
                if ($findcontact->status == 1) {
                    if (Hash::check($request->password, $findcontact->password)) {
                        Auth::loginUsingId($findcontact->id);
                        $redirecting = '/dashboard/home';
                        if ($findcontact->role_id == 7):
                            $redirecting = '/accountant/dashboard/home';
                        endif;
                        return response()->json([
                            "status" => "success",
                            "message" => "Please wait we are redirecting you",
                            "redirect" => $redirecting
                        ]);
                    } else {
                        return response()->json([
                            "status" => "danger",
                            "message" => "Incorrect password",
                        ]);
                    }
                } else {
                    return response()->json([
                        "status" => "warning",
                        "message" => "Sorry! Your account is blocked. Please contact support",
                    ]);
                }
            } else {
                return response()->json([
                    "status" => "danger",
                    "message" => "Sorry! we cannot find any account with your details",
                ]);
            }
        }
    }

    public function dashboard()
    {
        $view = 'dashboard';
        $user = $this->login_check();
        if ($user->role_id == 2 || $user->role_id == 1) {
            $latestusers = User::take(10)->orderBy('id', 'DESC')->get();
        } else {
            $latestusers = User::take(10)->where('created_by', $user->id)->orderBy('id', 'DESC')->get();
        }
        $UsersCount = User::where(['status'=>'1'])->where('role_id','<>','1')->count();
        $AssignedFormsCount = User::where(['status'=>'1'])->where('assigned_to_accountant_id','<>','NULL')->count();
        $TotalForms = DB::table('attempted_question_options')
        ->select(DB::raw('COUNT(A.user_id) AS TotalForms'))
        ->fromSub(function ($query) {
            $query->select('user_id')
                ->from('attempted_question_options')
                ->groupBy('user_id');
        }, 'A')
        ->first()->TotalForms;
        return view($view, compact('user', 'UsersCount','AssignedFormsCount','TotalForms'));
    }

    public function all_forms()
    {
        $view = 'all_forms';
        $user = $this->login_check();
        $FinalArray = array();

        $AttemptedByUsers = AttemptedQuestionOption::join('users AS U', 'U.id', '=', 'attempted_question_options.user_id')
            ->join('users AS A', 'A.id', '=', 'U.assigned_to_accountant_id', 'left')
            ->groupBy(['U.id', 'U.name', 'U.email', 'U.contact_no', 'U.assigned_to_accountant_id', 'A.name', 'U.is_locked'])->get(['U.id AS UserID', 'U.name AS UserFullName', 'U.email AS UserEmail', 'U.contact_no AS ContactNo', 'A.username AS AccountantUserName', 'A.name AS AccountantName', 'U.assigned_to_accountant_id AS AssignedToAccountantID', 'U.assigned_to_accountant_date AS AssignedToAccountantDate', 'U.is_locked AS IsLocked']);

        foreach ($AttemptedByUsers as $AUKey => $AUItem):
            $ReportsHistory = ReportsHistory::join('users AS U','U.id','=','reports_histories.sent_to_manager_id')->where(['reports_histories.user_id'=>$AUItem->UserID])->orderBy('reports_histories.id','DESC')->get(
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
                    'U.email AS ManagerEmail']);

                    $LastUserReportStatus = '';
                    if(count($ReportsHistory)==0):
                        $LastUserReportStatus = 'NoAction';
                    else:
                        $LastUserReportStatus = $ReportsHistory[0]->approval_status;
                    endif;

            array_push($FinalArray, [
                'UserID' => $AUItem->UserID,
                'UserFullName' => $AUItem->UserFullName,
                'UserEmail' => $AUItem->UserEmail,
                'ContactNo' => $AUItem->ContactNo,
                'AssignedToAccountantID' => $AUItem->AssignedToAccountantID,
                'AssignedToAccountantDate' => $AUItem->AssignedToAccountantDate,
                'AccountantUserName' => $AUItem->AccountantUserName,
                'AccountantName' => $AUItem->AccountantName,
                'IsLocked' => $AUItem->IsLocked,
                'ApprovalStatus'=>$LastUserReportStatus,
                'AttemptedAnswers' => [],
                'ReportsHistory'=>$ReportsHistory
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

        $AccountantData = User::where(['role_id'=>7])->get();


        return view($view, compact('user', 'FinalArray','AccountantData'));
    }

    public function assign_to_accountant(Request $req){
        $ResponseMSG = '';
        $ResponseStatus = '';
        try{
            if(User::where(['id'=>$req->user_id])->update(['assigned_to_accountant_id'=>$req->accountant_id,'assigned_to_accountant_date'=>date('Y-m-d h:i:s')])):
                $ResponseMSG = 'Assigned Successfully';
                $ResponseStatus = 'success_msg';
            endif;
        }catch(\Exception $ex){
            $ResponseMSG = $this->ExceptionMessage($ex);
            $ResponseStatus = 'failure_msg';
        }
        return redirect()->route('admin_all_forms')->with($ResponseStatus, $ResponseMSG);
    }

    public function users(Request $request, $refkey)
    {

        $view = 'users';

        $user = $this->login_check();
        $roles = $this->roles();
        $finduser = User::where('ref_key', $refkey)->first();
        if (!isset($request->role)) {
            if (isset($finduser)) {
                if ($finduser->role_id == 2 || $finduser->role_id == 1) {
                    $alluser = User::orderBy('id', 'DESC')->paginate(10);
                } else {
                    $alluser = User::where('created_by', $finduser->id)->orderBy('id', 'DESC')->paginate(10);
                }
            } else {
                return redirect('/dashboard/home');
            }
        } else {
            if (isset($finduser)) {
                if ($finduser->role_id == 2 || $finduser->role_id == 1) {
                    $alluser = User::orderBy('id', 'DESC')->where('role_id', $request->role)->paginate(10);
                } else {
                    $alluser = User::where('created_by', $finduser->id)->where('role_id', $request->role)->orderBy('id', 'DESC')->paginate(10);
                }
            } else {
                return redirect('/dashboard/home');
            }
        }
        return view($view, compact('user', 'alluser', 'roles'));
    }

    public function check_username(Request $request)
    {
        if (isset($request->username)) {
            $findusername = User::where('username', $request->username)->count();
            if ($findusername > 0) {
                return response()->json([
                    "status" => "warning",
                    "message" => "Username already exists"
                ]);
            } else {
                return response()->json([
                    "status" => "success",
                    "message" => "Username available"
                ]);
            }
        } else if (isset($request->email)) {
            $findemail = User::where('email', $request->email)->count();
            if ($findemail > 0) {
                return response()->json([
                    "status" => "warning",
                    "message" => "Email already exists"
                ]);
            } else {
                return response()->json([
                    "status" => "success",
                    "message" => "Email available"
                ]);
            }
        } else if (isset($request->number)) {
            $findcontact = User::where('contact_no', $request->number)->count();
            if ($findcontact > 0) {
                return response()->json([
                    "status" => "warning",
                    "message" => "Contact Number already exists"
                ]);
            } else {
                return response()->json([
                    "status" => "success",
                    "message" => "Contact Number available"
                ]);
            }
        }
    }

    public function user_submit(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->contact_no = $request->phone;
        $user->password = bcrypt($request->password);
        $user->role_id = $request->role;
        $user->created_by = Auth()->user()->id;
        $user->status = 1;
        if ($request->role == 4) {
            $user->level = $request->level;
        }
        $user->save();
        return response()->json([
            "status" => "success",
            "message" => "User registered successfully"
        ]);
    }

    public function profile(Request $request)
    {
        $view = 'profile';
        $roles = $this->roles();
        $user = $this->login_check();
        return view($view, compact('user', 'roles'));
    }

    public function profile_update_submit(Request $request)
    {
        $user = $this->login_check();
        if (isset($request->name)) {
            $user->name = $request->name;
        }
        if (isset($request->email)) {
            $user->email = $request->email;
        }
        if (isset($request->password) && isset($request->currpassword)) {
            if (Hash::check($request->currpassword, $user->password)) {
                $user->password = bcrypt($request->password);
            } else {
                return response()->json([
                    "status" => 'warning',
                    "message" => "Sorry! The current password is incorrect."
                ]);
            }
        }
        if (isset($request->number)) {
            $user->contact_no = $request->number;
        }
        return response()->json([
            "status" => "success",
            "message" => "Profile has been updated"
        ]);
    }

    public function deactive_account($userid)
    {
        $finduser = User::find($userid);
        if (isset($finduser->id)) {
            $finduser->status = 0;
            $finduser->save();
        }
        return response()->json([
            "status" => "success"
        ]);
    }

    public function active_account($userid)
    {
        $finduser = User::find($userid);
        if (isset($finduser->id)) {
            $finduser->status = 1;
            $finduser->save();
        }
        return response()->json([
            "status" => "success"
        ]);
    }

    public function questions()
    {
        $ArrayToPass = [];
        $QuestionsData = Question::join('questionnaire_sections AS QS','QS.id','=','questions.questionnaire_section_id')->join('question_for_genders AS QFG','QFG.id','=','questions.question_for_gender_id')->orderBy('questions.sort_id', 'ASC')->get(['questions.id AS ParentQuestionID', 'questions.title AS ParentQuestionTitle','questions.question_option_type_id AS ParentQuestionOptionTypeID','QS.title AS ParentQuestionnaireSectionTitle','QFG.title AS ParentQuestionForGenderTitle']);

        if (count($QuestionsData) > 0):

            foreach ($QuestionsData as $QDKey => $QDItem):
                $ParentQuestionID = $QDItem->ParentQuestionID;
                $ParentQuestionTitle = $QDItem->ParentQuestionTitle;
                $ParentQuestionOptionTypeID = $QDItem->ParentQuestionOptionTypeID;
                $ParentQuestionnaireSectionTitle = $QDItem->ParentQuestionnaireSectionTitle;
                $ParentQuestionForGenderTitle = $QDItem->ParentQuestionForGenderTitle;
                array_push($ArrayToPass, array(
                    'ParentQuestionID' => $ParentQuestionID,
                    'ParentQuestionTitle' => $ParentQuestionTitle,
                    'ParentQuestionOptionTypeID' => $ParentQuestionOptionTypeID,
                    'ParentQuestionnaireSectionTitle' => $ParentQuestionnaireSectionTitle,
                    'ParentQuestionForGenderTitle' => $ParentQuestionForGenderTitle,
                    'Options' => array()
                ));
            endforeach;

            foreach ($ArrayToPass as $ATPKey => $ATPItem):
                $ParentQuestionID = $ATPItem['ParentQuestionID'];
                $ParentQuestionTitle = $ATPItem['ParentQuestionTitle'];

                $Options = Option::where(['question_id' => $ParentQuestionID])->orderBy('sort_id', 'ASC')->get(['id AS ParentOptionID', 'title AS ParentOptionTitle']);

                if (count($Options) > 0):
                    foreach ($Options as $OKey => $OItem):
                        array_push($ArrayToPass[$ATPKey]['Options'], [
                            'ParentOptionID' => $OItem->ParentOptionID,
                            'ParentOptionTitle' => $OItem->ParentOptionTitle,
                            'RelatedQuestions' => array()
                        ]);
                    endforeach;
                endif;

            endforeach;

            foreach ($ArrayToPass as $ATPKey => $ATPItem):
                if (count($ATPItem['Options']) > 0):
                    foreach ($ATPItem['Options'] as $OKey => $OItem):
                        $ParentOptionID = $OItem['ParentOptionID'];
                        $RelatedQuestions = RelatedQuestion::where(['option_id' => $ParentOptionID])->orderBy('sort_id', 'ASC')->get(['id AS RelatedQuestionID', 'title AS RelatedQuestionTitle','question_option_type_id AS RelatedQuestionOptionTypeID','incre_decre_data_for','incre_decre_get_data']);

                        if (count($RelatedQuestions) > 0):

                            foreach ($RelatedQuestions as $RQKey => $RQItem):

                                $RelatedQuestionID = $RQItem->RelatedQuestionID;
                                $RelatedQuestionTitle = $RQItem->RelatedQuestionTitle;
                                $RelatedQuestionOptionTypeID = $RQItem->RelatedQuestionOptionTypeID;
                                $IncreDecreDataFor = $RQItem->incre_decre_data_for;
                                $IncreDecreGetData = $RQItem->incre_decre_get_data;

                                array_push($ArrayToPass[$ATPKey]['Options'][$OKey]['RelatedQuestions'], [
                                    'RelatedQuestionID' => $RelatedQuestionID,
                                    'RelatedQuestionTitle' => $RelatedQuestionTitle,
                                    'RelatedQuestionOptionTypeID' => $RelatedQuestionOptionTypeID,
                                    'IncreDecreDataFor' => $IncreDecreDataFor,
                                    'IncreDecreGetData' => $IncreDecreGetData,
                                    'RelatedOptions' => array()
                                ]);

                            endforeach;

                        endif;



                    endforeach;
                endif;
            endforeach;

            foreach ($ArrayToPass as $ATPKey => $ATPItem):

                if (count($ATPItem['Options']) > 0):
                    foreach ($ATPItem['Options'] as $OKey => $OItem):
                        if (count($OItem['RelatedQuestions']) > 0):
                            foreach ($OItem['RelatedQuestions'] as $RQKey => $RQItem):
                                $RelatedQuestionID = $RQItem['RelatedQuestionID'];

                                $RelatedOptions = RelatedOption::where(['related_question_id' => $RelatedQuestionID])->orderBy('sort_id', 'ASC')->get(['id AS RelatedOptionID', 'title AS RelatedOptionTitle']);

                                if (count($RelatedOptions) > 0):
                                    foreach ($RelatedOptions as $ROKey => $ROItem):
                                        $RelatedOptionID = $ROItem->RelatedOptionID;
                                        $RelatedOptionTitle = $ROItem->RelatedOptionTitle;

                                        array_push($ArrayToPass[$ATPKey]['Options'][$OKey]['RelatedQuestions'][$RQKey]['RelatedOptions'], [
                                            'RelatedOptionID' => $RelatedOptionID,
                                            'RelatedOptionTitle' => $RelatedOptionTitle
                                        ]);

                                    endforeach;
                                endif;


                            endforeach;
                        endif;
                    endforeach;
                endif;

            endforeach;

        endif;
        // exit;

        $QuestionData = $ArrayToPass;

        $view = 'questions';
        $user = $this->login_check();
        return view($view, compact('user', 'QuestionData'));
    }



    public function create_question()
    {
        $questionoptiontypedata = QuestionOptionType::where('status', 1)->get();
        $QSData = QuestionnaireSection::where('status',1)->get();
        $QuestionForGenders = QuestionForGender::get();
        $view = 'create_question';
        $user = $this->login_check();
        return view($view, compact('user', 'questionoptiontypedata','QSData','QuestionForGenders'));
    }

    public function edit_question(Request $req){
        $QuestionExistence = Question::find($req->QuestionID);

        if($QuestionExistence!=null):
            $questionoptiontypedata = QuestionOptionType::where('status', 1)->get();
            $QSData = QuestionnaireSection::where('status',1)->get();
            $QuestionForGenders = QuestionForGender::get();
            $view = 'edit_question';
            $user = $this->login_check();

            $FinalArray = [
                'ParentQuestionID'=>$QuestionExistence->id,
                'ParentQuestionTitle'=>$QuestionExistence->title,
                'ParentQuestionOptionTypeID'=>$QuestionExistence->question_option_type_id,
                'ParentQuestionnaireSectionID'=>$QuestionExistence->questionnaire_section_id,
                'ParentQuestionForGenderID'=>$QuestionExistence->question_for_gender_id,
                'ParentIsAddress'=>$QuestionExistence->is_address,
                'ParentTooltipInfo'=>$QuestionExistence->tooltip_info,
                'ParentIsRequired'=>$QuestionExistence->is_required,
                'Options'=>[]
            ];


            $OptionData = Option::where(['question_id'=>$QuestionExistence->id])->orderBy('sort_id','ASC')->get();

            if(count($OptionData)>0):
                foreach($OptionData as $ODKey=>$ODItem):
                    $RQData = RelatedQuestion::where(['option_id'=>$ODItem->id])->orderBy('sort_id','ASC')->get();
                    array_push($FinalArray['Options'],[
                        'ParentOptionID'=>$ODItem->id,
                        'ParentOptionTitle'=>$ODItem->title,
                        'RelatedQuestions'=>[]
                    ]);
                    if(count($RQData)>0):

                        $OptionIndex = count($FinalArray['Options'])-1;
                        foreach($RQData as $RQKey=>$RQItem):
                            array_push($FinalArray['Options'][$OptionIndex]['RelatedQuestions'],[
                                'RelatedQuestionID'=>$RQItem->id,
                                'RelatedQuestionTitle'=>$RQItem->title,
                                'RelatedQuestionOptionTypeID'=>$RQItem->question_option_type_id,
                                'RelatedIncreDecreDataFor'=>$RQItem->incre_decre_data_for,
                                'RelatedIncreDecreGetData'=>$RQItem->incre_decre_get_data,
                                'RelatedTooltipInfo'=>$RQItem->tooltip_info,
                                'RelatedIsRequired'=>$RQItem->is_required,
                                'RelatedOptions'=>[]
                            ]);

                            $ROData = RelatedOption::where(['related_question_id'=>$RQItem->id])->orderBy('sort_id','ASC')->get();

                            if(count($ROData)>0):

                                $RelatedQuestionIndex = count($FinalArray['Options'][$OptionIndex]['RelatedQuestions'])-1;

                                foreach($ROData as $ROKey=>$ROItem):

                                    array_push($FinalArray['Options'][$OptionIndex]['RelatedQuestions'][$RelatedQuestionIndex]['RelatedOptions'],[
                                        'RelatedOptionID'=>$ROItem->id,
                                        'RelatedOptionTitle'=>$ROItem->title,
                                    ]);

                                endforeach;


                            endif;

                        endforeach;

                    endif;


                endforeach;
            endif;
            return view($view, compact('user', 'questionoptiontypedata','QSData','QuestionForGenders','FinalArray'));
        else:
            return redirect()->route('questions');
        endif;
    }

    public function store_question(Request $req)
    {
        $response = array('status' => false, 'message' => 'Some Error Occured', 'redirect_url' => '','req'=>json_decode($req->Data));
        try {
            if (count(json_decode($req->Data, true)) > 0):
                $Data = json_decode($req->Data);

                foreach ($Data as $key => $item):
                    $MainQuestionOptionTypeID = $item->MainQuestionOptionTypeID;
                    $MainQuestion = $item->MainQuestion;
                    $MainQuestionTooltip = $item->MainQuestionTooltip;
                    $MainQuestionIsRequired = $item->MainQuestionIsRequired;

                    $ExistingLatestSortID = Question::orderBy('sort_id', 'DESC')->first();



                    $MainQuestionSortID = ($ExistingLatestSortID != null) ? $ExistingLatestSortID->sort_id + 1 : $item->MainQuestionSortID;



                    $question = new Question;
                    $question->questionnaire_section_id = $item->QuestionSectionID;
                    $question->question_for_gender_id = $item->QuestionForGenderID;
                    $question->title = $MainQuestion;
                    $question->is_required = $MainQuestionIsRequired;
                    $question->question_option_type_id = $MainQuestionOptionTypeID;
                    $question->sort_id = $MainQuestionSortID;
                    $question->tooltip_info = $MainQuestionTooltip;
                    $question->questionnaire_section_id = $req->questionnaire_section_id;

                    if ($question->save()):

                        $question_id = $question->id;
                        foreach ($item->MainOptions as $MOKey => $MOItem):
                            $MainOptionTitle = $MOItem->MainOptionTitle;
                            $MainOptionSortID = $MOItem->MainOptionSortID;

                            $option = new Option;
                            $option->title = $MainOptionTitle;
                            $option->question_id = $question_id;
                            $option->sort_id = $MainOptionSortID;
                            $option->status = 1;

                            if ($option->save()):
                                if (count($MOItem->RelatedQuestions) > 0):
                                    $option_id = $option->id;
                                    foreach ($MOItem->RelatedQuestions as $RQKey => $RQItem):
                                        $ChildQuestionOptionType = $RQItem->ChildQuestionOptionType;
                                        $IncreDecreDataFor = $RQItem->IncreDecreDataFor;
                                        $IncreDecreGetData = $RQItem->IncreDecreGetData;
                                        $ChildQuestionTitle = $RQItem->ChildQuestionTitle;
                                        $ChildQuestionTooltip = $RQItem->ChildQuestionTooltip;
                                        $ChildQuestionIsRequired = $RQItem->ChildQuestionIsRequired;
                                        $ChildQuestionSortID = $RQItem->ChildQuestionSortID;

                                        $related_question = new RelatedQuestion;
                                        $related_question->title = $ChildQuestionTitle;
                                        $related_question->is_required = $ChildQuestionIsRequired;
                                        $related_question->option_id = $option_id;
                                        $related_question->question_option_type_id = $ChildQuestionOptionType;
                                        $related_question->incre_decre_data_for = $IncreDecreDataFor;
                                        $related_question->incre_decre_get_data = $IncreDecreGetData;
                                        $related_question->sort_id = $ChildQuestionSortID;
                                        $related_question->tooltip_info = $ChildQuestionTooltip;
                                        $related_question->status = 1;

                                        if ($related_question->save()):
                                            $related_question_id = $related_question->id;

                                            foreach ($RQItem->ChildOptions as $COKey => $COItem):
                                                $ChildOptionTitle = $COItem->ChildOptionTitle;
                                                $ChildOptionSortID = $COItem->ChildOptionSortID;

                                                $related_option = new RelatedOption;
                                                $related_option->title = $ChildOptionTitle;
                                                $related_option->related_question_id = $related_question_id;
                                                $related_option->sort_id = $ChildOptionSortID;
                                                $related_option->save();

                                            endforeach;

                                        endif;

                                    endforeach;

                                endif;
                            endif;

                        endforeach;

                    endif;


                endforeach;

                $response['status'] = true;
                $response['message'] = 'Question Added Successfully';
                $response['redirect_url'] = route('questions');

            endif;
        } catch (\Exception $ex) {
            $response['message'] = $this->ExceptionMessage($ex);
        }

        return $response;
    }

    public function update_question_sorting(Request $req)
    {
        $response = array('status' => false, 'message' => 'Some Error Occured');
        try {
            if (count(json_decode($req->Data, true)) > 0):

                $Data = json_decode($req->Data);
                foreach ($Data as $QDKey => $QDItem):
                    $ParentQuestionSortID = $QDItem->SortID;
                    $ParentQuestionID = $QDItem->ParentQuestionID;

                    $UpdateParentQuestion = Question::find($ParentQuestionID);
                    $UpdateParentQuestion->sort_id = $ParentQuestionSortID;
                    $UpdateParentQuestion->save();

                    if (count($QDItem->Options) > 0):
                        foreach ($QDItem->Options as $OKey => $OItem):
                            $ParentOptionSortID = $OItem->SortID;
                            $ParentOptionID = $OItem->ParentOptionID;

                            $UpdateParentOption = Option::find($ParentOptionID);
                            $UpdateParentOption->sort_id = $ParentOptionSortID;
                            $UpdateParentOption->save();

                            if (count($OItem->RelatedQuestions) > 0):

                                foreach ($OItem->RelatedQuestions as $RQKey => $RQItem):
                                    $RelatedQuestionSortID = $RQItem->SortID;
                                    $RelatedQuestionID = $RQItem->RelatedQuestionID;

                                    $UpdateRelatedQuestion = RelatedQuestion::find($RelatedQuestionID);
                                    $UpdateRelatedQuestion->sort_id = $RelatedQuestionSortID;
                                    $UpdateRelatedQuestion->save();

                                    if (count($RQItem->RelatedOptions) > 0):

                                        foreach ($RQItem->RelatedOptions as $ROKey => $ROItem):
                                            $RelatedOptionSortID = $ROItem->SortID;
                                            $RelatedOptionID = $ROItem->RelatedOptionID;

                                            $UpdateRelatedOption = RelatedOption::find($RelatedOptionID);
                                            $UpdateRelatedOption->sort_id = $RelatedOptionSortID;
                                            $UpdateRelatedOption->save();

                                        endforeach;

                                    endif;

                                endforeach;

                            endif;

                        endforeach;
                    endif;


                endforeach;
                $response['status'] = true;
                $response['message'] = 'Updated Successfully';
            endif;
        } catch (\Exception $ex) {
            $response['message'] = $this->ExceptionMessage($ex);
        }

        return $response;
    }

    public function update_question_old(Request $req){
        try {
            $ValueToUpdate = $req->value_to_update;
            $ActualID = $req->actual_id;

            if ($req->question_level == "parent_question"):
                $QuestionToUpdate = Question::find($ActualID);
                $QuestionToUpdate->title = $ValueToUpdate;
                if ($QuestionToUpdate->save()):
                    return redirect()->route('questions')->with('success_msg', 'Question Updated Successfully');
                endif;
            endif;

            if ($req->question_level == "parent_option"):
                $OptionToUpdate = Option::find($ActualID);
                $OptionToUpdate->title = $ValueToUpdate;
                if ($OptionToUpdate->save()):
                    return redirect()->route('questions')->with('success_msg', 'Option Updated Successfully');
                endif;
            endif;

            if ($req->question_level == "related_question"):
                $RQToUpdate = RelatedQuestion::find($ActualID);
                $RQToUpdate->title = $ValueToUpdate;
                if ($RQToUpdate->save()):
                    return redirect()->route('questions')->with('success_msg', 'Related Question Updated Successfully');
                endif;
            endif;

            if ($req->question_level == "related_option"):
                $ROToUpdate = RelatedOption::find($ActualID);
                $ROToUpdate->title = $ValueToUpdate;
                if ($ROToUpdate->save()):
                    return redirect()->route('questions')->with('success_msg', 'Related Option Updated Successfully');
                endif;
            endif;

        } catch (\Exception $ex) {
            $this->ExceptionMessage($ex);
        }
    }

    public function update_question(Request $req)
    {
        $response = array('status' => false, 'message' => 'Some Error Occured', 'redirect_url' => '','req'=>json_decode($req->Data));
        try {
            if (count(json_decode($req->Data, true)) > 0):
                $Data = json_decode($req->Data);

                foreach($Data as $key=>$item):

                    $MainQuestionID = $item->MainQuestionID;
                    if($MainQuestionID!=""):

                        //updating mainquestion

                        $UpdateMQ = Question::find($MainQuestionID);
                        $UpdateMQ->questionnaire_section_id = $item->QuestionSectionID;
                        $UpdateMQ->question_for_gender_id = $item->QuestionForGenderID;
                        $UpdateMQ->title = $item->MainQuestion;
                        $UpdateMQ->tooltip_info = $item->MainQuestionTooltip;
                        $UpdateMQ->is_required = $item->MainQuestionIsRequired;
                            if ($UpdateMQ->save()):
                        foreach ($item->MainOptions as $MOKey => $MOItem):
                            $MainOptionTitle = $MOItem->MainOptionTitle;
                            $MainOptionSortID = $MOItem->MainOptionSortID;
                            if($MOItem->MainOptionID==""):

                                $option = new Option;
                                $option->title = $MainOptionTitle;
                                $option->question_id = $MainQuestionID;
                                $option->sort_id = $MainOptionSortID;
                                $option->status = 1;

                                if ($option->save()):
                                    if (count($MOItem->RelatedQuestions) > 0):
                                        $option_id = $option->id;
                                        foreach ($MOItem->RelatedQuestions as $RQKey => $RQItem):
                                            $ChildQuestionOptionType = $RQItem->ChildQuestionOptionType;
                                            $IncreDecreDataFor = $RQItem->IncreDecreDataFor;
                                            $IncreDecreGetData = $RQItem->IncreDecreGetData;
                                            $ChildQuestionTitle = $RQItem->ChildQuestionTitle;
                                            $ChildQuestionTooltip = $RQItem->ChildQuestionTooltip;
                                            $ChildQuestionIsRequired = $RQItem->ChildQuestionIsRequired;
                                            $ChildQuestionSortID = $RQItem->ChildQuestionSortID;

                                            $related_question = new RelatedQuestion;
                                            $related_question->title = $ChildQuestionTitle;
                                            $related_question->tooltip_info = $ChildQuestionTooltip;
                                            $related_question->is_required = $ChildQuestionIsRequired;
                                            $related_question->option_id = $option_id;
                                            $related_question->question_option_type_id = $ChildQuestionOptionType;
                                            $related_question->incre_decre_data_for = $IncreDecreDataFor;
                                            $related_question->incre_decre_get_data = $IncreDecreGetData;
                                            $related_question->sort_id = $ChildQuestionSortID;
                                            $related_question->status = 1;

                                            if ($related_question->save()):
                                                $related_question_id = $related_question->id;

                                                foreach ($RQItem->ChildOptions as $COKey => $COItem):
                                                    $ChildOptionTitle = $COItem->ChildOptionTitle;
                                                    $ChildOptionSortID = $COItem->ChildOptionSortID;

                                                    $related_option = new RelatedOption;
                                                    $related_option->title = $ChildOptionTitle;
                                                    $related_option->related_question_id = $related_question_id;
                                                    $related_option->sort_id = $ChildOptionSortID;
                                                    $related_option->save();

                                                endforeach;

                                            endif;

                                        endforeach;

                                    endif;
                                endif;

                            elseif($MOItem->MainOptionID!=""):
                                $MainOptionID = $MOItem->MainOptionID;
                                $option = Option::find($MainOptionID);
                                $option->title = $MainOptionTitle;
                                $option->question_id = $MainQuestionID;
                                $option->sort_id = $MainOptionSortID;
                                $option->status = 1;

                                if($option->save()):

                                    if (count($MOItem->RelatedQuestions) > 0):

                                        foreach ($MOItem->RelatedQuestions as $RQKey => $RQItem):

                                            $ChildQuestionID = $RQItem->ChildQuestionID;
                                            $ChildQuestionOptionType = $RQItem->ChildQuestionOptionType;
                                            $IncreDecreDataFor = $RQItem->IncreDecreDataFor;
                                            $IncreDecreGetData = $RQItem->IncreDecreGetData;
                                            $ChildQuestionTitle = $RQItem->ChildQuestionTitle;
                                            $ChildQuestionTooltip = $RQItem->ChildQuestionTooltip;
                                            $ChildQuestionIsRequired = $RQItem->ChildQuestionIsRequired;
                                            $ChildQuestionSortID = $RQItem->ChildQuestionSortID;

                                            if($ChildQuestionID==""):



                                                $related_question = new RelatedQuestion;
                                                $related_question->title = $ChildQuestionTitle;
                                                $related_question->tooltip_info = $ChildQuestionTooltip;
                                                $related_question->is_required = $ChildQuestionIsRequired;
                                                $related_question->option_id = $MainOptionID;
                                                $related_question->question_option_type_id = $ChildQuestionOptionType;
                                                $related_question->incre_decre_data_for = $IncreDecreDataFor;
                                                $related_question->incre_decre_get_data = $IncreDecreGetData;
                                                $related_question->sort_id = $ChildQuestionSortID;
                                                $related_question->status = 1;

                                                if ($related_question->save()):
                                                    $related_question_id = $related_question->id;

                                                    foreach ($RQItem->ChildOptions as $COKey => $COItem):
                                                        $ChildOptionTitle = $COItem->ChildOptionTitle;
                                                        $ChildOptionSortID = $COItem->ChildOptionSortID;

                                                        $related_option = new RelatedOption;
                                                        $related_option->title = $ChildOptionTitle;
                                                        $related_option->related_question_id = $related_question_id;
                                                        $related_option->sort_id = $ChildOptionSortID;
                                                        $related_option->save();

                                                    endforeach;

                                                endif;

                                            elseif($ChildQuestionID!=""):

                                                $related_question = RelatedQuestion::find($ChildQuestionID);
                                                $related_question->title = $ChildQuestionTitle;
                                                $related_question->tooltip_info = $ChildQuestionTooltip;
                                                $related_question->is_required = $ChildQuestionIsRequired;
                                                $related_question->option_id = $MainOptionID;
                                                $related_question->question_option_type_id = $ChildQuestionOptionType;
                                                $related_question->incre_decre_data_for = $IncreDecreDataFor;
                                                $related_question->incre_decre_get_data = $IncreDecreGetData;
                                                $related_question->sort_id = $ChildQuestionSortID;
                                                $related_question->status = 1;

                                                if ($related_question->save()):

                                                    foreach ($RQItem->ChildOptions as $COKey => $COItem):

                                                        $ChildOptionID = $COItem->ChildOptionID;
                                                        $ChildOptionTitle = $COItem->ChildOptionTitle;
                                                        $ChildOptionSortID = $COItem->ChildOptionSortID;

                                                        if($ChildOptionID==""):

                                                            $related_option = new RelatedOption;
                                                            $related_option->title = $ChildOptionTitle;
                                                            $related_option->related_question_id = $ChildQuestionID;
                                                            $related_option->sort_id = $ChildOptionSortID;
                                                            $related_option->save();

                                                        elseif($ChildOptionID!=""):

                                                            $related_option = RelatedOption::find($ChildOptionID);
                                                            $related_option->title = $ChildOptionTitle;
                                                            $related_option->related_question_id = $ChildQuestionID;
                                                            $related_option->sort_id = $ChildOptionSortID;
                                                            $related_option->save();

                                                        endif;





                                                    endforeach;

                                                endif;

                                            endif;

                                        endforeach;

                                    endif;

                                endif;



                            endif;

                        endforeach;

                    endif;

                    endif;

                endforeach;


                $response['status'] = true;
                $response['message'] = 'Question Updated Successfully';
                $response['redirect_url'] = route('questions');

            endif;
        } catch (\Exception $ex) {
            $response['message'] = $this->ExceptionMessage($ex);
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
