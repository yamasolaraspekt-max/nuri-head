<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Z2-W0-12 · Widerruf aller API-Token eines Nutzers — ohne ihn zu deaktivieren.
 *
 * **Was es NICHT doppelt, gemessen vor dem Bau:** Z2-W0-9 löscht die Token bereits beim
 * Deaktivieren (`UserController::deactive`, `adminUsersToggleActive`, `logOffUser`) und beim
 * abgelehnten Token-Antrag eines gesperrten Kontos. Was fehlte, ist der Fall **Widerruf ohne
 * Sperre**: ein verlorenes Gerät, ein gewechseltes Telefon — der Nutzer soll weiterarbeiten
 * können, nur nicht mit dem alten Token.
 *
 * `logout-all` im API-Controller ist Selbstbedienung: es braucht einen gültigen Token und hilft
 * genau dann nicht, wenn der Token in fremder Hand ist.
 */
class NurivaTokenWiderruf extends Command
{
    protected $signature = 'nuriva:token-widerruf {nutzer : E-Mail, Name oder ID des Nutzers}';

    protected $description = 'Widerruft ALLE API-Token eines Nutzers (ohne das Konto zu deaktivieren).';

    public function handle(): int
    {
        $schluessel = (string) $this->argument('nutzer');

        $nutzer = User::query()
            ->where('email', $schluessel)
            ->orWhere('name', $schluessel)
            ->when(ctype_digit($schluessel), fn ($q) => $q->orWhere('id', (int) $schluessel))
            ->first();

        if (! $nutzer) {
            $this->error("Kein Nutzer gefunden zu: {$schluessel}");

            return self::FAILURE;
        }

        if (! method_exists($nutzer, 'tokens')) {
            $this->error('Dieser Nutzer führt keine API-Token (Sanctum-Trait fehlt).');

            return self::FAILURE;
        }

        $vorher = $nutzer->tokens()->count();
        $nutzer->tokens()->delete();

        $this->info("Widerrufen: {$vorher} Token von {$nutzer->email} (ID {$nutzer->id}). Das Konto bleibt aktiv.");

        return self::SUCCESS;
    }
}
