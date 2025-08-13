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
        Schema::create('research_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('scope')->nullable();
            $table->string('project_type')->nullable();
            $table->string('start_year')->nullable();
            $table->string('end_year')->nullable();
            $table->string('start_month')->nullable();
            $table->string('end_month')->nullable();
            $table->string('funding_source')->nullable();
            $table->longText('summary')->nullable();
            $table->string('is_funded')->nullable();
            $table->string('participation_type')->nullable();
            $table->string('institution_role')->nullable();
            $table->string('is_solidary')->nullable();
            $table->string('funding_type')->nullable();
            $table->string('institution_name')->nullable();
            $table->string('administrative_act_code_number')->nullable();
            $table->string('institution_project_code')->nullable();
            $table->bigInteger('counterpart_value')->nullable();
            $table->bigInteger('project_value_without_counterpart')->nullable();
            $table->date('administrative_act_date')->nullable();
            $table->string('number_pages')->nullable();
            $table->longText('linked_productions_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_projects');
    }
};
