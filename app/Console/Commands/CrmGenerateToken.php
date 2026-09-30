<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CrmGenerateToken extends Command
{
    protected $signature   = 'crm:generate-token {name : Nom du token (ex: NomDuCRM)} {--user-email= : Email du compte admin propriétaire du token}';
    protected $description = 'Génère un token Sanctum avec l\'ability crm:read pour accès CRM';

    public function handle(): int
    {
        $email = $this->option('user-email');

        if ($email) {
            $user = User::where('email', $email)->first();
        } else {
            $user = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))
                ->orWhereHas('guide', fn ($q) => $q->whereNotNull('user_id'))
                ->orderBy('id')
                ->first();

            // Fallback : premier user de la base
            $user ??= User::orderBy('id')->first();
        }

        if (! $user) {
            $this->error('Aucun utilisateur trouvé. Précisez --user-email=votre@email.com');
            return Command::FAILURE;
        }

        $tokenName = $this->argument('name');

        // Révoque les anciens tokens portant le même nom pour éviter les doublons
        $user->tokens()->where('name', $tokenName)->delete();

        $token = $user->createToken($tokenName, ['crm:read']);

        $this->newLine();
        $this->info('Token CRM créé avec succès.');
        $this->table(
            ['Champ', 'Valeur'],
            [
                ['Nom',      $tokenName],
                ['Propriétaire', $user->email],
                ['Ability',  'crm:read'],
                ['Token',    $token->plainTextToken],
            ]
        );
        $this->newLine();
        $this->warn('⚠  Copiez ce token maintenant — il ne sera plus affiché ensuite.');
        $this->line('  Authorization: Bearer ' . $token->plainTextToken);
        $this->newLine();

        return Command::SUCCESS;
    }
}
