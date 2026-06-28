<?php

namespace App\Http\Controllers\Email;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeadEmail;
use App\Models\LeadEmailAccounts;
use App\Models\LeadEmailDomainFilter;
use Webklex\IMAP\Facades\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class LeadEmailReaderController extends Controller
{
    public function fetchAndStore(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '256M');

        $accounts = LeadEmailAccounts::query()
            ->where('status', 'Published')
            ->get();

        Log::info('Data of Account', [$accounts]);

        $allowedDomains = LeadEmailDomainFilter::query()
            ->pluck('domain')
            ->map(fn ($d) => strtolower(trim($d)))
            ->filter()
            ->values()
            ->toArray();

        $savedCount = 0;
        $skippedCount = 0;
        $errors = [];

        Log::info('Lead email sync started', [
            'accounts' => $accounts->count(),
            'allowed_domains' => $allowedDomains,
        ]);

        foreach ($accounts as $account) {
            Log::info("Connecting to IMAP account: {$account->email}");

            try {
                $client = Client::make([
                    'host'          => $account->host,
                    'port'          => $account->port,
                    'encryption'    => $account->encryption,
                    'validate_cert' => false,
                    'username'      => $account->email,
                    'password'      => $account->password,
                    'protocol'      => 'imap',
                ]);

                $client->connect();
                $inbox = $client->getFolder('INBOX');

                $page = 1;
                $limit = 20;
                $maxPages = 50; // safety cap: 50 * 20 = 1000 messages max per sync

                while ($page <= $maxPages) {
                    $messages = $inbox->query()
                        ->since(now()->subDays(14))
                        ->limit($limit, $page)
                        ->get();

                    $count = count($messages);

                    Log::info("Fetched page for {$account->email}", [
                        'page' => $page,
                        'limit' => $limit,
                        'count' => $count,
                    ]);

                    if ($count === 0) {
                        break;
                    }

                    foreach ($messages as $msg) {
                        $result = $this->processMessage($msg, $allowedDomains);

                        if ($result === 'saved') {
                            $savedCount++;
                        } else {
                            $skippedCount++;
                        }
                    }

                    unset($messages);
                    gc_collect_cycles();

                    $page++;
                }
            } catch (\Throwable $e) {
                $errors[] = "Verbindung zu {$account->email} fehlgeschlagen.";
                Log::error("Lead email fetch failed for {$account->email}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $response = [
            'success' => empty($errors),
            'saved' => $savedCount,
            'skipped' => $skippedCount,
            'errors' => $errors,
            'unread_count' => LeadEmail::where('is_read', false)->count(),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($response);
        }

        if (!empty($errors)) {
            return redirect()
                ->route('lead.email.inbox')
                ->with('delete_msg', implode(' ', $errors));
        }

        return redirect()
            ->route('lead.email.inbox')
            ->with('save_msg', "E-Mails wurden aktualisiert. Neu gespeichert: {$savedCount}");
    }

   private function processMessage($msg, array $allowedDomains): string
    {
        try {
            $messageId = $this->extractMessageId($msg);

            if (!$messageId) {
                Log::info('Skipped email: missing message_id');
                return 'skipped';
            }

            if (LeadEmail::where('message_id', $messageId)->exists()) {
                Log::info("Skipped duplicate email", [
                    'message_id' => $messageId,
                ]);
                return 'skipped';
            }

            $fromData = $this->extractFrom($msg);
            $fromEmail = $fromData['email'];
            $fromName = $fromData['name'];
            $domain = $fromEmail ? strtolower(substr(strrchr($fromEmail, '@'), 1)) : null;

            if (!$fromEmail) {
                Log::info('Skipped email: missing sender', [
                    'message_id' => $messageId,
                ]);
                return 'skipped';
            }

            $isAllowed = empty($allowedDomains) || ($domain && in_array($domain, $allowedDomains, true));

            if (!$isAllowed) {
                Log::info("Skipped blocked domain", [
                    'message_id' => $messageId,
                    'domain' => $domain,
                ]);
                return 'skipped';
            }

            $subject = $this->extractSubject($msg);
            $date = $this->extractDate($msg);
            $body = $this->extractBody($msg);

            $fromDisplay = $fromName && $fromEmail
                ? $fromName . ' <' . $fromEmail . '>'
                : $fromEmail;

            LeadEmail::create([
                'message_id' => $messageId,
                'from'       => $fromDisplay,
                'domain'     => $domain,
                'subject'    => $subject,
                'body'       => $body,
                'date'       => $date,
                'is_read'    => false,
                'status'     => 'new',
            ]);

            Log::info("Saved new lead email", [
                'message_id' => $messageId,
                'from' => $fromDisplay,
                'domain' => $domain,
                'subject' => $subject,
            ]);

            return 'saved';
        } catch (\Throwable $e) {
            Log::error('Failed processing email', [
                'error' => $e->getMessage(),
            ]);

            return 'skipped';
        }
    }
    private function extractMessageId($msg): ?string
    {
        $raw = $msg->getMessageId();

        if (is_string($raw) && trim($raw) !== '') {
            return trim($raw);
        }

        if (is_object($raw) && method_exists($raw, 'first')) {
            $first = $raw->first();
            return $first ? trim((string) $first) : null;
        }

        if (is_array($raw) && !empty($raw[0])) {
            return trim((string) $raw[0]);
        }

        if (method_exists($msg, 'getUid')) {
            $uid = $msg->getUid();
            if ($uid) {
                return 'uid-' . trim((string) $uid);
            }
        }

        return null;
    }

    private function extractFrom($msg): array
    {
        $from = $msg->getFrom();
        $first = $from[0] ?? null;

        return [
            'email' => $first?->mail ? strtolower(trim((string) $first->mail)) : null,
            'name'  => $first?->personal ? trim((string) $first->personal) : null,
        ];
    }

    private function extractSubject($msg): ?string
    {
        $raw = $msg->getSubject();

        if (is_string($raw)) {
            return trim($raw);
        }

        if (is_object($raw) && method_exists($raw, 'first')) {
            return $raw->first() ? trim((string) $raw->first()) : null;
        }

        if (is_array($raw) && !empty($raw[0])) {
            return trim((string) $raw[0]);
        }

        return null;
    }

    private function extractDate($msg)
    {
        $raw = $msg->getDate();

        if ($raw instanceof \Carbon\Carbon) {
            return $raw;
        }

        if (is_object($raw) && method_exists($raw, 'first')) {
            return $raw->first();
        }

        return $raw;
    }

    private function extractBody($msg): ?string
    {
        $text = null;
        $html = null;

        try {
            $text = $msg->getTextBody();
        } catch (\Throwable $e) {
        }

        try {
            $html = $msg->getHTMLBody();
        } catch (\Throwable $e) {
        }

        if (!empty($text)) {
            return trim(mb_substr($text, 0, 50000));
        }

        if (!empty($html)) {
            return trim(mb_substr($html, 0, 50000));
        }

        return null;
    }

    public function inbox(Request $request)
    {
        $query = LeadEmail::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('from', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        if ($request->filled('domain')) {
            $query->where('domain', $request->domain);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $emails = $query->orderByDesc('date')->paginate(10);

        $availableDomains = LeadEmail::query()
            ->whereNotNull('domain')
            ->distinct()
            ->orderBy('domain')
            ->pluck('domain');

        return view('admin.lead_email.inbox.index', compact('emails', 'availableDomains'));
    }

    public function realtimeList(Request $request): JsonResponse
    {
        $query = LeadEmail::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('from', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        if ($request->filled('domain')) {
            $query->where('domain', $request->domain);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $emails = $query->orderByDesc('date')->take(10)->get();

        return response()->json([
            'rows' => $emails->map(function ($email) {
                return [
                    'id' => $email->id,
                    'from' => $email->from,
                    'subject' => $email->subject,
                    'domain' => $email->domain,
                    'date' => optional($email->date)->format('d.m.Y H:i'),
                    'is_read' => (bool) $email->is_read,
                ];
            }),
            'unread_count' => LeadEmail::where('is_read', false)->count(),
        ]);
    }

    public function show($id): JsonResponse
    {
        $email = LeadEmail::findOrFail($id);

        return response()->json([
            'id'      => $email->id,
            'from'    => $email->from,
            'subject' => $email->subject ?: '(Kein Betreff)',
            'body'    => html_entity_decode((string) $email->body),
            'date'    => optional($email->date)->format('d.m.Y H:i'),
            'domain'  => $email->domain,
        ]);
    }

    public function markAsRead($id): JsonResponse
    {
        $email = LeadEmail::findOrFail($id);

        if (!$email->is_read) {
            $email->update([
                'is_read' => true,
                'status' => 'read',
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'unread_count' => LeadEmail::where('is_read', false)->count(),
        ]);
    }

    public function getEmailDetails($id): JsonResponse
    {
        $email = LeadEmail::findOrFail($id);

        return response()->json([
            'subject' => $email->subject ?: '(Kein Betreff)',
            'from'    => $email->from,
            'domain'  => $email->domain,
            'date'    => optional($email->date)->format('d.m.Y H:i'),
            'body'    => $email->body,
        ]);
    }
}