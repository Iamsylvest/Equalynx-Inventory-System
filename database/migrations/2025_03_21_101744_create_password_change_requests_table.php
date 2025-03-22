<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordChangeRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('password_change_requests', function (Blueprint $table) {
            $table->id(); // Primary key, auto-incrementing
            $table->unsignedBigInteger('user_id'); // Foreign key to users table
            $table->string('token'); // The password change token
            $table->timestamp('expires_at'); // Expiration time of the token
            $table->timestamps(); // Created at and updated at columns

            // Foreign key constraint to the users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('password_change_requests');
    }
}
