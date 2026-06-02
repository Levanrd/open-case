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
        Schema::create('case_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_case_id')->constrained()->cascadeOnDelete();
            $table->string('result_type');
            $table->string('risk_level');
            $table->unsignedTinyInteger('risk_score')->default(0);
            $table->text('generated_summary');
            $table->json('recommendations')->nullable();
            $table->boolean('disclaimer_accepted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_results');
    }
};
