<?php

namespace App\Console\Commands;

use App\Events\LeadEmailReceived;
use App\Models\LeadEmail;
use App\Models\LeadEmailAccounts;
use App\Models\LeadEmailDomainFilter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Webklex\IMAP\Facades\Client;

class SyncLeadEmails extends Command
{
    protected $signature = 'lead-emails:sync';
    protected $description = 'Fetch new emails from active lead email accounts';

    public function handle(): int
    {
        $accounts = LeadEmailAccounts::query()
            ->where('status', 'Published')
            ->get();

        $allowedDomains = LeadEmailDomainFilter::query()
            ->pluck('domain')
            ->map(fn ($d) => strtolower(trim($d)))
            ->filter()
            ->values()
            ->all();

        foreach ($accounts as $account) {
            try {
                $client = Client::make([
                    'host' => $account->host,
                    'port' => $account->port,
                    'encryption' => $account->encryption,
                    'validate_cert' => true,
                    'username' => $account->email,
                    'password' => $account->password,
                    'protocol' => 'imap',
                ]);

                $client->connect();

                $folder = $client->getFolder('INBOX');

                $messages = $folder->messages()
                    ->all()
                    ->limit(30)
                    ->setFetchOrder('desc')
                    ->get();

                foreach ($messages as $message) {
                    try {
                        $messageId = $message->getMessageId()?->first();

                        if (!$messageId) {
                            $messageId = 'uid-' . $account->id . '-' . $message->getUid();
                        }

                        $exists = LeadEmail::query()
                            ->where('message_id', $messageId)
                            ->exists();

                        if ($exists) {
                            continue;
                        }

                        $fromObj = $message->getFrom()[0] ?? null;
                        $fromEmail = strtolower(trim($fromObj?->mail ?? ''));
                        $fromName = trim($fromObj?->personal ?? '');
                        $domain = $fromEmail ? strtolower(substr(strrchr($fromEmail, "@"), 1)) : null;

                        if (!empty($allowedDomains) && $domain && !in_array($domain, $allowedDomains, true)) {
                            continue;
                        }

                        $fromFormatted = $fromName && $fromEmail
                            ? $fromName . ' <' . $fromEmail . '>'
                            : ($fromEmail ?: 'Unbekannt');

                        $saved = LeadEmail::create([
                            'message_id' => $messageId,
                            'from' => $fromFormatted,
                            'domain' => $domain,
                            'subject' => $message->getSubject()?->first(),
                            'body' => $message->getHTMLBody() ?: $message->getTextBody(),
                            'date' => $message->getDate()?->first(),
                            'is_read' => false,
                            'status' => 'new',
                        ]);

                        broadcast(new LeadEmailReceived($saved));
                    } catch (\Throwable $messageError) {
                        Log::error('Lead email save failed', [
                            'account_email' => $account->email,
                            'error' => $messageError->getMessage(),
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Lead email sync failed for account', [
                    'account_id' => $account->id,
                    'email' => $account->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return self::SUCCESS;
    }
}