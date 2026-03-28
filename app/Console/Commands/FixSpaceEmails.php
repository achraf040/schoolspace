<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Espace;

class FixSpaceEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:space-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix space emails to remove accents and special characters';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Correction des emails d\'espaces...');
        $this->newLine();

        $espaces = Espace::all();
        $updated = 0;

        foreach ($espaces as $espace) {
            $oldEmail = $espace->display_email;
            $newEmail = $espace->generateEmailFromName();
            
            if ($oldEmail !== $newEmail) {
                // Mettre à jour l'email sans accents
                $espace->update(['email' => $newEmail]);
                
                $this->info("Espace: {$espace->nom}");
                $this->line("  Ancien email: {$oldEmail}");
                $this->line("  Nouvel email: {$newEmail}");
                $this->newLine();
                $updated++;
            } else {
                $this->line("Espace: {$espace->nom} - Email OK: {$oldEmail}");
            }
        }

        $this->newLine();
        $this->info("Correction terminée! {$updated} espaces mis à jour.");
        
        return 0;
    }
}
