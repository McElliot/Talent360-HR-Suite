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
        Schema::create('psychometric_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('psychometric_test_questions')->onDelete('cascade');
            $table->string('option_text');
            $table->string('option_value')->nullable(); // For storing different values than displayed text
            $table->decimal('score_weight', 5, 2)->default(0.00); // For weighted scoring
            $table->boolean('is_correct')->default(false); // For correct answers
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('metadata')->nullable(); // For additional option config
            $table->timestamps();

            $table->index('question_id');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psychometric_question_options');
    }
};
