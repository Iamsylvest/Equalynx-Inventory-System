<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('drs', function (Blueprint $table) {
            $table->string('remarks')->nullable()->after('status'); // Choose an existing column
        });
    }

    public function down()
    {
        Schema::table('drs', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};