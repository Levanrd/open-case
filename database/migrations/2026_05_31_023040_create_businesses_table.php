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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('registered_name');
            $table->string('trade_name')->nullable();
            $table->text('tin_encrypted')->nullable();
            $table->string('tin_last_four', 4)->nullable();
            $table->string('rdo_code')->nullable();
            $table->string('business_type');
            $table->string('taxpayer_type')->nullable();
            $table->text('registered_address')->nullable();
            $table->date('registration_date')->nullable();
            $table->string('business_activity')->nullable();
            $table->string('line_of_business')->nullable();
            $table->string('business_status')->default('active');
            $table->unsignedSmallInteger('last_year_of_operation')->nullable();
            $table->boolean('still_operating')->default(true);
            $table->string('primary_goal')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'business_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
