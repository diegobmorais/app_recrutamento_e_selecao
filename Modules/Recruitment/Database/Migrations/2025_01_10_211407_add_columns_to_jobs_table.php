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
            $table->boolean('qualify_lead')->default(false)->after('workspace');
            $table->boolean('receive_notification')->default(false)->after('qualify_lead');
            $table->boolean('activate_pre_selection')->default(false)->after('receive_notification');
            $table->integer('average')->default(0)->after('activate_pre_selection');       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn(['qualify_lead', 'receive_notification', 'activate_pre_selection']);
        });
    }
};
