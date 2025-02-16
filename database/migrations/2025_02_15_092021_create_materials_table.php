<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dr_id');
            $table->foreign('dr_id')->references('id')->on('drs')->onDelete('cascade');
            $table->string('material_name');
            $table->string('measurement_unit');
            $table->integer('material_quantity'); // Fixed column name
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('materials');
    }
};