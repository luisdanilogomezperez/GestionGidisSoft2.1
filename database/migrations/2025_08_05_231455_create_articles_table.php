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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('year')->nullable();
            $table->string('month')->nullable();
            $table->string('dissemination_medium')->nullable();
            $table->string('evidence_document')->nullable();
            $table->string('start_page')->nullable();
            $table->string('end_page')->nullable();
            $table->string('journal_name')->nullable();
            $table->string('article_type')->nullable();
            $table->string('volume')->nullable();
            $table->string('journal_issue')->nullable();
            $table->string('journal_series')->nullable();
            $table->string('publication_place')->nullable();
            $table->string('digital_identifier_doi')->nullable();
            $table->string('language')->nullable();
            $table->string('website_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
