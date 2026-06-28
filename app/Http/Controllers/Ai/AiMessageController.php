<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Models\AiChat;
use App\Models\AiMessage;
use App\Services\CustomerContextBuilder;
use App\Services\OllamaClient;
use App\Services\PromptFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse; 
use App\Services\WeatherClient;
use App\Services\NormTempService;
use App\Services\RoofAreaEstimator;
use App\Services\ConversationMemory;
use App\Services\EmbeddingClient;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiMessageController extends Controller
{
    public function createChat(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required','exists:new_leads,id'],
            'title'       => ['nullable','string','max:200'],
        ]);

        $chat = AiChat::create([
            'user_id'          => $request->user()->id,
            'customer_id'      => $data['customer_id'],
            'title'            => $data['title'] ?? 'New chat',
            'last_activity_at' => now(),
        ]);

        return response()->json($chat); // <-- top-level { "id": ... }
    }
 
 
        public function ask(Request $req, AiChat $chat)
        {
            Gate::authorize('view', $chat);

            $question = $req->validate(['message' => ['required','string','max:8000']])['message'];

            $userMsg = AiMessage::create([
                'ai_chat_id' => $chat->id,
                'user_id'    => $req->user()->id,
                'role'       => 'user',
                'content'    => $question,
            ]);

            // ---- Trace helpers
            $rid = (string) Str::uuid();
            $t0  = microtime(true);
            $dbg = function(string $msg, array $ctx = []) use ($rid, $t0) {
                Log::info('[SSE '.$rid.'] '.$msg, $ctx + ['dt_ms' => (int) round((microtime(true)-$t0)*1000), 'rid' => $rid]);
            };

            

            $dbg('enter', [
                'chat_id' => $chat->id,
                'user_id' => $req->user()->id,
                'accept'  => $req->header('accept'),
                'ct'      => $req->header('content-type'),
                'xhr'     => $req->header('x-requested-with'),
            ]);

            // Release session lock (important for streaming)
            try { $req->session()->save(); $dbg('session_saved', ['sid' => $req->session()->getId()]); } catch (\Throwable $e) { $dbg('session_save_error', ['err'=>$e->getMessage()]); }

            $intent = $this->detectIntent($question);
            $lang   = $this->detectLang($question);
            $dbg('intent_lang', ['intent'=>$intent, 'lang'=>$lang]);

             if ($intent === 'branding') {
                    return response()->stream(function () use ($chat, $rid, $dbg, $lang) {
                        @ini_set('output_buffering', 'off');
                        @ini_set('zlib.output_compression', '0');
                        while (ob_get_level() > 0) { @ob_end_flush(); }
                        ignore_user_abort(true);

                        header('Content-Type: text/event-stream; charset=utf-8');
                        header('Cache-Control: no-cache, no-transform');
                        header('X-Accel-Buffering: no');

                        $flush = static function() { @ob_flush(); flush(); };
                        $sse   = static function(array $payload, ?string $event = null) use ($flush) {
                            if ($event) echo "event: {$event}\n";
                            echo 'data: '.json_encode($payload, JSON_UNESCAPED_UNICODE)."\r\n\r\n";
                            $flush();
                        };

                        echo str_repeat(' ', 32768)."\n"; $flush();
                        echo ": welcome\n\n"; $flush();
                        $sse(['status'=>'starting','rid'=>$rid], 'status');
                        $sse(['status'=>'generating','rid'=>$rid], 'status');

                        // Produce the canonical branding text (Markdown)
                        $answer = \App\Services\PromptFactory::brandingAnswer($lang);

                        // Stream it as a single chunk (fine for small payloads)
                        $sse(['chunk' => $answer]);

                        // Persist assistant message
                        try {
                            \App\Models\AiMessage::create([
                                'ai_chat_id' => $chat->id,
                                'role'       => 'assistant',
                                'content'    => trim($answer),
                            ]);
                            $chat->update(['last_activity_at'=>now()]);
                        } catch (\Throwable $e) {
                            $dbg('persist_error', ['err'=>$e->getMessage()]);
                        }

                        $sse(['done'=>true,'rid'=>$rid]);
                        $flush();
                    }, 200, [
                        'Content-Type'      => 'text/event-stream; charset=utf-8',
                        'Cache-Control'     => 'no-cache, no-transform',
                        'X-Accel-Buffering' => 'no',
                        'X-Debug-RID'       => $rid,
                    ]);
                }

            return response()->stream(function () use ($chat, $question, $intent, $lang, $rid, $dbg) {
                @ini_set('output_buffering', 'off');
                @ini_set('zlib.output_compression', '0');
                while (ob_get_level() > 0) { @ob_end_flush(); }
                ignore_user_abort(true);

                // HTTP/2-safe headers
                header('Content-Type: text/event-stream; charset=utf-8');
                header('Cache-Control: no-cache, no-transform');
                header('X-Accel-Buffering: no');

                $flush = static function() { @ob_flush(); flush(); };
                $sse   = static function(array $payload, ?string $event = null) use ($flush) {
                    if ($event) echo "event: {$event}\n";
                    echo 'data: '.json_encode($payload, JSON_UNESCAPED_UNICODE)."\r\n\r\n";
                    $flush();
                };

                // Force first bytes through proxies/buffers
                echo str_repeat(' ', 32768)."\n"; $flush();
                echo ": welcome\n\n"; $flush();
                $sse(['status'=>'starting','rid'=>$rid], 'status');

                // ---- Build context (timed)
                $brief = [];
                if ($intent !== 'branding') {
                    try {
                        $t = microtime(true);
                        $sse(['status'=>'reading_customer','rid'=>$rid], 'status');
                        $builder = app(\App\Services\CustomerContextBuilder::class);
                        $include = in_array($intent, ['pv','heizlast','battery','wp','sizing','roofarea'], true);
                        $brief   = $builder->build((int)$chat->customer_id, $include);
                        $dbg('context_ok', ['ms' => (int) round((microtime(true)-$t)*1000)]);
                    } catch (\Throwable $e) {
                        $dbg('context_error', ['err'=>$e->getMessage()]);
                        $sse(['status'=>'context_error','message'=>$e->getMessage(),'rid'=>$rid], 'status');
                    }
                }

                $messages = [
                    ['role'=>'system', 'content'=>\App\Services\PromptFactory::systemForIntent($intent, $lang)],
                    ['role'=>'user',   'content'=>\App\Services\PromptFactory::userWithContext($question, $brief, $intent, $lang)],
                ];

                if ($intent !== 'branding') {
                    try {
                        $sse(['status'=>'memory','rid'=>$rid], 'status');
                        $mem = app(\App\Services\ConversationMemory::class)->buildMessages($chat, $question, $brief, $intent, $lang);
                        $messages = array_merge($messages, $mem);
                    } catch (\Throwable $e) {
                        $dbg('memory_error', ['err'=>$e->getMessage()]);
                    }
                }

                // ---- Stream from model (REAL streaming via Guzzle)
                $client = \App\Services\OllamaClient::make();
                $buffer = '';
                $first  = true;
                $chunks = 0;
                $bytes  = 0;
                $sse(['status'=>'generating','rid'=>$rid], 'status');
                $dbg('ollama_start');

                try {
                    $tFirst = null;
                    foreach ($client->streamChat($messages, ['rid' => $rid]) as $chunk) {
                        if (function_exists('connection_aborted') && connection_aborted()) { $dbg('client_aborted'); break; }

                        // mark first byte arrival
                        if ($first) { $tFirst = microtime(true); $dbg('first_chunk', ['ms' => (int) round(($tFirst - ($GLOBALS['__t0'] ?? microtime(true)))*1000)]); $first = false; }

                        $chunks++;
                        $bytes += strlen($chunk);
                        $buffer .= $chunk;
                        $sse(['chunk'=>$chunk]);
                    }
                    $dbg('ollama_done', ['chunks'=>$chunks, 'bytes'=>$bytes]);
                } catch (\Throwable $e) {
                    $dbg('ollama_error', ['err'=>$e->getMessage()]);
                    $sse(['chunk'=>'⚠️ '.$e->getMessage()]);
                } finally {
                    try {
                        $assistant = AiMessage::create([
                            'ai_chat_id' => $chat->id,
                            'role'       => 'assistant',
                            'content'    => trim($buffer),
                        ]);
                        $chat->update(['last_activity_at'=>now()]);
                    } catch (\Throwable $e) {
                        $dbg('persist_error', ['err'=>$e->getMessage()]);
                    }

                    $sse(['done'=>true,'rid'=>$rid]);
                    $flush();
                }
            }, 200, [
                'Content-Type'      => 'text/event-stream; charset=utf-8',
                'Cache-Control'     => 'no-cache, no-transform',
                'X-Accel-Buffering' => 'no',
                'X-Debug-RID'       => $rid, // helpful in DevTools
            ]);
        }

        

    private function detectIntent(string $q): string
    {
        $s = mb_strtolower($q, 'UTF-8');

        // ⬇️ NEW: explicit developer/creator questions in EN/DE/FA
        if (
            preg_match('/\b(who\s+(built|made|created)\s+you|your\s+developer|who\s+is\s+your\s*(dev|developer)|creator)\b/u', $s) ||
            preg_match('/\b(wer).*(gebaut|entwickelt).*(dich|euch)\b/u', $s) ||
            preg_match('/\b(entwickler|dein\s*entwickler|wer\s*hat\s*dich\s*gebaut)\b/u', $s) ||
            preg_match('/\p{Arabic}+/u', $s) && preg_match('/(کی|چه\s*کسی).*(ساخت|توسعه|برنامه)\b/u', $s)
        ) {
            return 'branding';
        }

        // Domain sizing
        if (preg_match('/\b(heizlast|wärmepumpe|heat\s*pump|wp)\b/u', $s)) return 'heizlast';
        if (preg_match('/\b(pv|photovoltaik|module|kwp|dach|roof)\b/u', $s)) return 'pv';
        if (preg_match('/\b(battery|batterie|speicher)\b/u', $s)) return 'battery';
        if (preg_match('/\b(roof\s*area|dachfläche|roof\s*size|polygon|fläche)\b/u', $s)) return 'roofarea';

        // Workflow / info / CRM
        if (preg_match('/\b(workflow|phase|status|progress|fortschritt|aktivität|activities?)\b/u', $s)) return 'workflow';
        if (preg_match('/\b(weather|wetter|forecast|heute|today)\b/u', $s)) return 'weather';
        if (preg_match('/\b(norm.*temper|normaußentemperatur|nat|design\s*outdoor)\b/u', $s)) return 'normtemp';
        if (preg_match('/\b(appointment|termin|meeting|besichtigung|vor-ort)\b/u', $s)) return 'appointment';
        if (preg_match('/\b(problem|ticket|störung|fehler|issue|support)\b/u', $s)) return 'problems';
        if (preg_match('/\b(task|aufgabe|todo|to-do|personal\s*task)\b/u', $s)) return 'tasks';
        if (preg_match('/\b(address|adresse|anschrift|straße|plz|postcode|city|ort|email|telefon|phone)\b/u', $s)) return 'contact';
        if (preg_match('/\b(email|e-mail|anschreiben|write an email|compose email)\b/u', $s)) return 'email';
        if (preg_match('/\b(advise|advice|empfehlung|recommend|soll ich|kaufen|anschaffen)\b/u', $s)) return 'advice';

        return 'general';
    }

