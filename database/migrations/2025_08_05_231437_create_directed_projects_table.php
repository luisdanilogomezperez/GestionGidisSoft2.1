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
        Schema::create('directed_projects', function (Blueprint $table) {
            $table->id();
             $table->string('name');
            $table->string('product_type')->nullable();
            $table->string('start_year')->nullable();
            $table->string('end_year')->nullable();
            $table->string('start_month')->nullable();
            $table->string('end_month')->nullable();
            $table->string('orientation_type')->nullable();
            $table->string('total_pages')->nullable();
            $table->string('institution_name')->nullable();
            $table->string('academic_program')->nullable();
            $table->string('thesis_evaluation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directed_projects');
    }
};
