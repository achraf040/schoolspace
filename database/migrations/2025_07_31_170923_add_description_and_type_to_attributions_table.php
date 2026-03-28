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
            $table->enum('type', [
                'access',           // Accès général
                'reservation',      // Réservation ponctuelle
                'permanent',        // Attribution permanente
                'temporary',        // Attribution temporaire
                'maintenance',      // Accès maintenance
                'administration'    // Accès administratif
            ])->default('access')->after('espace_id');
            
            $table->text('description')->nullable()->after('type');
            $table->date('start_date')->nullable()->after('description');
            $table->date('end_date')->nullable()->after('start_date');
            $table->json('access_hours')->nullable()->after('end_date'); // Heures d'accès spécifiques
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attributions', function (Blueprint $table) {
            $table->dropColumn(['type', 'description', 'start_date', 'end_date', 'access_hours']);
        });
    }
};
