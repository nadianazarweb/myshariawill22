<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Question;
use App\Models\Option;
use App\Models\RelatedQuestion;
use App\Models\RelatedOption;
use App\Models\AttemptedQuestionOption;
use App\Models\QuestionnaireSection;
use Mail;
use Session;
use Stripe\Exception\CardException;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Artisan;

class FrontEndController extends Controller
{

    public function index()
    {
        $QSData = QuestionnaireSection::where(['status' => 1])->get();
        return view('frontend.index', compact('QSData'));
    }
    public function login()
    {
        return view('frontend.auth.login');
    }

    public function login_submit(Request $req)
    {
        try {
            $result = User::join('roles AS R', 'R.id', '=', 'users.role_id')->where([
                'users.email' => $req->email,
                'users.role_id' => 2
            ])->first([
                'users.id',
                'users.username',
                'users.name',
                'users.email',
                'users.avatar',
                'users.password',
                'users.is_email_verified',
                'users.is_locked',
                'users.is_payment_done',
                'users.role_id',
                'users.contact_no',
                'R.name AS role_name'
            ]);

            if (!empty($result)) :
                if (Hash::check($req->password, $result->password)) :
                    if ($result->is_email_verified == 1) :
                        $sessiondata = array(
                            'user_id' => $result->id,
                            'user_name' => $result->username,
                            'full_name' => $result->name,
                            'email' => $result->email,
                            'avatar' => $result->avatar,
                            'is_locked' => $result->is_locked,
                            'role_id' => $result->role_id,
                            'contact_no' => $result->contact_no,
                            'role_name' => $result->role_name
                        );
                        $req->session()->put($sessiondata);
                        
                        // Check if user has already completed questionnaire and needs to pay
                        if ($result->is_locked == "1" && $result->is_payment_done == 0) :
                            // Redirect directly to payment instead of showing view
                            return redirect()->route('payment_checkout', ['user_id' => $result->id]);
                        elseif ($result->is_locked == "1" && $result->is_payment_done == 1) :
                            return redirect()->route('customer_forms');
                        else :
                            return redirect()->route('customer_queries');
                        endif;

                        //     $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
                        // $session = $stripe->checkout->sessions->create([
                        //     'line_items' => [[
                        //         'price_data' => [
                        //             'currency' => 'gbp',
                        //             'product_data' => [
                        //                 'name' => 'My Sharia Wills Subscription',
                        //             ],
                        //             'unit_amount' => "1000",
                        //         ],
                        //         'quantity' => 1,
                        //     ]],
                        //     'mode' => 'payment',
                        //     "success_url" => url('/') . '/payment/success',
                        //     'cancel_url' => url('/') . '/payment/failed',
                        // ]);
                        // $session_id = $session->id;

                        // $User = User::find($result->id);
                        // $User->pre_payment_session_key = $session_id;

                        // if($User->save()):
                        //     return redirect($session->url)->with('session_id', $session_id);
                        // endif;

                    else :

                        return redirect()->route('login')->with('msg', '<h5 class="mb-0 text-center mt-3 text-danger">Your Email Is Not Verified</h5>');

                    endif;

                else :

                    return redirect()->route('login')->with('msg', '<h5 class="mb-0 text-center mt-3 text-danger">Incorrect Credentials</h5>');

                endif;
            else :
                return redirect()->route('login')->with('msg', '<h5 class="mb-0 text-center mt-3 text-danger">Incorrect Credentials</h5>');
            endif;
        } catch (\Exception $ex) {
            return redirect()->route('login')->with('msg', '<h5 class="mb-0 text-center mt-3 text-danger">' . $ex->getMessage() . ' on line ' . $ex->getLine() . '</h5>');
        }
    }

    public function register()
    {
        return view('frontend.auth.register');
    }

    public function forgot_your_password()
    {
        return view('frontend.auth.forgot_your_password');
    }
    public function forgot_your_password_submit(Request $req)
    {
        $ResponseMSG = '';
        $ResponseStatus = '';

        try {

            $Email = $req->email;

            if ($Email != "") :
                $User = User::where(['email' => $Email])->first();
                if ($User != null) :

                    try {
                        $Token = uniqid(50);
                        $url = url("/auth/reset_password?token=" . $Token);

                        Mail::send('emails.reset_password', compact('url'), function ($message) use ($Email) {
                            $message->to($Email);
                            $message->from(config('mail.from.address'));
                            $message->subject('Reset Your Password');
                        });

                        $User->password_reset_token = $Token;

                        if ($User->save()) :
                            $ResponseMSG = 'Password reset link has been sent to the email ' . $Email . ' Please also check your Spam/Junk folder if you dont see it in your inbox.';
                            $ResponseStatus = 'success_msg';
                        endif;
                    } catch (\Exception $emailexception) {
                        $ResponseMSG = 'Email Exception | ' . $emailexception->getMessage() . ' on line number ' . $emailexception->getLine();
                        $ResponseStatus = 'failure_msg';
                    }

                else :

                    $ResponseMSG = 'The email you provided does not exist';
                    $ResponseStatus = 'failure_msg';

                endif;
            endif;
        } catch (\Exception $ex) {
            $ResponseStatus = 'failure_msg';
            $ResponseMSG = $this->ExceptionMessage($ex);
        }

        return redirect()->route('forgot_your_password')->with($ResponseStatus, $ResponseMSG);
    }

    public function reset_password(Request $req)
    {
        $Token = $_GET['token'];
        $User = User::where('password_reset_token', $Token)->first();
        if ($User != null) :
            return view('frontend.auth.reset_password');
        else :
            return redirect()->route('login')->with('msg', '<h5 class="mb-0 text-center mt-3 text-danger">Token Has Been Expired</h5>');
        endif;
    }

    public function reset_password_submit(Request $req)
    {
        $ResponseMSG = '';
        $ResponseStatus = '';
        try {

            $User = User::where('password_reset_token', $req->password_reset_token)->first();
            if ($User != null) :
                $User->password = Hash::make($req->password);
                $User->password_reset_token = null;

                if ($User->save()) :
                    return redirect()->route('login')->with('msg', '<h5 class="mb-0 text-center mt-3 text-success">Your password has been updated successfully</h5>');
                endif;

            endif;
        } catch (\Exception $ex) {
            $ResponseStatus = 'failure_msg';
            $ResponseMSG = $this->ExceptionMessage($ex);
        }
        return redirect()->back()->with($ResponseStatus, $ResponseMSG);
    }

    public function validateusername(Request $req)
    {
        $UserName = $req->UserName;
        return $UserNameCount = User::where(['username' => $UserName])->count();
    }

    public function validateemail(Request $req)
    {
        $Email = $req->Email;
        return $EmailCount = User::where(['email' => $Email])->count();
    }

    public function register_submit(Request $req)
    {
        try {
            $FullName = $req->full_name;
            $UserName = $req->user_name;
            $Email = $req->email;
            $Password = $req->password;
            $ContactNo = $req->contact_no;
            $Token = uniqid(50);

            $isGoodToGo = true;

            $Errors = [];

            if (User::where('username', $UserName)->count() > 0) :
                $isGoodToGo = false;
                $Errors[] = 'User Name';
            endif;

            if (User::where('email', $Email)->count() > 0) :
                $isGoodToGo = false;
                $Errors[] = 'Email';
            endif;

            if ($isGoodToGo) :

                try {
                    $url = url("/verifyemail?token=" . $Token);

                    Mail::send('emails.verification', compact('url'), function ($message) use ($Email) {
                        $message->to($Email);
                        $message->from(config('mail.from.address'));
                        $message->subject('Verify Your Email');
                    });

                    $User = new User;
                    $User->role_id = 2;
                    $User->name = $FullName;
                    $User->username = $UserName;
                    $User->email = $Email;
                    $User->password = Hash::make($Password);
                    $User->contact_no = $ContactNo;
                    $User->email_verification_token = $Token;
                    if ($User->save()) :
                        return redirect()->route('login')->with('msg', '<h5 class="mb-0 text-center mt-3 text-success">A verification link has been sent to the email ' . $Email . '<br> Please also check your <strong>Spam/Junk</strong> folder if you dont see it in your inbox.</h5>');
                    endif;
                } catch (\Exception $emailexception) {
                    echo 'Email Exception | ' . $emailexception->getMessage() . ' on line number ' . $emailexception->getLine();
                }
            else :
                echo join(' And ', $Errors) . ' already exists';
            endif;
        } catch (\Exception $ex) {
            echo $ex->getMessage() . ' on line number ' . $ex->getLine();
        }
    }

