<?php

use App\Http\Controllers\AccountantController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FrontEndController;
use App\Http\Controllers\BackEndController;
use App\Http\Middleware\CheckCustomerIsNotLocked;
use App\Http\Middleware\CheckCustomerIsLocked;


use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckLoginSession;
use App\Http\Middleware\CheckLoginRole;
use App\Http\Middleware\CheckSession;
use App\Http\Middleware\CheckAdminSession;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;


Route::get('/smtp-probe', function () {
    $targets = [
        ['desc' => 'Remote 587 (STARTTLS)', 'proto' => 'tcp://', 'host' => 'myshariawill.co.uk', 'port' => 587],
        ['desc' => 'Remote 465 (SSL)',      'proto' => 'ssl://', 'host' => 'myshariawill.co.uk', 'port' => 465],
        ['desc' => 'Local 25',              'proto' => 'tcp://', 'host' => '127.0.0.1',          'port' => 25],
    ];
    $out = [];
    foreach ($targets as $t) {
        $errno = $errstr = null;
        $start = microtime(true);
        $fp = @stream_socket_client($t['proto'].$t['host'].':'.$t['port'], $errno, $errstr, 8);
        $ms = round((microtime(true) - $start) * 1000);
        $out[] = $t['desc'].': '.($fp ? "OK ({$ms} ms)" : "FAIL {$errno} {$errstr}");
        if ($fp) fclose($fp);
    }
    return nl2br(implode("\n", $out));
});


Route::get('/mail-test', function () {
    Artisan::call('optimize:clear');
    try {
        Mail::raw('Test via sendmail on cPanel.', function ($m) {
            $m->to('tayyab.pucit@gmail.com')->subject('Laravel Mail Test');
        });
        return 'Test email dispatched.';
    } catch (\Throwable $e) {
        return 'Mail failed: ' . $e->getMessage();
    }
});



Route::get('/mail-proof', function () {
    $traceId = (string) Str::uuid();

    try {
        Mail::raw('This is a test email to check if mail is working.', function ($m) use ($traceId) {
            $m->to('tayyab.pucit@gmail.com');
            $m->from(config('mail.from.address'), config('mail.from.name'));
            $m->subject('Test Email');

            // Add a custom header you can search in the recipient mailbox
            $m->getSwiftMessage()->getHeaders()->addTextHeader('X-Debug-Trace', $traceId);
        });

        Log::info('Mail sent without exception', ['trace' => $traceId]);
        return response()->json(['ok' => true, 'trace' => $traceId]);
    } catch (\Throwable $e) {
        Log::error('Mail send exception', ['error' => $e->getMessage(), 'trace' => $traceId]);
        return response()->json(['ok' => false, 'error' => $e->getMessage(), 'trace' => $traceId], 500);
    }
});


Route::get('/mail-smtp-debug', function () {
    $swift = Mail::getSwiftMailer();
    $logger = new \Swift_Plugins_Loggers_ArrayLogger();
    $swift->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));

    try {
        Mail::raw('SMTP debug test body', function ($m) {
            $m->to('tayyab.pucit@gmail.com');
            $m->from(config('mail.from.address'), config('mail.from.name'));
            $m->subject('SMTP Debug Test');
        });

        return response("<pre>SUCCESS\n\n".$logger->dump()."</pre>");
    } catch (\Throwable $e) {
        return response("<pre>ERROR: ".$e->getMessage()."\n\n".$logger->dump()."</pre>", 500);
    }
});


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//USER LOGIN STARTS
Route::middleware([CheckLoginSession::class])->group(function () {
    Route::get("/", [FrontEndController::class, 'index'])->name('index');
    Route::get("/auth/login", [FrontEndController::class, 'login'])->name('login');
    Route::post("/auth/login/submit", [FrontEndController::class, 'login_submit'])->name('userlogin_submit');

    //USER REGISTER STARTS
    Route::get("/auth/register", [FrontEndController::class, 'register'])->name('register');
    Route::get("/take_to_stripe_checkout", [FrontEndController::class, 'take_to_stripe_checkout'])->name('take_to_stripe_checkout');


    Route::get("/validateusername/{UserName}", [FrontEndController::class, 'validateusername']);
    Route::get("/validateemail/{Email}", [FrontEndController::class, 'validateemail']);

    Route::post("/auth/register/submit", [FrontEndController::class, 'register_submit'])->name('register_submit');

    Route::get('/verifyemail', [FrontEndController::class, 'verify_email']);



    Route::get('/auth/forgot_your_password',[FrontEndController::class, 'forgot_your_password'])->name('forgot_your_password');
    Route::post("/auth/forgot_your_password/submit", [FrontEndController::class, 'forgot_your_password_submit'])->name('forgot_your_password_submit');
    Route::get("/auth/reset_password", [FrontEndController::class, 'reset_password'])->name('reset_password');
    Route::post("/auth/reset_password/submit", [FrontEndController::class, 'reset_password_submit'])->name('reset_password_submit');

    //USER REGISTER ENDS

});
//USER LOGIN ENDS

