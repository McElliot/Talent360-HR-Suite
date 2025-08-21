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
        Schema::create('psychometric_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('psychometric_test_type_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->unsigned()->nullable(); // Duration in minutes, NULL if no time limit
            $table->unsignedSmallInteger('question_count')->default(0); // Total number of questions in the test
            $table->integer('version')->default(1)->unsigned();
            $table->integer('max_attempts')->nullable(); // NULL = unlimited
            $table->boolean('is_active')->default(true);
            $table->boolean('is_timed')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();

            $table->index('psychometric_test_type_id');
            $table->index('is_active');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psychometric_tests');
    }
};
