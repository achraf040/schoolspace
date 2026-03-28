<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class FixUserEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:user-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix user emails to match their display_email based on assigned spaces';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Correction des emails utilisateurs...');
        $this->newLine();

        $users = User::where('role', 'user')->with('espaces')->get();
        $updated = 0;

        foreach ($users as $user) {
            $currentEmail = $user->email;
            $correctEmail = $user->display_email;
            
            if ($currentEmail !== $correctEmail) {
                // Mettre à jour l'email pour correspondre au display_email
                $user->update(['email' => $correctEmail]);
                
                $this->info("Utilisateur: {$user->name}");
                $this->line("  Email actuel: {$currentEmail}");
                $this->line("  Email corrigé: {$correctEmail}");
                $this->newLine();
                $updated++;
            } else {
                $this->line("Utilisateur: {$user->name} - Email OK: {$currentEmail}");
            }
        }

        $this->newLine();
        $this->info("Correction terminée! {$updated} utilisateurs mis à jour.");
        
        return 0;
    }
}
