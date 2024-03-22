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
        Schema::create('sets', function (Blueprint $table)
        {
            $table->id();
            $table->foreignId('exercise_id')->constrained();
            $table->foreignId('workout_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->integer('position');
            $table->integer('repetitions');
            $table->integer('duration')->nullable();
            $table->integer('break_afterwards')->nullable();
            $table->timestamps();

            $table->unique(['exercise_id', 'workout_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
