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
        Schema::create('book_chapters', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('year')->nullable();
            $table->string('month')->nullable();
            $table->string('dissemination_medium')->nullable();
            $table->string('evidence_document')->nullable();
            $table->string('start_page')->nullable();
            $table->string('end_page')->nullable();
            $table->string('total_pages')->nullable();
            $table->string('book_series')->nullable();
            $table->string('edition')->nullable();
            $table->string('publication_place')->nullable();
            $table->string('discipline')->nullable();
            $table->string('knowledge_area')->nullable();
            $table->string('institution_endorsement_certificate')->nullable();
            $table->string('credits_certificate')->nullable();
            $table->foreignId('book_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_chapters');
    }
};
