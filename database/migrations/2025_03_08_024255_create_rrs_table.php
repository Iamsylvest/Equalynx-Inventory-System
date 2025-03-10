<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dr_id');
            $table->foreign('dr_id')->references('id')->on('drs')->onDelete('cascade');
            $table->string('rr_number')->unique();
            $table->string('name');
            $table->string('project_name');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete(); // Ensures user deletion doesn't cause issues
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('location');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->text('remarks')->nullable();
            $table->string('return_proof')->nullable();
            $table->string('return_proof_original_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rrs');
    }
};
