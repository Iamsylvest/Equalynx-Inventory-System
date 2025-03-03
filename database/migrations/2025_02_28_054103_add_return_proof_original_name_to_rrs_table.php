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
    public function up(): void
    {
        Schema::table('rrs', function (Blueprint $table) {
            $table->string('return_proof_original_name')->nullable();
        });
    }
    
    public function down(): void
    {
        Schema::table('rrs', function (Blueprint $table) {
            $table->dropColumn('return_proof_original_name');
        });
    }
};
