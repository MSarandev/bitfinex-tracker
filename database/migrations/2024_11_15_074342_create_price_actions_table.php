<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public const TRIGGERS = ['above', 'below'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('price_actions', static function (Blueprint $table) {
            $table->id()->unique();
            $table->foreignId('user_id')->constrained(
                table: 'users', indexName: 'price_actions_user_id'
            );
            $table->enum('trigger', self::TRIGGERS);
            $table->float('price');
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
        Schema::dropIfExists('price_actions');
    }
};
