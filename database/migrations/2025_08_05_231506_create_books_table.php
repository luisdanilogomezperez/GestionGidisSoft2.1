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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('isbn')->nullable();
            $table->string('year')->nullable();
            $table->string('month')->nullable();
            $table->string('discipline')->nullable();
            $table->string('publisher')->nullable();
            $table->string('publisher_type')->nullable();
            $table->string('dissemination_medium')->nullable();
            $table->string('publication_place')->nullable();
            $table->string('evidence_document')->nullable();
            $table->string('credits_certificate')->nullable();
            $table->string('institution_endorsement_certificate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
