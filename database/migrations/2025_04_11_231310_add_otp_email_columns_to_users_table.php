<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtpEmailColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email_otp')->nullable();             // Add email OTP column
            $table->timestamp('email_otp_expires_at')->nullable(); // Add email OTP expiration column
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
            // Drop the email OTP columns if rolling back
            $table->dropColumn('email_otp');
            $table->dropColumn('email_otp_expires_at');
        });
    }
}