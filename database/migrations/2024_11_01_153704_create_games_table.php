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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['waiting', 'playing', 'finished'])->default('waiting');
            $table->foreignId('player1_id')->constrained('users');
            $table->foreignId('player2_id')->nullable()->constrained('users');
            $table->foreignId('winner_id')->nullable()->constrained('users');
            $table->integer('moves_count')->default(0);
            $table->integer('board_size')->default(15);
            $table->foreignId('current_turn')->nullable()->constrained('users');
            $table->boolean('has_password')->default(false);
            $table->string('password')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
