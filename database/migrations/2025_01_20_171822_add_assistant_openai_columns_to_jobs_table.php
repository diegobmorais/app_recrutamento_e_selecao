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
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('id_assistant_openai_pre_selection')->nullable()->after('created_by');
            $table->string('id_assistant_openai_behavioral_test')->nullable()->after('id_assistant_openai_pre_selection');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('id_assistant_openai_pre_selection');
            $table->dropColumn('id_assistant_openai_behavioral_test');
        });
    }
};
