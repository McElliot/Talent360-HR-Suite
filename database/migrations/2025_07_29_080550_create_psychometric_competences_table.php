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
        Schema::create('psychometric_competences', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Leadership", "Problem Solving"
            $table->string('code')->unique(); // "LDR", "PS"
            $table->text('description')->nullable();
            $table->foreignId('test_type_id')->constrained('psychometric_test_types');
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('psychometric_competences_test', function (Blueprint $table) {
            $table->foreignId('test_id')->constrained('psychometric_tests')->cascadeOnDelete();
            // Change this line:
            $table->foreignId('competency_id')->constrained('psychometric_competences')->cascadeOnDelete();
            $table->primary(['test_id', 'competency_id']);
            $table->integer('weight')->default(1);
            $table->timestamps();
        });

        Schema::create('psychometric_competences_question', function (Blueprint $table) {
            $table->foreignId('question_id')->constrained('psychometric_test_questions')->cascadeOnDelete();
            // And this line:
            $table->foreignId('competency_id')->constrained('psychometric_competences')->cascadeOnDelete();
            $table->primary(['question_id', 'competency_id']);
            $table->float('weight', 5, 2)->default(1.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psychometric_competences');
        Schema::dropIfExists('psychometric_competences_test');
        Schema::dropIfExists('psychometric_competences_question');
    }
};
