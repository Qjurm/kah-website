<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUserInteractive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactief een nieuwe gebruiker aanmaken';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎵 Gebruiker Aanmaken\n');

        // Step 1: Naam
        $name = $this->ask('Wat is de volledige naam?');
        
        // Step 2: Email
        $email = $this->ask('Wat is het emailadres?');
        
        // Check if email exists
        if (User::where('email', $email)->exists()) {
            $this->error("❌ Dit emailadres bestaat al!");
            return 1;
        }

        // Step 3: Wachtwoord
        $password = $this->secret('Wat is het wachtwoord?');
        $passwordConfirm = $this->secret('Bevestig het wachtwoord');
        
        if ($password !== $passwordConfirm) {
            $this->error("❌ Wachtwoorden komen niet overeen!");
            return 1;
        }

        // Step 4: Role
        $role = $this->choice('Wat is de rol?', ['musician', 'admin'], 0);

        // Step 5: Approved status
        $approved = $this->confirm('Is deze gebruiker meteen goedgekeurd?', true);

        // Step 6: Instrument (if musician)
        $instruments = [];
        if ($role === 'musician') {
            $this->line("\n📯 Instrumenten selecteren (laat leeg om te beëindigen)");
            
            $counter = 1;
            while (true) {
                $instrument = $this->ask("Instrument #{$counter} (of Enter om klaar te zijn)");
                
                if (empty($instrument)) {
                    break;
                }
                
                $instruments[] = $instrument;
                $counter++;
            }
            
            if (empty($instruments)) {
                $this->warn("⚠️  Geen instrumenten geselecteerd");
            }
        }

        // Summary before creation
        $this->line("\n📋 Samenvatting:");
        $this->line("  Naam: <info>{$name}</info>");
        $this->line("  Email: <info>{$email}</info>");
        $this->line("  Rol: <info>{$role}</info>");
        $this->line("  Goedgekeurd: <info>" . ($approved ? 'Ja' : 'Nee') . "</info>");
        if (!empty($instruments)) {
            $this->line("  Instrumenten: <info>" . implode(', ', $instruments) . "</info>");
        }

        // Confirm
        if (!$this->confirm("\n✅ Gebruiker aanmaken?")) {
            $this->info("Geannuleerd.");
            return 0;
        }

        // Create user
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => $role,
                'approved' => $approved,
                'instruments' => !empty($instruments) ? json_encode($instruments) : null,
            ]);

            $this->info("\n✅ Gebruiker aangemaakt!");
            $this->line("  ID: <info>{$user->id}</info>");
            $this->line("  Email: <info>{$user->email}</info>");
            
            if ($role === 'musician' && !$approved) {
                $this->line("\n💡 <comment>Tip:</comment> Deze gebruiker moet eerst goedgekeurd worden in /beheer/gebruikers");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Fout bij aanmaken: " . $e->getMessage());
            return 1;
        }
    }
}
