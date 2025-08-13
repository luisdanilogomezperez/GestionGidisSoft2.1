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
        Schema::create('other_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('year')->nullable();
            $table->string('month')->nullable();
            $table->string('language')->nullable();
            $table->string('dissemination_medium')->nullable();
            $table->string('publication_place')->nullable();
            $table->string('purpose')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_jobs');
    }
};