    public function take_to_stripe_checkout(Request $req)
    {
        try {
            // Add logging to track what's happening
            \Log::info('take_to_stripe_checkout method called', [
                'url' => $req->url(),
                'route_user_id' => $req->route('user_id'),
                'session_payment_user_id' => $req->session()->get('payment_user_id'),
                'all_route_params' => $req->route()->parameters()
            ]);
            
            // Get user ID from route parameter (this is the correct way for route parameters)
            $user_id = $req->route('user_id') ?: ($req->session()->get('payment_user_id') ?: null);
            
            \Log::info('User ID determined', ['user_id' => $user_id]);
            
            if ($user_id) :
                \Log::info('User ID found, processing payment');
                
                // Re-enable Stripe checkout
                \Log::info('Creating Stripe client');
                $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
                \Log::info('Stripe client created, creating session');
                $session = $stripe->checkout->sessions->create([
                    'line_items' => [
                        [
                            'price_data' => [
                                'currency' => 'gbp',
                                'product_data' => [
                                    'name' => 'My Sharia Wills Subscription',
                                ],
                                'unit_amount' => "9900",
                            ],
                            'quantity' => 1,
                        ]
                    ],
                    'mode' => 'payment',
                    "success_url" => url('/') . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => url('/') . '/payment/failed',
                ]);

                $session_id = $session->id;
                \Log::info('Stripe session created', ['session_id' => $session_id]);

                $User = User::find($user_id);
                $User->pre_payment_session_key = $session_id;
                $User->save();
                
                \Log::info('User payment session saved', ['user_id' => $user_id, 'session_id' => $session_id]);

                $req->session()->forget('payment_user_id');

                \Log::info('Redirecting to Stripe', ['stripe_url' => $session->url, 'session_id' => $session_id]);
                return redirect($session->url)->with('session_id', $session_id);
            else :
                return redirect('/');
            endif;
        } catch (ApiErrorException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function payment_status(Request $req)
    {
        // Simple logging to track the flow
        \Log::info('payment_status method called', [
            'url' => $req->url(),
            'PaymentStatus' => $req->PaymentStatus,
            'session_id' => $req->get('session_id')
        ]);

        if ($req->PaymentStatus == 'success') :
            \Log::info('PaymentStatus is success, checking session_id');
            
            // Try to get session_id from URL query parameter first (Stripe sends this)
            $session_id = $req->get('session_id');
            
            if (!empty($session_id)) :
                \Log::info('Session ID found in URL', ['session_id' => $session_id]);
                
                $User = User::join('roles AS R', 'R.id', '=', 'users.role_id')->where('pre_payment_session_key', $session_id)->first([
                    'users.id',
                    'users.username',
                    'users.name',
                    'users.email',
                    'users.avatar',
                    'users.contact_no',
                    'users.password',
                    'users.is_email_verified',
                    'users.is_locked',
                    'users.is_payment_done',
                    'users.role_id',
                    'R.name AS role_name'
                ]);
            else :
                \Log::info('No session_id in session, searching database for active payment sessions');
                
                // Search for users with active payment sessions
                $User = User::join('roles AS R', 'R.id', '=', 'users.role_id')
                    ->whereNotNull('pre_payment_session_key')
                    ->where('pre_payment_session_key', '!=', '')
                    ->where('is_payment_done', 0)
                    ->first([
                        'users.id',
                        'users.username',
                        'users.name',
                        'users.email',
                        'users.avatar',
                        'users.contact_no',
                        'users.password',
                        'users.is_email_verified',
                        'users.is_locked',
                        'users.is_payment_done',
                        'users.role_id',
                        'R.name AS role_name'
                    ]);
                
                if ($User) {
                    $session_id = $User->pre_payment_session_key;
                    \Log::info('Found user with active payment session', ['user_id' => $User->id, 'session_id' => $session_id]);
                }
            endif;

            if ($User != null && !empty($session_id)) :
                \Log::info('User found for payment processing', [
                    'user_id' => $User->id,
                    'username' => $User->username,
                    'is_locked' => $User->is_locked,
                    'is_payment_done' => $User->is_payment_done,
                    'session_id' => $session_id
                ]);

                $UserUpdate = User::find($User->id);
                $UserUpdate->is_payment_done = 1;
                $UserUpdate->payment_date = date('Y-m-d h:i:s');
                $UserUpdate->pre_payment_session_key = null;
                
                // If user has completed questionnaire, set is_locked = 1
                if ($User->is_locked == 0) {
                    $UserUpdate->is_locked = 1;
                    $UserUpdate->queries_submission_datetime = date('Y-m-d H:i:s');
                }

                $stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
                $session = $stripe->checkout->sessions->retrieve($session_id);
                $stripe_response = json_encode($session);
                $UserUpdate->payment_response = $stripe_response;

                if ($UserUpdate->save()) :
                    \Log::info('User payment status updated successfully', [
                        'user_id' => $User->id,
                        'is_payment_done' => $UserUpdate->is_payment_done,
                        'payment_date' => $UserUpdate->payment_date
                    ]);

                    $sessiondata = array(
                        'user_id' => $User->id,
                        'user_name' => $User->username,
                        'full_name' => $User->name,
                        'email' => $User->email,
                        'avatar' => $User->avatar,
                        'is_locked' => $UserUpdate->is_locked,
                        'role_id' => $User->role_id,
                        'contact_no' => $User->contact_no,
                        'role_name' => $User->role_name
                    );
                    $req->session()->put($sessiondata);
                    
                    \Log::info('Session data updated, redirecting to customer_forms');
                    
                    // Simple redirect to forms page after payment
                    return redirect()->route('customer_forms');
                endif;
            else :
                \Log::warning('No user found or session_id is empty');
                return redirect('/')->with('error', 'No active payment session found');
            endif;
        elseif ($req->PaymentStatus == 'failed') :
            \Log::warning('Payment failed');
            echo 'payment failed';
        endif;
    }


    public function verify_email()
    {
        $Token = $_GET['token'];
        $User = User::where('email_verification_token', $Token)->first();
        if ($User != null) :
            $UpdateUser = User::find($User->id);
            $UpdateUser->is_email_verified = 1;
            $UpdateUser->email_verified_at = date('Y-m-d h:i:s');
            $UpdateUser->email_verification_token = null;
            $UpdateUser->status = 1;
            if ($UpdateUser->save()) :
                // No need to set payment_user_id anymore - user can proceed to questionnaire
            endif;

            // return redirect()->route('login')->with('msg', '<h5 class="mb-0 text-center mt-3 text-success">Email Verified Successfully</h5>');
            return view('frontend.user.email_verified');
        else :
            return redirect()->route('login')->with('msg', '<h5 class="mb-0 text-center mt-3 text-danger">Token Has Been Expired</h5>');
        endif;
    }

    public function queries()
    {
        $QSData = QuestionnaireSection::where(['status' => 1])->get();
        $UserID = Session::get('user_id');
        $UserIsLocked = User::find($UserID);
        if ($UserIsLocked->is_locked != "1") :
            $QuestionData = Question::join('options AS O', 'O.question_id', '=', 'questions.id')
                ->join('question_option_types AS QOT', 'QOT.id', '=', 'questions.question_option_type_id')
                ->orderBy('questions.sort_id', 'ASC')->orderBy('O.sort_id', 'ASC')->get(
                    [
                        'questions.id AS QuestionID',
                        'questions.title AS QuestionTitle',
                        'questions.question_option_type_id AS QuestionOptionTypeID',
                        'QOT.title AS QuestionOptionType',
                        'O.id AS OptionID',
                        'O.title AS OptionTitle',
                    ]
                );

            $MainFinalArray = array();

            if (count($QuestionData) > 0) :
                foreach ($QuestionData as $QDKey => $QDItem) :
                    $MFAQDArr = array_column($MainFinalArray, 'QuestionID');
                    if (in_array($QDItem->QuestionID, $MFAQDArr)) :
                        $QFoundedIndex = array_search($QDItem->QuestionID, $MFAQDArr);

                        array_push($MainFinalArray[$QFoundedIndex]['Options'], [
                            'OptionID' => $QDItem->OptionID,
                            'OptionTitle' => $QDItem->OptionTitle,
                            'RelatedQuestions' => array()
                        ]);


                    else :
                        array_push($MainFinalArray, [
                            'QuestionID' => $QDItem->QuestionID,
                            'QuestionTitle' => $QDItem->QuestionTitle,
                            'QuestionOptionTypeID' => $QDItem->QuestionOptionTypeID,
                            'QuestionOptionType' => $QDItem->QuestionOptionType,
                            'Options' => array(
                                [
                                    'OptionID' => $QDItem->OptionID,
                                    'OptionTitle' => $QDItem->OptionTitle,
                                    'RelatedQuestions' => array()
                                ]
                            )
                        ]);
                    endif;
                endforeach;
            endif;

            if (count($MainFinalArray) > 0) :
                foreach ($MainFinalArray as $MFAKey => $MFAItem) :
                    if (count($MFAItem['Options']) > 0) :
                        foreach ($MFAItem['Options'] as $OKey => $OItem) :
                            $RelatedQuestionData = RelatedQuestion::join('related_options AS RO', 'RO.related_question_id', '=', 'related_questions.id')
                                ->join('question_option_types AS QOT', 'QOT.id', '=', 'related_questions.question_option_type_id')
                                ->where(['related_questions.option_id' => $OItem['OptionID']])
                                ->orderBy('related_questions.sort_id', 'ASC')->orderBy('RO.sort_id', 'ASC')->get(
                                    [
                                        'related_questions.id AS RelatedQuestionID',
                                        'related_questions.title AS RelatedQuestionTitle',
                                        'related_questions.question_option_type_id AS QuestionOptionTypeID',
                                        'QOT.title AS QuestionOptionType',
                                        'RO.id AS RelatedOptionID',
                                        'RO.title AS RelatedOptionTitle',
                                    ]
                                );

                            if (count($RelatedQuestionData) > 0) :

                                foreach ($RelatedQuestionData as $RQDKey => $RQDItem) :
                                    $RelatedQuestionID = $RQDItem->RelatedQuestionID;
                                    $RelatedQuestionTitle = $RQDItem->RelatedQuestionTitle;
                                    $RelatedQuestionOptionTypeID = $RQDItem->QuestionOptionTypeID;
                                    $RelatedQuestionOptionType = $RQDItem->QuestionOptionType;
                                    $RelatedOptionID = $RQDItem->RelatedOptionID;
                                    $RelatedOptionTitle = $RQDItem->RelatedOptionTitle;

                                    $MFARQDArr = array_column($MainFinalArray[$MFAKey]['Options'][$OKey]['RelatedQuestions'], 'RelatedQuestionID');

                                    if (in_array($RQDItem->RelatedQuestionID, $MFARQDArr)) :
                                        $QFoundedIndex = array_search($RQDItem->RelatedQuestionID, $MFARQDArr);

                                        array_push($MainFinalArray[$MFAKey]['Options'][$OKey]['RelatedQuestions'][$QFoundedIndex]['RelatedOptions'], [
                                            'RelatedOptionID' => $RelatedOptionID,
                                            'RelatedOptionTitle' => $RelatedOptionTitle,
                                        ]);

                                    else :

                                        array_push($MainFinalArray[$MFAKey]['Options'][$OKey]['RelatedQuestions'], [
                                            'RelatedQuestionID' => $RelatedQuestionID,
                                            'RelatedQuestionTitle' => $RelatedQuestionTitle,
                                            'RelatedQuestionOptionTypeID' => $RelatedQuestionOptionTypeID,
                                            'RelatedQuestionOptionType' => $RelatedQuestionOptionType,
                                            'RelatedOptions' => array(
                                                [
                                                    'RelatedOptionID' => $RelatedOptionID,
                                                    'RelatedOptionTitle' => $RelatedOptionTitle,
                                                ]
                                            )
                                        ]);

                                    endif;

                                endforeach;

                            endif;

                        endforeach;
                    endif;
                endforeach;
            endif;


            return view('frontend.user.dashboard', compact('MainFinalArray', 'QSData'));

        else :

            return redirect()->route('customer_dashboard');

        endif;
    }

    public function update_question(Request $req)
    {
        $response = [
            'reached_end' => "0",
            'Question' => '',
            'Options' => '',
            'AttemptedQuestion' => '',
            'AttemptedRelatedQuestion' => '',
            'is_related_question' => false,
            'related_data' => '',
            'status' => false,
            'has_data' => false,
            'is_locked' => false,
            'msg' => '',
            'q_section_progress' => '',
            'req' => $_POST
        ];

        try {
            $UserID = $req->session()->get('user_id');

            $IsLockedStatus = User::find($UserID);
            $response['reached_end'] = $IsLockedStatus->reached_end;
            // if ($IsLockedStatus->reached_end == "0"):
            if ($IsLockedStatus->is_locked == "0") :
                $IsRelatedQuestion = false;
                if ($req->QuestionOptionID != 'NULL' && $req->RelatedQuestionOptionID == 'NULL') :

                    $QuestionID = $req->QuestionOptionID['ParentQuestionID'];



                    $AttemptedQuestionOptionData = AttemptedQuestionOption::where(['question_id' => $QuestionID, 'user_id' => $UserID])->first();
                    if ($AttemptedQuestionOptionData != null) :


                        if ($req->QuestionOptionTypeID == "1") :
                            $AttemptedOptions = AttemptedQuestionOption::where(['question_id' => $QuestionID, 'user_id' => $UserID, 'option_id' => $req->QuestionOptionID['SelectedOptionID']])->get();
                            if (count($AttemptedOptions) == 0) :
                                AttemptedQuestionOption::where(['question_id' => $QuestionID, 'user_id' => $UserID])->delete();
                                if ($QuestionID == 4 || $QuestionID == 5) :
                                    AttemptedQuestionOption::where(['question_id' => 6, 'user_id' => $UserID])->delete(); //also delete the children attempted question
                                endif;
                                $AttemptedQuestionOption = new AttemptedQuestionOption;
                                $AttemptedQuestionOption->user_id = $UserID;
                                $AttemptedQuestionOption->question_id = $QuestionID;
                                $AttemptedQuestionOption->option_id = $req->QuestionOptionID['SelectedOptionID'];
                                $AttemptedQuestionOption->save();
                            endif;

                        elseif ($req->QuestionOptionTypeID == "2" || $req->QuestionOptionTypeID == "4" || $req->QuestionOptionTypeID == "5") :

                            $AttemptedQuestionOption = AttemptedQuestionOption::find($AttemptedQuestionOptionData->id);
                            $AttemptedQuestionOption->user_id = $UserID;

                            $AttemptedQuestionOption->text_value = $req->QuestionOptionID['ParentTextValue'];
                            $AttemptedQuestionOption->save();

                        endif;

                    else :
                        $AttemptedQuestionOption = new AttemptedQuestionOption;
                        $AttemptedQuestionOption->user_id = $UserID;
                        $AttemptedQuestionOption->question_id = $QuestionID;
                        if ($req->QuestionOptionTypeID == "1") :
                            $AttemptedQuestionOption->option_id = $req->QuestionOptionID['SelectedOptionID'];
                        elseif ($req->QuestionOptionTypeID == "2" || $req->QuestionOptionTypeID == "4" || $req->QuestionOptionTypeID == "5") :
                            $AttemptedQuestionOption->text_value = $req->QuestionOptionID['ParentTextValue'];
                        endif;
                        $AttemptedQuestionOption->save();
                    endif;

                    $RelatedQuestionData = new \stdClass();
                    $RelatedQuestionData->related_question = "";

                    if ($req->RelatedSortID == 'NULL' && $req->QuestionOptionTypeID == "1") :
                        $OptionID = $req->QuestionOptionID['SelectedOptionID'];
                        if ($OptionID == 4 || $OptionID == 6) :
                            if ($QuestionID == 4 || $QuestionID == 5) :
                                AttemptedQuestionOption::where(['question_id' => 6, 'user_id' => $UserID])->delete(); //also delete the children attempted question
                            endif;
                        endif;
                        $RelatedQuestionData->related_question = RelatedQuestion::join('question_option_types AS QOT', 'QOT.id', '=', 'related_questions.question_option_type_id')
                            ->join('options AS O', 'O.id', '=', 'related_questions.option_id')
                            ->join('questions AS Q', 'Q.id', '=', 'O.question_id')
                            ->where(['related_questions.option_id' => $OptionID])
                            ->orderBy('related_questions.sort_id', 'ASC')
                            ->first(
                                [
                                    'Q.id AS ParentQuestionID',
                                    'related_questions.option_id AS ParentOptionID',
                                    'related_questions.id AS RelatedQuestionID',
                                    'related_questions.title AS RelatedQuestionTitle',
                                    'related_questions.question_option_type_id AS RelatedQuestionOptionTypeID',
                                    'related_questions.is_required AS RelatedQuestionIsRequired',
                                    'QOT.title AS RelatedQuestionOptionType',
                                    'Q.sort_id AS ParentSortID',
                                    'related_questions.sort_id AS RelatedSortID',
                                    'related_questions.incre_decre_data_for AS IncreDecreDataFor',
                                    'related_questions.incre_decre_get_data AS IncreDecreGetData',
                                ]
                            );

                    elseif ($req->RelatedSortID >= '0' && $req->QuestionOptionTypeID == "1") :
                        $OptionID = $req->QuestionOptionID['SelectedOptionID'];
                        $RelatedQuestionData->related_question = RelatedQuestion::join('question_option_types AS QOT', 'QOT.id', '=', 'related_questions.question_option_type_id')
                            ->join('options AS O', 'O.id', '=', 'related_questions.option_id')
                            ->join('questions AS Q', 'Q.id', '=', 'O.question_id')
                            ->where(['related_questions.option_id' => $OptionID])
                            ->where('related_question_id.sort_id', '>' . $req->RelatedSortID)
                            ->orderBy('related_questions.sort_id', 'ASC')
                            ->first(
                                [
                                    'Q.id AS ParentQuestionID',
                                    'related_questions.option_id AS ParentOptionID',
                                    'related_questions.id AS RelatedQuestionID',
                                    'related_questions.title AS RelatedQuestionTitle',
                                    'related_questions.question_option_type_id AS RelatedQuestionOptionTypeID',
                                    'related_questions.is_required AS RelatedQuestionIsRequired',
                                    'QOT.title AS RelatedQuestionOptionType',
                                    'Q.sort_id AS ParentSortID',
                                    'related_questions.sort_id AS RelatedSortID',
                                    'related_questions.incre_decre_data_for AS IncreDecreDataFor',
                                    'related_questions.incre_decre_get_data AS IncreDecreGetData',
                                ]
                            );

                    endif;


                    if ($RelatedQuestionData->related_question != "") :
                        $RelatedQuestionData->related_options = RelatedOption::where('related_question_id', $RelatedQuestionData->related_question->RelatedQuestionID)->orderBy('sort_id', 'ASC')->get();
                        $IsRelatedQuestion = true;

                        $AttemptedRelatedQuestionData = AttemptedQuestionOption::where([
                            'question_id' => $RelatedQuestionData->related_question->ParentQuestionID,
                            'option_id' => $RelatedQuestionData->related_question->ParentOptionID,
                            'related_question_id' => $RelatedQuestionData->related_question->RelatedQuestionID,
                            'user_id' => $UserID
                        ])->first();

                        $response['AttemptedRelatedQuestion'] = $AttemptedRelatedQuestionData == null ? '' : $AttemptedRelatedQuestionData;


                        $response['is_related_question'] = true;
                        $response['related_data'] = $RelatedQuestionData;
                        $response['has_data'] = true;
                        $response['status'] = true;


                    endif;

                elseif ($req->QuestionOptionID == 'NULL' && $req->RelatedQuestionOptionID != 'NULL') :

                    $RelatedQuestionData = new \stdClass();
                    $RelatedQuestionData->related_question = "";




                    $ParentQuestionID = $req->RelatedQuestionOptionID['ParentQuestionID'];
                    $ParentOptionID = $req->RelatedQuestionOptionID['ParentOptionID'];
                    $RelatedQuestionID = $req->RelatedQuestionOptionID['RelatedQuestionID'];

                    $AttemptedQuestionOptionData = AttemptedQuestionOption::where(['question_id' => $ParentQuestionID, 'option_id' => $ParentOptionID, 'related_question_id' => $RelatedQuestionID, 'user_id' => $UserID])->first();
                    if ($AttemptedQuestionOptionData != null) :
                        $AttemptedQuestionOption = AttemptedQuestionOption::find($AttemptedQuestionOptionData->id);
                        $AttemptedQuestionOption->user_id = $UserID;
                        if ($req->QuestionOptionTypeID == "1") :
                            $AttemptedQuestionOption->related_option_id = $req->RelatedQuestionOptionID['SelectedOptionID'];
                        elseif ($req->QuestionOptionTypeID == "2" || $req->QuestionOptionTypeID == "4" || $req->QuestionOptionTypeID == "5" || $req->QuestionOptionTypeID == "6") :
                            $AttemptedQuestionOption->related_text_value = $req->RelatedQuestionOptionID['RelatedTextValue'];
                        endif;
                        $AttemptedQuestionOption->save();

                    else :
                        $AttemptedQuestionOption = new AttemptedQuestionOption;
                        $AttemptedQuestionOption->user_id = $UserID;
                        $AttemptedQuestionOption->question_id = $ParentQuestionID;
                        $AttemptedQuestionOption->option_id = $ParentOptionID;
                        $AttemptedQuestionOption->related_question_id = $RelatedQuestionID;
                        if ($req->QuestionOptionTypeID == "1") :
                            $AttemptedQuestionOption->related_option_id = $req->RelatedQuestionOptionID['SelectedOptionID'];
                        elseif ($req->QuestionOptionTypeID == "2" || $req->QuestionOptionTypeID == "4" || $req->QuestionOptionTypeID == "5" || $req->QuestionOptionTypeID == "6") :
                            $AttemptedQuestionOption->related_text_value = $req->RelatedQuestionOptionID['RelatedTextValue'];
                        endif;
                        $AttemptedQuestionOption->save();
                    endif;

                    if ($req->RelatedSortID >= '0') :
                        $RelatedQuestionData->related_question = RelatedQuestion::join('question_option_types AS QOT', 'QOT.id', '=', 'related_questions.question_option_type_id')
                            ->join('options AS O', 'O.id', '=', 'related_questions.option_id')
                            ->join('questions AS Q', 'Q.id', '=', 'O.question_id')
                            ->where(['related_questions.option_id' => $ParentOptionID])
                            ->where('related_questions.sort_id', '>', $req->RelatedSortID)
                            ->orderBy('related_questions.sort_id', 'ASC')
                            ->first(
                                [
                                    'Q.id AS ParentQuestionID',
                                    'related_questions.option_id AS ParentOptionID',
                                    'related_questions.id AS RelatedQuestionID',
                                    'related_questions.title AS RelatedQuestionTitle',
                                    'related_questions.question_option_type_id AS RelatedQuestionOptionTypeID',
                                    'related_questions.is_required AS RelatedQuestionIsRequired',
                                    'QOT.title AS RelatedQuestionOptionType',
                                    'Q.sort_id AS ParentSortID',
                                    'related_questions.sort_id AS RelatedSortID',
                                    'related_questions.incre_decre_data_for AS IncreDecreDataFor',
                                    'related_questions.incre_decre_get_data AS IncreDecreGetData',
                                ]
                            );


                    endif;


                    if ($RelatedQuestionData->related_question != "") :
                        $RelatedQuestionData->related_options = RelatedOption::where('related_question_id', $RelatedQuestionData->related_question->RelatedQuestionID)->orderBy('sort_id', 'ASC')->get();
                        $IsRelatedQuestion = true;

                        $AttemptedRelatedQuestionData = AttemptedQuestionOption::where([
                            'question_id' => $RelatedQuestionData->related_question->ParentQuestionID,
                            'option_id' => $RelatedQuestionData->related_question->ParentOptionID,
                            'related_question_id' => $RelatedQuestionData->related_question->RelatedQuestionID,
                            'user_id' => $UserID
                        ])->first();

                        $response['AttemptedRelatedQuestion'] = $AttemptedRelatedQuestionData == null ? '' : $AttemptedRelatedQuestionData;


                        $response['is_related_question'] = true;
                        $response['AttemptedRelatedQuestion'] = $AttemptedRelatedQuestionData;
                        $response['related_data'] = $RelatedQuestionData;
                        $response['has_data'] = true;
                        $response['status'] = true;


                    endif;

                endif;
                //update here
                if ($IsRelatedQuestion == false) :
                    //Getting Current User's Gender ID - STARTS
                    $UserGenderID = User::find($UserID)->gender;
                    //Getting Current User's Gender ID - ENDS
                    $QuestionData = Question::where('sort_id', '>', $req->SortID)
                        ->whereIn('question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                        ->orderBy('sort_id', 'ASC')->first(['id', 'title', 'question_option_type_id', 'questionnaire_section_id', 'is_address', 'is_required', 'sort_id']);
                    if ($req->QuestionOptionID != "NULL") :
                        if ($req->QuestionOptionID['SelectedOptionID']) :
                            $OptionID = $req->QuestionOptionID['SelectedOptionID'];
                            if ($OptionID == 4 || $OptionID == 6) :
                                if ($QuestionID == 4 || $QuestionID == 5) :
                                    $QuestionData = null;
                                endif;
                            endif;
                        endif;
                    endif;


                    $QDataTemp = json_decode(json_encode($QuestionData), true);
                    if ($QuestionData != null) :
                        $OptionData = Option::where('question_id', $QuestionData->id)->orderBy('sort_id', 'ASC')->get(['id', 'title', 'question_id', 'sort_id']);


                        $AttemptedQuestionData = AttemptedQuestionOption::where(['question_id' => $QuestionData->id, 'user_id' => $UserID])->first();

                        $response['Question'] = $QuestionData;
                        $response['Options'] = $OptionData;
                        $response['AttemptedQuestion'] = $AttemptedQuestionData == null ? '' : $AttemptedQuestionData;
                        $response['has_data'] = true;
                        $response['status'] = true;

                    else :
                        $UpdateReachedEnd = User::find($UserID);
                        $UpdateReachedEnd->reached_end = "1";
                        $UpdateReachedEnd->save();
                        $response['has_data'] = false;
                        $response['status'] = true;
                    endif;


                endif;
            else :
                $response['status'] = true;
                $response['is_locked'] = true;
            endif;
            // else:
            //     $response['status'] = true;
            //     $response['reached_end'] = "1";
            // endif;

            $response['q_section_progress'] = $this->q_section_progress();
        } catch (\Exception $ex) {
            $response['msg'] = 'Exception | ' . $ex->getMessage() . ' on line number ' . $ex->getLine();
            $response['status'] = false;
        }

        return $response;
    }

    public function next_question(Request $req)
    {
        $response = [
            'reached_end' => "0",
            'Question' => '',
            'Options' => '',
            'AttemptedQuestion' => '',
            'AttemptedRelatedQuestion' => '',
            'is_related_question' => false,
            'related_data' => '',
            'status' => false,
            'has_data' => false,
            'is_locked' => false,
            'msg' => '',
            'q_section_progress' => '',
            'req' => $_POST
        ];

        try {
            $UserID = $req->session()->get('user_id');

            $IsLockedStatus = User::find($UserID);
            if ($IsLockedStatus->reached_end == "0") :
                if ($IsLockedStatus->is_locked == "0") :
                    $IsRelatedQuestion = false;
                    if ($req->QuestionOptionID != 'NULL' && $req->RelatedQuestionOptionID == 'NULL') :

                        $QuestionID = $req->QuestionOptionID['ParentQuestionID'];



                        $AttemptedQuestionOptionData = AttemptedQuestionOption::where(['question_id' => $QuestionID, 'user_id' => $UserID])->first();
                        if ($AttemptedQuestionOptionData != null) :


                            if ($req->QuestionOptionTypeID == "1") :
                                $AttemptedOptions = AttemptedQuestionOption::where(['question_id' => $QuestionID, 'user_id' => $UserID, 'option_id' => $req->QuestionOptionID['SelectedOptionID']])->get();
                                if (count($AttemptedOptions) == 0) :
                                    AttemptedQuestionOption::where(['question_id' => $QuestionID, 'user_id' => $UserID])->delete();
                                    if ($QuestionID == 4 || $QuestionID == 5) :
                                        AttemptedQuestionOption::where(['question_id' => 6, 'user_id' => $UserID])->delete(); //also delete the children attempted question
                                    endif;
                                    $AttemptedQuestionOption = new AttemptedQuestionOption;
                                    $AttemptedQuestionOption->user_id = $UserID;
                                    $AttemptedQuestionOption->question_id = $QuestionID;
                                    $AttemptedQuestionOption->option_id = $req->QuestionOptionID['SelectedOptionID'];
                                    $AttemptedQuestionOption->save();
                                endif;

                            elseif ($req->QuestionOptionTypeID == "2" || $req->QuestionOptionTypeID == "4" || $req->QuestionOptionTypeID == "5") :

                                $AttemptedQuestionOption = AttemptedQuestionOption::find($AttemptedQuestionOptionData->id);
                                $AttemptedQuestionOption->user_id = $UserID;

                                $AttemptedQuestionOption->text_value = $req->QuestionOptionID['ParentTextValue'];
                                $AttemptedQuestionOption->save();

                            endif;

                        else :
                            $AttemptedQuestionOption = new AttemptedQuestionOption;
                            $AttemptedQuestionOption->user_id = $UserID;
                            $AttemptedQuestionOption->question_id = $QuestionID;
                            if ($req->QuestionOptionTypeID == "1") :
                                $AttemptedQuestionOption->option_id = $req->QuestionOptionID['SelectedOptionID'];
                            elseif ($req->QuestionOptionTypeID == "2" || $req->QuestionOptionTypeID == "4" || $req->QuestionOptionTypeID == "5") :
                                $AttemptedQuestionOption->text_value = $req->QuestionOptionID['ParentTextValue'];
                            endif;
                            $AttemptedQuestionOption->save();
                        endif;

                        //UPDATE USER GENDER IF QUESTION IS ABOUT GENDER STARTS
                        if ($QuestionID == 1) : //if gender question is answered
                            $GenderID = "";
                            if ($req->QuestionOptionID['SelectedOptionID'] == 1) : // if is male
                                $GenderID = "1";
                            elseif ($req->QuestionOptionID['SelectedOptionID'] == 2) :
                                $GenderID = "2";
                            endif;

                            $UpdateUser = User::find($UserID);
                            $UpdateUser->gender = $GenderID;
                            $UpdateUser->save();
                        endif;
                        //UPDATE USER GENDER IF QUESTION IS ABOUT GENDER ENDS

                        //UPDATE USER MARITIAL STATUS IF QUESTION IS ABOUT MARRIAGE STARTS
                        if ($QuestionID == 4 || $QuestionID == 5) : //if gender question is answered
                            $MaritialStatus = "0";
                            if ($req->QuestionOptionID['SelectedOptionID'] == 3 || $req->QuestionOptionID['SelectedOptionID'] == 5) : // if yes is selected for marriage
                                $MaritialStatus = "1";
                            elseif ($req->QuestionOptionID['SelectedOptionID'] == 4 || $req->QuestionOptionID['SelectedOptionID'] == 6) : // if no is selected
                                $MaritialStatus = "0";
                            endif;

                            $UpdateUser = User::find($UserID);
                            $UpdateUser->is_married = $MaritialStatus;
                            $UpdateUser->save();
                        endif;
                        //UPDATE USER MARITIAL STATUS IF QUESTION IS ABOUT MARRIAGE ENDS

                        //Getting Current User's Details - STARTS
                        $UserDetails = User::find($UserID);
                        $UserGenderID = $UserDetails->gender;
                        $IsMarried = $UserDetails->is_married;
                        //Getting Current User's Details - ENDS


                        $RelatedQuestionData = new \stdClass();
                        $RelatedQuestionData->related_question = "";

                        if ($req->RelatedSortID == 'NULL' && $req->QuestionOptionTypeID == "1") :
                            $OptionID = $req->QuestionOptionID['SelectedOptionID'];
                            $RelatedQuestionData->related_question = RelatedQuestion::join('question_option_types AS QOT', 'QOT.id', '=', 'related_questions.question_option_type_id')
                                ->join('options AS O', 'O.id', '=', 'related_questions.option_id')
                                ->join('questions AS Q', 'Q.id', '=', 'O.question_id')
                                ->where(['related_questions.option_id' => $OptionID])
                                ->whereIn('Q.question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                                ->orderBy('related_questions.sort_id', 'ASC')
                                ->first(
                                    [
                                        'Q.id AS ParentQuestionID',
                                        'related_questions.option_id AS ParentOptionID',
                                        'related_questions.id AS RelatedQuestionID',
                                        'related_questions.title AS RelatedQuestionTitle',
                                        'related_questions.question_option_type_id AS RelatedQuestionOptionTypeID',
                                        'related_questions.is_required AS RelatedQuestionIsRequired',
                                        'related_questions.tooltip_info AS TooltipInfo',
                                        'QOT.title AS RelatedQuestionOptionType',
                                        'Q.sort_id AS ParentSortID',
                                        'related_questions.sort_id AS RelatedSortID',
                                        'related_questions.incre_decre_data_for AS IncreDecreDataFor',
                                        'related_questions.incre_decre_get_data AS IncreDecreGetData',
                                    ]
                                );

                        elseif ($req->RelatedSortID >= '0' && $req->QuestionOptionTypeID == "1") :
                            $OptionID = $req->QuestionOptionID['SelectedOptionID'];
                            $RelatedQuestionData->related_question = RelatedQuestion::join('question_option_types AS QOT', 'QOT.id', '=', 'related_questions.question_option_type_id')
                                ->join('options AS O', 'O.id', '=', 'related_questions.option_id')
                                ->join('questions AS Q', 'Q.id', '=', 'O.question_id')
                                ->where(['related_questions.option_id' => $OptionID])
                                ->whereIn('Q.question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                                ->where('related_question_id.sort_id', '>' . $req->RelatedSortID)
                                ->orderBy('related_questions.sort_id', 'ASC')
                                ->first(
                                    [
                                        'Q.id AS ParentQuestionID',
                                        'related_questions.option_id AS ParentOptionID',
                                        'related_questions.id AS RelatedQuestionID',
                                        'related_questions.title AS RelatedQuestionTitle',
                                        'related_questions.question_option_type_id AS RelatedQuestionOptionTypeID',
                                        'related_questions.is_required AS RelatedQuestionIsRequired',
                                        'related_questions.tooltip_info AS TooltipInfo',
                                        'QOT.title AS RelatedQuestionOptionType',
                                        'Q.sort_id AS ParentSortID',
                                        'related_questions.sort_id AS RelatedSortID',
                                        'related_questions.incre_decre_data_for AS IncreDecreDataFor',
                                        'related_questions.incre_decre_get_data AS IncreDecreGetData',
                                    ]
                                );

                        endif;


                        if ($RelatedQuestionData->related_question != "") :
                            $RelatedQuestionData->related_options = RelatedOption::where('related_question_id', $RelatedQuestionData->related_question->RelatedQuestionID)->orderBy('sort_id', 'ASC')->get();
                            $IsRelatedQuestion = true;

                            $AttemptedRelatedQuestionData = AttemptedQuestionOption::where([
                                'question_id' => $RelatedQuestionData->related_question->ParentQuestionID,
                                'option_id' => $RelatedQuestionData->related_question->ParentOptionID,
                                'related_question_id' => $RelatedQuestionData->related_question->RelatedQuestionID,
                                'user_id' => $UserID
                            ])->first();

                            $response['AttemptedRelatedQuestion'] = $AttemptedRelatedQuestionData == null ? '' : $AttemptedRelatedQuestionData;


                            $response['is_related_question'] = true;
                            $response['related_data'] = $RelatedQuestionData;
                            $response['has_data'] = true;
                            $response['status'] = true;


                        endif;

                    elseif ($req->QuestionOptionID == 'NULL' && $req->RelatedQuestionOptionID != 'NULL') :

                        $RelatedQuestionData = new \stdClass();
                        $RelatedQuestionData->related_question = "";




                        $ParentQuestionID = $req->RelatedQuestionOptionID['ParentQuestionID'];
                        $ParentOptionID = $req->RelatedQuestionOptionID['ParentOptionID'];
                        $RelatedQuestionID = $req->RelatedQuestionOptionID['RelatedQuestionID'];

                        $AttemptedQuestionOptionData = AttemptedQuestionOption::where(['question_id' => $ParentQuestionID, 'option_id' => $ParentOptionID, 'related_question_id' => $RelatedQuestionID, 'user_id' => $UserID])->first();
                        if ($AttemptedQuestionOptionData != null) :
                            $AttemptedQuestionOption = AttemptedQuestionOption::find($AttemptedQuestionOptionData->id);
                            $AttemptedQuestionOption->user_id = $UserID;
                            if ($req->QuestionOptionTypeID == "1") :
                                $AttemptedQuestionOption->related_option_id = $req->RelatedQuestionOptionID['SelectedOptionID'];
                            elseif ($req->QuestionOptionTypeID == "2" || $req->QuestionOptionTypeID == "4" || $req->QuestionOptionTypeID == "5" || $req->QuestionOptionTypeID == "6") :
                                $AttemptedQuestionOption->related_text_value = $req->RelatedQuestionOptionID['RelatedTextValue'];
                            endif;
                            $AttemptedQuestionOption->save();

                        else :
                            $AttemptedQuestionOption = new AttemptedQuestionOption;
                            $AttemptedQuestionOption->user_id = $UserID;
                            $AttemptedQuestionOption->question_id = $ParentQuestionID;
                            $AttemptedQuestionOption->option_id = $ParentOptionID;
                            $AttemptedQuestionOption->related_question_id = $RelatedQuestionID;
                            if ($req->QuestionOptionTypeID == "1") :
                                $AttemptedQuestionOption->related_option_id = $req->RelatedQuestionOptionID['SelectedOptionID'];
                            elseif ($req->QuestionOptionTypeID == "2" || $req->QuestionOptionTypeID == "4" || $req->QuestionOptionTypeID == "5" || $req->QuestionOptionTypeID == "6") :
                                $AttemptedQuestionOption->related_text_value = $req->RelatedQuestionOptionID['RelatedTextValue'];
                            endif;
                            $AttemptedQuestionOption->save();
                        endif;

                        if ($req->RelatedSortID >= '0') :
                            //Getting Current User's Gender ID - STARTS
                            $UserGenderID = User::find($UserID)->gender;
                            //Getting Current User's Gender ID - ENDS
                            $RelatedQuestionData->related_question = RelatedQuestion::join('question_option_types AS QOT', 'QOT.id', '=', 'related_questions.question_option_type_id')
                                ->join('options AS O', 'O.id', '=', 'related_questions.option_id')
                                ->join('questions AS Q', 'Q.id', '=', 'O.question_id')
                                ->where(['related_questions.option_id' => $ParentOptionID])
                                ->where('related_questions.sort_id', '>', $req->RelatedSortID)
                                ->whereIn('Q.question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                                ->orderBy('related_questions.sort_id', 'ASC')
                                ->first(
                                    [
                                        'Q.id AS ParentQuestionID',
                                        'related_questions.option_id AS ParentOptionID',
                                        'related_questions.id AS RelatedQuestionID',
                                        'related_questions.title AS RelatedQuestionTitle',
                                        'related_questions.question_option_type_id AS RelatedQuestionOptionTypeID',
                                        'related_questions.is_required AS RelatedQuestionIsRequired',
                                        'related_questions.tooltip_info AS TooltipInfo',
                                        'QOT.title AS RelatedQuestionOptionType',
                                        'Q.sort_id AS ParentSortID',
                                        'related_questions.sort_id AS RelatedSortID',
                                        'related_questions.incre_decre_data_for AS IncreDecreDataFor',
                                        'related_questions.incre_decre_get_data AS IncreDecreGetData',
                                    ]
                                );


                        endif;


                        if ($RelatedQuestionData->related_question != "") :
                            $RelatedQuestionData->related_options = RelatedOption::where('related_question_id', $RelatedQuestionData->related_question->RelatedQuestionID)->orderBy('sort_id', 'ASC')->get();
                            $IsRelatedQuestion = true;

                            $AttemptedRelatedQuestionData = AttemptedQuestionOption::where([
                                'question_id' => $RelatedQuestionData->related_question->ParentQuestionID,
                                'option_id' => $RelatedQuestionData->related_question->ParentOptionID,
                                'related_question_id' => $RelatedQuestionData->related_question->RelatedQuestionID,
                                'user_id' => $UserID
                            ])->first();

                            $response['AttemptedRelatedQuestion'] = $AttemptedRelatedQuestionData == null ? '' : $AttemptedRelatedQuestionData;


                            $response['is_related_question'] = true;
                            $response['AttemptedRelatedQuestion'] = $AttemptedRelatedQuestionData;
                            $response['related_data'] = $RelatedQuestionData;
                            $response['has_data'] = true;
                            $response['status'] = true;


                        endif;

                    endif;
                    if ($IsRelatedQuestion == false) :
                        $QuestionData = Question::where('sort_id', '>', $req->SortID)->orderBy('sort_id', 'ASC')
                            ->whereIn('question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                            ->first(['id', 'title', 'question_option_type_id', 'questionnaire_section_id', 'is_address', 'is_required', 'sort_id', 'tooltip_info', 'is_marriage_required']);

                        if ($QuestionData != null) :
                            if ($QuestionData->is_marriage_required == 1) :
                                $UserDetails = User::find($UserID);
                                $IsMarried = $UserDetails->is_married;
                                if ($IsMarried != 1) :
                                    $SkipToNextSortID = $req->SortID + 1;
                                    $QuestionData = Question::where('sort_id', '>', $SkipToNextSortID)->orderBy('sort_id', 'ASC')
                                        ->whereIn('question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                                        ->first(['id', 'title', 'question_option_type_id', 'questionnaire_section_id', 'is_address', 'is_required', 'sort_id', 'tooltip_info', 'is_marriage_required']);
                                endif;
                            endif;
                            $OptionData = Option::where('question_id', $QuestionData->id)->orderBy('sort_id', 'ASC')->get(['id', 'title', 'question_id', 'sort_id']);


                            $AttemptedQuestionData = AttemptedQuestionOption::where(['question_id' => $QuestionData->id, 'user_id' => $UserID])->first();

                            $response['Question'] = $QuestionData;
                            $response['Options'] = $OptionData;
                            $response['AttemptedQuestion'] = $AttemptedQuestionData == null ? '' : $AttemptedQuestionData;
                            $response['has_data'] = true;
                            $response['status'] = true;

                        else :
                            $UpdateReachedEnd = User::find($UserID);
                            $UpdateReachedEnd->reached_end = "1";
                            $UpdateReachedEnd->save();
                            $response['has_data'] = false;
                            $response['status'] = true;
                        endif;


                    endif;
                else :
                    $response['status'] = true;
                    $response['is_locked'] = true;
                endif;
            else :
                $response['status'] = true;
                $response['reached_end'] = "1";
            endif;

            $response['q_section_progress'] = $this->q_section_progress();
        } catch (\Exception $ex) {
            $response['msg'] = 'Exception | ' . $ex->getMessage() . ' on line number ' . $ex->getLine();
            $response['status'] = false;
        }

        return $response;
    }



    public function q_section_progress()
    {
        $UserID = Session::get('user_id');
        $QSData = QuestionnaireSection::where('status', '1')->get();
        $FinalArray = array();
        if (count($QSData) > 0) :
            //Getting Current User's Gender ID - STARTS
            $UserGenderID = User::find($UserID)->gender;
            //Getting Current User's Gender ID - ENDS
            foreach ($QSData as $QSKey => $QSItem) :



                $QData = Question::where('questionnaire_section_id', $QSItem->id)
                ->whereIn('question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                ->where('id','<>','6')
                ->get();

                if ($UserGenderID != null) :

                    $QData = Question::where('questionnaire_section_id', $QSItem->id)
                        ->whereIn('question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                        ->where('id','<>','6')
                        ->get();


                endif;
                if (count($QData) > 0) :
                    $AQData = AttemptedQuestionOption::join('questions AS Q', 'Q.id', '=', 'attempted_question_options.question_id')
                        ->whereIn('Q.question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                        ->where(['Q.questionnaire_section_id' => $QSItem->id, 'attempted_question_options.user_id' => $UserID])
                        ->where('Q.id','<>','6')
                        ->groupBy('attempted_question_options.question_id')->get();

                    array_push($FinalArray, [
                        'SectionID' => $QSItem->id,
                        'Section' => $QSItem->title,
                        'RequiredQuestionsCount' => count($QData->toArray()),
                        'AttemptedQuestionsCount' => count($AQData->toArray()),
                        'TotalPercentage' => number_format((count($AQData->toArray()) / count($QData->toArray())) * 100, 0)
                    ]);
                endif;

            endforeach;
        endif;
        return $FinalArray;
    }

    public function resume_question()
    {
        $response = [
            'reached_end' => '0',
            'Question' => '',
            'Options' => '',
            'AttemptedQuestion' => '',
            'AttemptedRelatedQuestion' => '',
            'is_related_question' => false,
            'related_data' => '',
            'status' => false,
            'has_data' => false,
            'is_locked' => false,
            'msg' => '',
            'q_section_progress' => ''
        ];
        $UserID = Session::get('user_id');
        //Getting Current User's Gender ID - STARTS
        $UserGenderID = User::find($UserID)->gender;
        //Getting Current User's Gender ID - ENDS
        try {
            $IsLockedStatus = User::find($UserID);
            if ($IsLockedStatus->reached_end == "0") :

                if ($IsLockedStatus->is_locked == "0") :
                    $LastAttemptedData = AttemptedQuestionOption::where(['user_id' => $UserID])->orderBy('id', 'DESC')->first();
                    if ($LastAttemptedData != null) :
                        $QuestionID = $LastAttemptedData->question_id;

                        $questionQuery = Question::where('id', $QuestionID)
                            ->select('id', 'title', 'question_option_type_id', 'questionnaire_section_id', 'is_address', 'is_required', 'sort_id', 'tooltip_info');

                        if ($UserGenderID !== null) {
                            $questionQuery->whereIn('question_for_gender_id', [$UserGenderID, 3]); // 3 id is for both genders
                        }

                        $QuestionData = $questionQuery->first();


                        $OptionData = Option::where('question_id', $QuestionData->id)->orderBy('sort_id', 'ASC')->get(['id', 'title', 'question_id', 'sort_id']);

                        $AttemptedQuestionData = AttemptedQuestionOption::where(['question_id' => $QuestionData->id, 'user_id' => $UserID])->first();

                        $response['Question'] = $QuestionData;
                        $response['Options'] = $OptionData;
                        $response['AttemptedQuestion'] = $AttemptedQuestionData == null ? '' : $AttemptedQuestionData;
                        $response['has_data'] = true;
                        $response['status'] = true;

                    else :
                        // $QuestionData = Question::orderBy('sort_id', 'ASC')->first(['id', 'title', 'question_option_type_id', 'questionnaire_section_id', 'is_address', 'is_required', 'sort_id', 'tooltip_info']);
                        // if ($UserGenderID != null) :
                        //     $QuestionData = Question::orderBy('sort_id', 'ASC')
                        //         ->whereIn('question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                        //         ->first(['id', 'title', 'question_option_type_id', 'questionnaire_section_id', 'is_address', 'is_required', 'sort_id', 'tooltip_info']);
                        // endif;

                        $questionQuery = Question::orderBy('sort_id', 'ASC')
                            ->select('id', 'title', 'question_option_type_id', 'questionnaire_section_id', 'is_address', 'is_required', 'sort_id', 'tooltip_info');

                        if ($UserGenderID !== null) {
                            $questionQuery->whereIn('question_for_gender_id', [$UserGenderID, 3]); // 3 id is for both genders
                        }

                        $QuestionData = $questionQuery->first();


                        $OptionData = Option::where(['question_id' => $QuestionData->id])->orderBy('sort_id', 'ASC')->get(['id', 'title', 'question_id', 'sort_id']);

                        $AttemptedQuestionData = AttemptedQuestionOption::where(['question_id' => $QuestionData->id, 'user_id' => $UserID])->first(['id', 'option_id']);

                        $response['Question'] = $QuestionData;
                        $response['Options'] = $OptionData;
                        $response['AttemptedQuestion'] = $AttemptedQuestionData == null ? '' : $AttemptedQuestionData;
                        $response['has_data'] = true;
                        $response['status'] = true;

                    endif;

                else :
                    $response['status'] = true;
                    $response['is_locked'] = true;
                endif;
            else :
                $response['status'] = true;
                $response['reached_end'] = "1";
            endif;
            $response['q_section_progress'] = $this->q_section_progress();
        } catch (\Exception $ex) {
            $response['msg'] = 'Exception | ' . $ex->getMessage() + ' on line number ' . $ex->getLine();
            $response['status'] = false;
        }
        return $response;
    }

    public function back_question(Request $req)
    {
        $response = [
            'Question' => '',
            'Options' => '',
            'AttemptedQuestion' => '',
            'is_related_question' => false,
            'related_data' => '',
            'status' => false,
            'has_data' => false,
            'is_locked' => false,
            'msg' => '',
            'req' => array(
                'SortID' => $req->SortID,
                'NoMoreQuestions' => $req->NoMoreQuestions
            ),
            'q_section_progress' => ''
        ];

        $UserID = $req->session()->get('user_id');
        //Getting Current User's Gender ID - STARTS
        $UserDetails = User::find($UserID);
        $UserGenderID = $UserDetails->gender;
        $IsMarried = $UserDetails->is_married;
        //Getting Current User's Gender ID - ENDS

        try {
            $IsLockedStatus = User::find($UserID);
            if ($IsLockedStatus->is_locked == "0") :

                $QuestionData = null;

                if ($req->NoMoreQuestions == 'false') :

                    if ($req->IsRelatedQuestion == 'true') :
                        $QuestionData = Question::where('id', $req->ParentQuestionID)
                            ->whereIn('question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                            ->orderBy('sort_id', 'ASC')->first(['id', 'title', 'question_option_type_id', 'questionnaire_section_id', 'is_address', 'is_required', 'sort_id', 'tooltip_info']);
                    else :

                        if ($req->SortID == "0") :

                            $QuestionData = Question::orderBy('sort_id', 'ASC')
                                ->whereIn('question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                                ->first(['id', 'title', 'question_option_type_id', 'questionnaire_section_id', 'is_address', 'is_required', 'sort_id', 'tooltip_info']);

                        else :

                            $QuestionData = Question::where('sort_id', '<', $req->SortID)
                                ->whereIn('question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                                ->orderBy('sort_id', 'DESC')->first(['id', 'title', 'question_option_type_id', 'questionnaire_section_id', 'is_address', 'is_required', 'sort_id', 'tooltip_info', 'is_marriage_required']);

                            if ($QuestionData->is_marriage_required == 1) :
                                if ($IsMarried != 1) :
                                    $QuestionID = $QuestionData->id;
                                    $QuestionData = Question::where('sort_id', '<', $QuestionData->sort_id)
                                        ->whereIn('question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                                        ->orderBy('sort_id', 'DESC')->first(['id', 'title', 'question_option_type_id', 'questionnaire_section_id', 'is_address', 'is_required', 'sort_id', 'tooltip_info', 'is_marriage_required']);
                                endif;
                            endif;

                        endif;

                    endif;

                else :

                    $QuestionData = Question::orderBy('sort_id', 'DESC')
                        ->whereIn('question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                        ->first(['id', 'title', 'question_option_type_id', 'questionnaire_section_id', 'is_address', 'is_required', 'sort_id', 'tooltip_info', 'is_marriage_required']);

                    if ($QuestionData->is_marriage_required == 1) :
                        if ($IsMarried != 1) :
                            $SkipToNextSortID = $QuestionData->sort_id - 1;
                            // $QuestionData = Question::where('sort_id', '<', $SkipToNextSortID)
                            //     ->whereIn('question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                            //     ->orderBy('sort_id', 'DESC')->first(['id', 'title', 'question_option_type_id', 'questionnaire_section_id', 'is_address', 'is_required', 'sort_id', 'tooltip_info', 'is_marriage_required']);

                            $QuestionData = Question::orderBy('sort_id', 'DESC')
                                ->where('sort_id', '<', $QuestionData->sort_id)
                                ->whereIn('question_for_gender_id', [$UserGenderID, 3]) //3 id is for both genders
                                ->first(['id', 'title', 'question_option_type_id', 'questionnaire_section_id', 'is_address', 'is_required', 'sort_id', 'tooltip_info']);
                        endif;
                    endif;

                endif;

                if ($QuestionData != null) :
                    $OptionData = Option::where('question_id', $QuestionData->id)->orderBy('sort_id', 'ASC')->get(['id', 'title', 'question_id', 'sort_id']);


                    $AttemptedQuestionData = AttemptedQuestionOption::where(['question_id' => $QuestionData->id, 'user_id' => $UserID])->first();

                    $response['Question'] = $QuestionData;
                    $response['Options'] = $OptionData;
                    $response['AttemptedQuestion'] = $AttemptedQuestionData == null ? '' : $AttemptedQuestionData;
                    $response['has_data'] = true;
                    $response['status'] = true;

                else :
                    $response['has_data'] = false;
                    $response['status'] = true;
                endif;
            else :
                $response['status'] = true;
                $response['is_locked'] = true;
            endif;
            $response['q_section_progress'] = $this->q_section_progress();
        } catch (\Exception $ex) {
            $response['msg'] = 'Exception | ' . $ex->getMessage() + ' on line number ' . $ex->getLine();
            $response['status'] = false;
        }

        return $response;
    }

    public function submit_query(Request $req)
    {
        try {
            $UserID = $req->session()->get('user_id');
            $User = User::find($UserID);
            
            // Always set is_locked = 1 when questionnaire is submitted
            $UpdateUserIsLocked = User::find($UserID);
            $UpdateUserIsLocked->is_locked = 1;
            $UpdateUserIsLocked->queries_submission_datetime = date('Y-m-d H:i:s');
            
            if ($UpdateUserIsLocked->save()) :
                $req->session()->put('is_locked', '1');
                
                // Check if user has already paid
                if ($User->is_payment_done == 1) {
                    // User has paid, send email and redirect to forms
                    $FullName = $req->session()->get('full_name');
                    $Email = $req->session()->get('email');

                    Mail::send('emails.will_submission_confirmation', compact('FullName'), function ($message) use ($Email) {
                        $message->to($Email);
                        $message->from(config('mail.from.address'));
                        $message->subject('Confirmation Email');
                    });
                    
                    return redirect()->route('customer_forms');
                } else {
                    // User hasn't paid, redirect to payment with user ID in URL
                    return redirect()->route('payment_checkout', ['user_id' => $UserID]);
                }
            endif;
        } catch (\Exception $ex) {
            echo $this->ExceptionMessage($ex);
        }
    }


    public function review_all_questions()
    {
        $UserID = Session::get('user_id');
        $FinalArray = array();

        $AttemptedByUsers = AttemptedQuestionOption::join('users AS U', 'U.id', '=', 'attempted_question_options.user_id')
            ->where('attempted_question_options.user_id', $UserID)
            ->groupBy(['U.id', 'U.name', 'U.is_locked'])->get(['U.id AS UserID', 'U.name AS UserFullName', 'U.is_locked AS IsLocked']);

        foreach ($AttemptedByUsers as $AUKey => $AUItem) :
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
        // $FinalArray = json_encode($FinalArray);

        return $FinalArray;
    }
}