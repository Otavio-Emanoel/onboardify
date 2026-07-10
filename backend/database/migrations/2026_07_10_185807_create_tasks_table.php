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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['video', 'quiz', 'pdf'])->default('pdf');
            $table->integer('pts')->default(10);
            $table->string('duration')->default('5 min');
            $table->string('video_url')->nullable();
            $table->text('quiz_question')->nullable();
            $table->json('quiz_options')->nullable();
            $table->integer('quiz_correct_index')->nullable();
            $table->string('pdf_title')->nullable();
            $table->json('pdf_content_pages')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
