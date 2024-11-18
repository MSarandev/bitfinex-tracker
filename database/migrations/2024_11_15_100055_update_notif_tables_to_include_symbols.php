<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('price_actions', static function (Blueprint $table) {
            $table->string('symbol')->nullable();
        });

        Schema::table('percent_deltas', static function (Blueprint $table) {
            $table->string('symbol')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_actions', static function (Blueprint $table) {
            $table->dropColumn('symbol');
        });

        Schema::table('percent_deltas', static function (Blueprint $table) {
            $table->dropColumn('symbol');
        });
    }
};
