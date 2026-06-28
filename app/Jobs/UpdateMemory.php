<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\AiChat;
use App\Services\ConversationMemory;
class UpdateMemory implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $chatId,
        public string $question,
        public string $answer,
        public string $lang
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ConversationMemory $memory): void
    {
        $chat = AiChat::find($this->chatId);
        if (!$chat) return;
        try { $memory->updateSummary($chat, $this->question, $this->answer, $this->lang); } catch (\Throwable) {}
    }
}
