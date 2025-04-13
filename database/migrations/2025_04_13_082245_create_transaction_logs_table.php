<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // Action performed in the transaction log
            $table->string('performed_by'); // Name of the user who performed the action
            $table->string('role'); // Role of the user who performed the action
            $table->timestamp('timestamp'); // Timestamp of the action
            $table->json('transaction_details'); // JSON column to store additional details like materials
            $table->timestamps(); // Laravel's default created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_logs');
    }
}