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
        Schema::create('psychometric_test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('psychometric_test_id')->constrained()->onDelete('cascade');
            $table->text('parent_question')->nullable();
            $table->text('question_text');
            $table->enum('answer_type', [
                'multiple_choice', // Multiple select
                'radio',           // Single select
                'open_ended',      // Text input
                'likert_scale',    // 1-5, 1-7 scales
                'true_false',      // Boolean
                'dropdown',        // Select menu
                'matrix',          // Grid/matrix questions
                'ranking',         // Drag-and-drop ranking
                'date',            // Date picker
                'file_upload',     // File upload
                'numeric',         // Number input
                'text_area',       // Long text
            ])->default('radio');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('question_number', 20);
            $table->json('validation')->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index('psychometric_test_id');
            $table->index('answer_type');
            $table->index('sort_order');
            $table->index('question_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psychometric_test_questions');
    }
};
