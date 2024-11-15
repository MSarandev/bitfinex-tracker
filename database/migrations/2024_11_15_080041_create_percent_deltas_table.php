<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public const TIMEFRAME_FLAGS = ["D", "W", "M", "Y"];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('percent_deltas', static function (Blueprint $table) {
            $table->id()->unique();
            $table->foreignId('user_id')->constrained(
                table: 'users', indexName: 'percent_deltas_user_id'
            );
            $table->enum('timeframe_flag', self::TIMEFRAME_FLAGS);
            $table->integer('timeframe_value');
            $table->float('percent_change');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('percent_deltas');
    }
};
