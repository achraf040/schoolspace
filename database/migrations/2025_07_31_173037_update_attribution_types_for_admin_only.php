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
            // Modifier l'enum pour avoir seulement les 3 types administratifs
            $table->dropColumn('type');
        });
        
        Schema::table('attributions', function (Blueprint $table) {
            $table->enum('type', [
                'permanente',    // Attribution permanente 
                'ponctuelle',    // Attribution ponctuelle
                'temporaire'     // Attribution temporaire
            ])->default('permanente')->after('espace_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attributions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        
        Schema::table('attributions', function (Blueprint $table) {
            $table->enum('type', [
                'access',
                'reservation', 
                'permanent',
                'temporary',
                'maintenance',
                'administration'
            ])->default('access')->after('espace_id');
        });
    }
};