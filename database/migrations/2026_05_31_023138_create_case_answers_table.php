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
        Schema::create('case_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_case_id')->constrained()->cascadeOnDelete();
            $table->string('question_key');
            $table->json('answer_value')->nullable();
            $table->timestamps();

            $table->unique(['tax_case_id', 'question_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_answers');
    }
};
