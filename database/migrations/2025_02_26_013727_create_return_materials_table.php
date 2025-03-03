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
        Schema::create('return_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rr_id');
            $table->foreign('rr_id')->references('id')->on('rrs')->onDelete('cascade');
            $table->string('material_name');
            $table->integer('measurement');
            $table->string('unit');
            $table->integer('material_quantity');
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
        Schema::dropIfExists('return_materials');
    }
};