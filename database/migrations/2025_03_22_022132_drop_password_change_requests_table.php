<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPasswordChangeRequestsTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('password_change_requests');
    }

    public function down()
    {
        // If you want to restore the table in case of rollback, you can recreate it here.
        Schema::create('password_change_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('token');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}