private function detectLang(string $q): string
{
    $s = mb_strtolower($q, 'UTF-8');

    // Persian script → fa (highest priority)
    if (preg_match('/[\x{0600}-\x{06FF}]/u', $s)) return 'fa';

    // Explicit English request
    if (preg_match('/\b(english|in english)\b/u', $s)) return 'en';

    // Likely German if diacritics or common German domain words
    if (preg_match('/[äöüß]/u', $s) ||
        preg_match('/\b(heizlast|wärmepumpe|adresse|termin|aufgabe|problem|angebot|rechnung|dachfläche|pv|entwickler|gebaut)\b/u', $s)) {
        return 'de';
    }

    // If it’s Latin letters and no German markers, prefer EN
    if (preg_match('/\p{Latin}/u', $s)) return 'en';

    // Fallback to browser
    $nav = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    if (stripos($nav, 'fa') !== false) return 'fa';
    if (stripos($nav, 'de') !== false) return 'de';

    return 'en';
}



private function languageGate(string $lang): string
{
    // Hard enforce output language
    return match ($lang) {
        'fa' => "فقط و فقط به زبان فارسی پاسخ بده. از زبان دیگری استفاده نکن.",
        'de' => "Antworte ausschließlich auf Deutsch. Verwende keine andere Sprache.",
        default => "Answer exclusively in English. Do not switch languages.",
    };
}