// Simple test route to isolate the issue (outside middleware for testing)
Route::get("/test/payment/{user_id}", function($user_id) {
    return view('frontend.user.payment_test', compact('user_id'));
})->name('test_payment');

// Test route for payment success (outside middleware for testing)
Route::get("/test/payment_success", function() {
    return response()->json(['message' => 'Test payment success route working']);
})->name('test_payment_success');

// Payment status route - must be outside middleware to handle Stripe redirects
Route::get("/payment/{PaymentStatus}", [FrontEndController::class, 'payment_status'])->name('payment_status');

//USER PANEL STARTS
Route::middleware([CheckSession::class])->group(function () {

    // Payment route for logged-in users
    Route::get("/payment/checkout/{user_id}", [FrontEndController::class, 'take_to_stripe_checkout'])->name('payment_checkout');

    Route::post('/next_question', [FrontEndController::class, 'next_question']);
    Route::post('/update_question', [FrontEndController::class, 'update_question']);
    Route::get('/back_question/{SortID}/{NoMoreQuestions}/{ParentQuestionID}/{IsRelatedQuestion}', [FrontEndController::class, 'back_question']);
    Route::get('/resume_question', [FrontEndController::class, 'resume_question']);
    Route::post('/submit_query', [FrontEndController::class, 'submit_query']);


    Route::group(['prefix' => 'customer'], function () {
        Route::middleware([CheckCustomerIsLocked::class])->group(function () {
            Route::get('/queries', [FrontEndController::class, 'queries'])->name('customer_queries');
            Route::get('/q_section_progress', [FrontEndController::class, 'q_section_progress'])->name('q_section_progress');
            Route::get('/review_all_questions',[FrontEndController::class, 'review_all_questions'])->name('review_all_questions');
        });
        Route::middleware([CheckCustomerIsNotLocked::class])->group(function () {
            Route::get('/dashboard', [CustomerController::class, 'index'])->name('customer_dashboard');
            Route::get('/forms', [CustomerController::class, 'forms'])->name('customer_forms');
            Route::post('/request_changes', [CustomerController::class, 'request_changes'])->name('request_changes');
            Route::post('/final_approve', [CustomerController::class, 'final_approve'])->name('final_approve');
            Route::get('/edit_attempted_answer/{QuestionID}', [CustomerController::class, 'edit_attempted_answer'])->name('customer_edit_attempted_answer');
            Route::post('/update_attempted_answer',[CustomerController::class, 'update_attempted_answer'])->name('customer_update_attempted_answer');
            Route::post('/change_password',[CustomerController::class, 'change_password'])->name('customer_change_password');
            Route::get('/settings',[CustomerController::class, 'settings'])->name('customer_settings');
            Route::post('/update_settings',[CustomerController::class, 'update_settings'])->name('customer_update_settings');


            Route::get('/book_appointment',[CustomerController::class, 'book_appointment'])->name('customer_book_appointment');


            Route::post('/appointment',[CustomerController::class, 'add_appointment'])->name('customer_add_appointment');

            Route::post('/booked_time_slots',[CustomerController::class, 'booked_time_slots']);

            Route::post('/appointment_purchase',[CustomerController::class, 'appointment_purchase'])->name('customer_appointment_purchase');

            Route::get("/appointment_payment/{PaymentStatus}", [CustomerController::class, 'appointment_payment_status']);

        });
    });

});
//USER PANEL ENDS

Route::get("/generate-pdf", [AccountantController::class, 'generatePdf'])->name('generatePdf');

Route::post("/login/submit", [BackEndController::class, 'login_submit'])->name('login_submit');

