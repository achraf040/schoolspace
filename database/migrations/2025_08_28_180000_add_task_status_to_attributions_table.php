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
        Schema::table('attributions', function (Blueprint $table) {
            $table->enum('task_status', [
                'pending',     // En attente
                'active',      // En cours
                'completed'    // Terminée
            ])->default('pending')->after('access_hours');
            
            $table->string('title')->nullable()->after('task_status');
            $table->timestamp('completed_at')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attributions', function (Blueprint $table) {
            $table->dropColumn(['task_status', 'title', 'completed_at']);
        });
    }
};