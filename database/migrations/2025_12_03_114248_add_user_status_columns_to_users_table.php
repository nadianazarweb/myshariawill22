<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserStatusColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_email_verified')) {
                $table->tinyInteger('is_email_verified')->default(0)->after('email_verification_token');
            }
            if (!Schema::hasColumn('users', 'is_locked')) {
                $table->tinyInteger('is_locked')->default(0)->after('is_email_verified');
            }
            if (!Schema::hasColumn('users', 'is_payment_done')) {
                $table->tinyInteger('is_payment_done')->default(0)->after('is_locked');
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
            if (Schema::hasColumn('users', 'is_email_verified')) {
                $table->dropColumn('is_email_verified');
            }
            if (Schema::hasColumn('users', 'is_locked')) {
                $table->dropColumn('is_locked');
            }
            if (Schema::hasColumn('users', 'is_payment_done')) {
                $table->dropColumn('is_payment_done');
            }
        });
    }
}