private function contextGate(array $brief, string $intent, string $lang): string
{
    // Guardrails: use given customer context; ask only for missing fields
    $rules = [
        "Use the CUSTOMER_CONTEXT_JSON below. Do NOT ask for the user's name, phone, or address if a customer is already selected.",
        "If a specific field is missing, ask ONLY for that field. Do not ask for unrelated identity info.",
        "Do not ask for CRM identity — use the 'identity' section of the context.",
    ];

    // Task-specific nudges
    if ($intent === 'heizlast') {
        $rules[] = "Task: Heizlast (design heat load) estimation. Use provided fields if available:";
        $rules[] = "- norm_outdoor_temp_c, tech.building.heated_area_m2, tech.envelope.u_values (if present), infiltration/ventilation assumptions.";
        $rules[] = "Show clear steps and list assumptions. If a key field is missing, ask ONLY for that field (e.g., heated area).";
    }

    $rulesText = implode("\n- ", $rules);

    return "RULES:\n- {$rulesText}\n\nCUSTOMER_CONTEXT_JSON:\n".json_encode($brief, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
}

 
    public function destroy(Request $request, AiChat $chat)
    {
        // Ensure only owner can delete
        $this->authorize('delete', $chat);

        // If your DB FK already cascades, this is enough:
        $chat->delete();

        return $request->wantsJson()
            ? response()->json(['ok' => true])
            : redirect()->route('ai.chats')->with('status', 'Chat deleted');
    }
 
}
