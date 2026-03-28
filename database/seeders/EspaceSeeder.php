<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Espace;

class EspaceSeeder extends Seeder
{
    public function run(): void
    {
        $espaces = [
            [
                'nom' => 'Scolarité',
                'description' => 'Gestion des inscriptions, emplois du temps et suivi académique des étudiants',
                'is_active' => true,
            ],
            [
                'nom' => 'Comptabilité',
                'description' => 'Gestion financière, facturation et suivi des paiements',
                'is_active' => true,
            ],
            [
                'nom' => 'Ressources Humaines',
                'description' => 'Gestion du personnel, paie et formation des employés',
                'is_active' => true,
            ],
            [
                'nom' => 'Pédagogie',
                'description' => 'Coordination pédagogique, programmes et méthodes d\'enseignement',
                'is_active' => true,
            ],
        ];

        foreach ($espaces as $espace) {
            Espace::create($espace);
        }
    }
}