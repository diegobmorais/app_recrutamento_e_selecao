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
        Schema::table('job_applications', function (Blueprint $table) {
            $table->decimal('behavioral_test_score', 5, 2)->nullable()->after('final_summary');
            $table->text('behavioral_test_summary')->nullable()->after('behavioral_test_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn('behavioral_test_score');
            $table->dropColumn('behavioral_test_summary');
        });
    }
};
