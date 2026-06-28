<?php

// app/Services/ConversationMemory.php
namespace App\Services;

use App\Models\AiChat;
use App\Models\AiMessage;

class ConversationMemory
{
    /**
     * Build LLM messages with:
     * - system prompt
     * - memory summary (if present)
     * - K most similar past turns
     * - last N turns (rolling window)
     * - current user question with customer brief
     */
    public function buildMessages(
        AiChat $chat,
        string $question,
        array $brief,
        string $intent,
        string $lang,
        int $recentTurns = 10,
        int $recallK = 6
    ): array {
        // Recent window (excluding the just-saved question, we’ll add below)
        $recent = AiMessage::where('ai_chat_id', $chat->id)
            ->orderBy('id', 'desc')
            ->take($recentTurns * 2) // user+assistant pairs
            ->get()
            ->reverse()
            ->map(fn($m) => ['role' => $m->role, 'content' => (string)$m->content])
            ->values()
            ->all();

        // Semantic recall (retrieve similar to the current question)
        $recall = $this->topSimilar($chat, $question, $recallK);

        // Compose
        $messages = [];

        // system comes from PromptFactory in controller, so skip here

        // memory summary (as a system reminder)
        if ($chat->memory_summary) {
            $messages[] = [
                'role' => 'system',
                'content' => "CONVERSATION SUMMARY (persistent memory):\n".$chat->memory_summary,
            ];
        }

        // semantic recall inject (as a tool/context block)
        if ($recall) {
            $snips = collect($recall)->map(function($m, $i){
                $ts = optional($m->created_at)->format('Y-m-d H:i');
                return "- {$ts} [{$m->role}]: ".mb_substr($m->content, 0, 800);
            })->implode("\n");
            $messages[] = [
                'role' => 'system',
                'content' => "RELEVANT PAST TURNS (semantic recall):\n".$snips,
            ];
        }

        // recent rolling window
        foreach ($recent as $r) { $messages[] = $r; }

        // finally, current user turn with customer brief
        $messages[] = [
            'role' => 'user',
            'content' => \App\Services\PromptFactory::userWithContext($question, $brief, $intent, $lang),
        ];

        return $messages;
    }

    /** @return \Illuminate\Support\Collection<AiMessage> */
        protected function topSimilar(AiChat $chat, string $query, int $k): ?\Illuminate\Support\Collection
        {
            $client = EmbeddingClient::make();
            $qv = $client->embed($query);

            $msgs = \App\Models\AiMessage::where('ai_chat_id', $chat->id)
                ->whereNotNull('embedding')
                ->orderBy('id','desc')
                ->take(400)
                ->get();

            if ($msgs->isEmpty()) return collect();

                $scored = $msgs->map(function($m) use ($qv){
                // Normalize embedding to array (handles old string JSON rows)
                $vec = $m->embedding;
                if (is_string($vec)) {
                    $vec = json_decode($vec, true) ?: [];
                }
                if (!is_array($vec) || empty($vec)) {
                    $m->similarity = 0.0;
                    return $m;
                }

                $sim = EmbeddingClient::cosine($qv, $vec);
                $m->similarity = is_finite($sim) ? $sim : 0.0;
                return $m;
            });
            return $scored->sortByDesc('similarity')->take($k)->values();
        }

    /**
     * Update running summary given previous summary + new turn (Q/A).
     */
    public function updateSummary(AiChat $chat, string $userQ, string $assistantA, string $lang = 'de'): void
    {
        $sys = <<<SYS
You are a concise meeting minutes taker. Update the ongoing conversation summary for future context.
Keep only stable facts, decisions, preferences, and key numbers. Remove irrelevant chit-chat.
Limit to ~120–180 words. Write in the user's language.
SYS;

        $prev = (string) $chat->memory_summary;

        $user = <<<USR
Previous summary:
{$prev}

New turn:
- User: {$userQ}
- Assistant: {$assistantA}

Update the summary accordingly.
USR;

        $messages = [
            ['role' => 'system', 'content' => $sys],
            ['role' => 'user', 'content' => $user],
        ];

        $client = \App\Services\OllamaClient::make();

        // non-streaming summarize (simple call via the same /chat endpoint)
        $text = '';
        foreach ($client->streamChat($messages, ['options' => ['temperature' => 0.2, 'stream' => true]]) as $chunk) {
            $text .= $chunk;
        }

        $chat->memory_summary = trim($text) ?: $prev;
        $chat->memory_updated_at = now();
        $chat->save();
    }
}
