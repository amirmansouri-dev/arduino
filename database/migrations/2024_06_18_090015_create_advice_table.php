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
        Schema::create('advice', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->integer('temperature_min')->nullable();
            $table->integer('temperature_max')->nullable();
            $table->integer('humidity_min')->nullable();
            $table->integer('humidity_max')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advices', function (Blueprint $table) {
            $table->dropColumn('temperature_min');
            $table->dropColumn('temperature_max');
            $table->dropColumn('humidity_min');
            $table->dropColumn('humidity_max');
        });

    }
};
