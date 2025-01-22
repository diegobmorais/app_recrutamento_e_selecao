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
        Schema::create('job_interview_candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_application_id');
            $table->string('sender');
            $table->text('content');
            $table->string('test_type');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('job_application_id')
                ->references('id')
                ->on('job_applications')
                ->onDelete('cascade');    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_interview_candidates');
    }
};
