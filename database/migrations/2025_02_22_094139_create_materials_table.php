<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dr_id');
            $table->foreign('dr_id')->references('id')->on('drs')->onDelete('cascade');
            $table->string('material_name');
            $table->integer('measurement'); // Now an integer instead of string
            $table->string('unit'); // New column for unit
            $table->integer('material_quantity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};