Route::group(['middleware' => 'instaload'], function () {
    Route::middleware(['CheckLoginRole:1'])->group(function () {
        Route::group(['prefix' => 'dashboard'], function () {
            Route::get("/home", [BackEndController::class, 'dashboard'])->name('dashboard');
            Route::get("/users/{refkey}", [BackEndController::class, 'users'])->name('users');

            Route::get("/questions", [BackEndController::class, 'questions'])->name('questions');
            Route::get("/questions/create", [BackEndController::class, 'create_question'])->name('create_question');
            Route::get("/questions/edit/{QuestionID}", [BackEndController::class, 'edit_question'])->name('edit_question');
            Route::post("/questions/store_question", [BackEndController::class, 'store_question'])->name('store_question');
            Route::post("/questions/update_question_sorting", [BackEndController::class, 'update_question_sorting'])->name('update_question_sorting');

            Route::post("/questions/update_question", [BackEndController::class, 'update_question'])->name('update_question');


            Route::get('/all_forms', [BackEndController::class, 'all_forms'])->name('admin_all_forms');

            Route::post('/assign_to_accountant', [BackEndController::class, 'assign_to_accountant'])->name('assign_to_accountant');




            Route::get("/check", [BackEndController::class, 'check_username'])->name('check_username');
            Route::post("/user/submit", [BackEndController::class, 'user_submit'])->name('user_submit');
            Route::get("/profile", [BackEndController::class, 'profile'])->name('profile');
            Route::post("/profile-update-submit", [BackEndController::class, 'profile_update_submit'])->name('profile_update_submit');
            Route::get("/deactive-account/{userid}", [BackEndController::class, 'deactive_account'])->name('deactive_account');
            Route::get("/active-account/{userid}", [BackEndController::class, 'active_account'])->name('active_account');
            Route::get("/logout", [BackEndController::class, 'logout'])->name('logout');
        });
    });

    //IF IS ACCOUNTANT STARTS
    Route::middleware(['CheckLoginRole:7'])->group(function () {
        Route::group(['prefix' => 'accountant'], function () {
            Route::get('/dashboard', [AccountantController::class, 'index'])->name('accountant_dashboard');

            Route::post('/update_attempted_answer',[AccountantController::class, 'update_attempted_answer'])->name('update_attempted_answer');


            Route::group(['prefix' => 'my_forms'], function () {
                Route::get('/', [AccountantController::class, 'my_forms'])->name('accountant_my_forms');
                Route::get('/edit_attempted_answer/{UserID}/{QuestionID}', [AccountantController::class, 'edit_attempted_answer'])->name('myforms_edit_attempted_answer');
                // Route::post('/send_report',[AccountantController::class, 'send_report'])->name('send_report');
                Route::post('/send_report', [AccountantController::class, 'send_report'])->name('send_report');
                Route::post('/creating_report',[AccountantController::class, 'creating_report'])->name('creating_report');
                Route::post('/update_read_status',[AccountantController::class, 'update_read_status'])->name('update_read_status');
            });


            Route::group(['prefix' => 'requests_for_changes'], function () {
                Route::get('/', [AccountantController::class, 'requests_for_changes'])->name('requests_for_changes');
                Route::get('/edit_requested_answer/{UserID}/{QuestionID}', [AccountantController::class, 'edit_attempted_answer'])->name('edit_attempted_answer');

            });

            Route::group(['prefix' => 'reports'], function () {
                Route::get('/', [AccountantController::class, 'reports'])->name('reports');
                Route::get('/fetch_reports/{ReportType}', [AccountantController::class, 'fetch_reports'])->name('fetch_reports');
            });


            Route::post('/mark_as_fulfilled', [AccountantController::class, 'mark_as_fulfilled'])->name('mark_as_fulfilled');

            Route::get("/logout", [AccountantController::class, 'logout'])->name('accountant_logout');
        });
    });
    //IF IS ACCOUNTANT ENDS

    //IF IS MANAGER STARTS
    Route::middleware(['CheckLoginRole:8'])->group(function () {
        Route::group(['prefix' => 'manager'], function () {
            Route::get('/dashboard', [ManagerController::class, 'index'])->name('manager_dashboard');
            Route::get('/reports', [ManagerController::class, 'reports'])->name('manager_reports');
            Route::get('/approved_reports', [ManagerController::class, 'approved_reports'])->name('manager_approved_reports');

            Route::post('/update_approval_status', [ManagerController::class, 'update_approval_status'])->name('manager_update_approval_status');
            Route::post('/send_email', [ManagerController::class, 'send_email'])->name('manager_send_email');

            Route::get('/appointments', [ManagerController::class, 'appointments'])->name('manager_appointments');

            Route::post('/update_appointment_approval_status', [ManagerController::class, 'update_appointment_approval_status'])->name('manager_update_appointment_approval_status');

            Route::post('/mark_as_arrived', [ManagerController::class, 'mark_as_arrived'])->name('manager_mark_as_arrived');



            Route::get("/logout", [ManagerController::class, 'logout'])->name('manager_logout');
        });
    });
    //IF IS MANAGER ENDS

});
Route::middleware([CheckAdminSession::class])->group(function () {
    Route::group(['prefix' => 'admin'], function () {
        Route::get('/', function () {
            return redirect('/admin/login');
        });
        Route::get('/login', [BackEndController::class, 'index']);
    });
});

Route::get('/LogoutCustomer', function () {
    Session::forget(['user_id', 'role_id','user_name', 'full_name', 'email', 'is_locked']);
    return redirect('/');
});

Route::get('/q_section_progress',[FrontEndController::class,'q_section_progress']);


