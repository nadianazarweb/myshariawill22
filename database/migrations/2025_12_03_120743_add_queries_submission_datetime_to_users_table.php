<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQueriesSubmissionDatetimeToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'queries_submission_datetime')) {
                $table->timestamp('queries_submission_datetime')->nullable()->after('is_payment_done');
            }
            if (!Schema::hasColumn('users', 'customer_approved')) {
                $table->tinyInteger('customer_approved')->default(0)->after('queries_submission_datetime');
            }
            if (!Schema::hasColumn('users', 'payment_date')) {
                $table->timestamp('payment_date')->nullable()->after('is_payment_done');
            }
            if (!Schema::hasColumn('users', 'pre_payment_session_key')) {
                $table->string('pre_payment_session_key')->nullable()->after('payment_date');
            }
            if (!Schema::hasColumn('users', 'payment_response')) {
                $table->text('payment_response')->nullable()->after('pre_payment_session_key');
            }
            if (!Schema::hasColumn('users', 'reached_end')) {
                $table->string('reached_end')->default('0')->after('payment_response');
            }
            if (!Schema::hasColumn('users', 'assigned_to_accountant_id')) {
                $table->unsignedBigInteger('assigned_to_accountant_id')->nullable()->after('reached_end');
            }
            if (!Schema::hasColumn('users', 'assigned_to_accountant_date')) {
                $table->timestamp('assigned_to_accountant_date')->nullable()->after('assigned_to_accountant_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'queries_submission_datetime')) {
                $table->dropColumn('queries_submission_datetime');
            }
            if (Schema::hasColumn('users', 'customer_approved')) {
                $table->dropColumn('customer_approved');
            }
            if (Schema::hasColumn('users', 'payment_date')) {
                $table->dropColumn('payment_date');
            }
            if (Schema::hasColumn('users', 'pre_payment_session_key')) {
                $table->dropColumn('pre_payment_session_key');
            }
            if (Schema::hasColumn('users', 'payment_response')) {
                $table->dropColumn('payment_response');
            }
            if (Schema::hasColumn('users', 'reached_end')) {
                $table->dropColumn('reached_end');
            }
            if (Schema::hasColumn('users', 'assigned_to_accountant_id')) {
                $table->dropColumn('assigned_to_accountant_id');
            }
            if (Schema::hasColumn('users', 'assigned_to_accountant_date')) {
                $table->dropColumn('assigned_to_accountant_date');
            }
        });
    }
}
