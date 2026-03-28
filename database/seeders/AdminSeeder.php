<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
    ['email' => 'admin@supmti.ac.ma'],
    [
        'name' => 'Administrateur SUPMTI',
        'email' => 'admin@supmti.ac.ma',
        'password' => Hash::make('admin123'), // <-- hardcoded password
        'role' => 'admin',
        'is_active' => true,
    ]
);

        // Générer un mot de passe fort pour le test
        $testPassword = $this->generateStrongPassword();
        
        // Créer un compte de test
        User::updateOrCreate(
            ['email' => 'test@supmti.ac.ma'],
            [
                'name' => 'Utilisateur Test',
                'email' => 'test@supmti.ac.ma',
                'password' => Hash::make($testPassword),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        $this->command->info('================================================');
        $this->command->info('         COMPTES CRÉÉS AVEC SUCCÈS');
        $this->command->info('================================================');
        $this->command->info('Admin : admin@supmti.ac.ma');

        $this->command->info('');
        $this->command->info('Test : test@supmti.ac.ma');
        $this->command->info('Mot de passe : ' . $testPassword);
        $this->command->info('');
        $this->command->info('⚠️  IMPORTANT: Notez ces mots de passe et changez-les après la première connexion !');
        $this->command->info('================================================');
    }

    /**
     * Générer un mot de passe fort aléatoire
     */
    private function generateStrongPassword(): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        
        $password = '';
        
        // Assurer qu'on a au moins un caractère de chaque type
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        // Compléter avec des caractères aléatoires
        for ($i = 4; $i < 16; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Mélanger le mot de passe
        return str_shuffle($password);
    }
}