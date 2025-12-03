<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'contact_no')) {
                $table->string('contact_no')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->tinyInteger('status')->default(0)->after('settings');
            }
            if (!Schema::hasColumn('users', 'ref_key')) {
                $table->string('ref_key')->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'email_verification_token')) {
                $table->string('email_verification_token')->nullable()->after('email_verified_at');
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
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
            if (Schema::hasColumn('users', 'contact_no')) {
                $table->dropColumn('contact_no');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'ref_key')) {
                $table->dropColumn('ref_key');
            }
            if (Schema::hasColumn('users', 'email_verification_token')) {
                $table->dropColumn('email_verification_token');
            }
        });
    }
}
