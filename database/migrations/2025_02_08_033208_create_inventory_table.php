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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('material_name', 255);
            $table->integer('stocks') -> unsigned(); // Stocks can't be negative
            $table->integer('threshold') -> unsigned();
            $table->integer('measurement_quantity'); // Change this from decimal to integer
            $table->string('measurement_unit', 50); // Add the unit of measurement, e.g., 'cm', 'm', 'kg'
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
        Schema::dropIfExists('inventory');
    }
};