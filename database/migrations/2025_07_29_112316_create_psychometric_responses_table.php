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
        Schema::create('psychometric_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('psychometric_tests');
            $table->foreignId('question_id')->constrained('psychometric_test_questions');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('option_id')->nullable()->constrained('psychometric_question_options');
            $table->text('text_response')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'test_id']);
            $table->unique(['question_id', 'user_id']); // One response per question
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psychometric_responses');
    }
};
