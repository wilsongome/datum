<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('request_results', function (Blueprint $table) {
            $table->datetime('batch');
            $table->integer('order_number', false, true);
            $table->string('str_in', 255);
            $table->string('key_found', 8);
            $table->string('hash', 32);
            $table->integer('tries', false, true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::dropIfExists('request_results');
    }
};
