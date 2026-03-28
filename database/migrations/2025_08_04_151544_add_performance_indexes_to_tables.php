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
        // INDEX SUR LA TABLE USERS
        Schema::table('users', function (Blueprint $table) {
            // Index pour les recherches par email (connexion)
            $table->index('email', 'idx_users_email');
            
            // Index pour les recherches par rôle
            $table->index('role', 'idx_users_role');
            
            // Index pour les utilisateurs actifs
            $table->index('is_active', 'idx_users_is_active');
            
            // Index composite pour les requêtes courantes (utilisateurs actifs par rôle)
            $table->index(['role', 'is_active'], 'idx_users_role_active');
            
            // Index pour les recherches par nom (tri alphabétique)
            $table->index('name', 'idx_users_name');
            
            // Index pour les dates de création (tri chronologique)
            $table->index('created_at', 'idx_users_created_at');
        });

        // INDEX SUR LA TABLE ESPACES
        Schema::table('espaces', function (Blueprint $table) {
            // Index pour les espaces actifs (filtrage principal)
            $table->index('is_active', 'idx_espaces_is_active');
            
            // Index pour les recherches par nom
            $table->index('nom', 'idx_espaces_nom');
            
            // Index pour les recherches par email
            $table->index('email', 'idx_espaces_email');
            
            // Index pour les dates de création
            $table->index('created_at', 'idx_espaces_created_at');
        });

        // INDEX SUR LA TABLE ATTRIBUTIONS
        Schema::table('attributions', function (Blueprint $table) {
            // Index sur user_id pour les requêtes "attributions d'un utilisateur"
            $table->index('user_id', 'idx_attributions_user_id');
            
            // Index sur espace_id pour les requêtes "utilisateurs d'un espace"
            $table->index('espace_id', 'idx_attributions_espace_id');
            
            // Index sur le type d'attribution (filtrage)
            $table->index('type', 'idx_attributions_type');
            
            // Index composite pour les requêtes complexes
            $table->index(['user_id', 'espace_id'], 'idx_attributions_user_espace');
            $table->index(['user_id', 'type'], 'idx_attributions_user_type');
            $table->index(['espace_id', 'type'], 'idx_attributions_espace_type');
            
            // Index pour les dates (tri chronologique et filtrage par période)
            $table->index('start_date', 'idx_attributions_start_date');
            $table->index('end_date', 'idx_attributions_end_date');
            $table->index('created_at', 'idx_attributions_created_at');
            
            // Index composite pour les attributions actives
            $table->index(['start_date', 'end_date'], 'idx_attributions_period');
        });

        // INDEX SUR LA TABLE FAILED_JOBS (performance des queues)
        if (Schema::hasTable('failed_jobs')) {
            Schema::table('failed_jobs', function (Blueprint $table) {
                $table->index('failed_at', 'idx_failed_jobs_failed_at');
                $table->index('queue', 'idx_failed_jobs_queue');
            });
        }

        // INDEX SUR LA TABLE PERSONAL_ACCESS_TOKENS (si Sanctum est utilisé)
        if (Schema::hasTable('personal_access_tokens')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->index('tokenable_type', 'idx_tokens_tokenable_type');
                $table->index('tokenable_id', 'idx_tokens_tokenable_id');
                $table->index(['tokenable_type', 'tokenable_id'], 'idx_tokens_tokenable');
                $table->index('expires_at', 'idx_tokens_expires_at');
                $table->index('created_at', 'idx_tokens_created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // SUPPRIMER LES INDEX DE LA TABLE USERS
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_email');
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_is_active');
            $table->dropIndex('idx_users_role_active');
            $table->dropIndex('idx_users_name');
            $table->dropIndex('idx_users_created_at');
        });

        // SUPPRIMER LES INDEX DE LA TABLE ESPACES
        Schema::table('espaces', function (Blueprint $table) {
            $table->dropIndex('idx_espaces_is_active');
            $table->dropIndex('idx_espaces_nom');
            $table->dropIndex('idx_espaces_email');
            $table->dropIndex('idx_espaces_created_at');
        });

        // SUPPRIMER LES INDEX DE LA TABLE ATTRIBUTIONS
        Schema::table('attributions', function (Blueprint $table) {
            $table->dropIndex('idx_attributions_user_id');
            $table->dropIndex('idx_attributions_espace_id');
            $table->dropIndex('idx_attributions_type');
            $table->dropIndex('idx_attributions_user_espace');
            $table->dropIndex('idx_attributions_user_type');
            $table->dropIndex('idx_attributions_espace_type');
            $table->dropIndex('idx_attributions_start_date');
            $table->dropIndex('idx_attributions_end_date');
            $table->dropIndex('idx_attributions_created_at');
            $table->dropIndex('idx_attributions_period');
        });

        // SUPPRIMER LES INDEX DES AUTRES TABLES
        if (Schema::hasTable('failed_jobs')) {
            Schema::table('failed_jobs', function (Blueprint $table) {
                $table->dropIndex('idx_failed_jobs_failed_at');
                $table->dropIndex('idx_failed_jobs_queue');
            });
        }

        if (Schema::hasTable('personal_access_tokens')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->dropIndex('idx_tokens_tokenable_type');
                $table->dropIndex('idx_tokens_tokenable_id');
                $table->dropIndex('idx_tokens_tokenable');
                $table->dropIndex('idx_tokens_expires_at');
                $table->dropIndex('idx_tokens_created_at');
            });
        }
    }
};