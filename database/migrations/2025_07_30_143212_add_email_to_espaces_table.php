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
        Schema::table('espaces', function (Blueprint $table) {
            $table->string('email')->nullable()->after('nom');
        });
        
        // Mettre à jour les espaces existants avec un email temporaire
        \DB::table('espaces')->whereNull('email')->update([
            'email' => \DB::raw("CONCAT(LOWER(REPLACE(nom, ' ', '-')), '@espace.supmti.ac.ma')")
        ]);
        
        // Rendre la colonne unique après avoir mis à jour les données
        Schema::table('espaces', function (Blueprint $table) {
            $table->string('email')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('espaces', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
