<?php

namespace App\Jobs;
use App\Models\AiMessage;
use App\Services\EmbeddingClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EmbedMessage implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
      public function __construct(public int $messageId) {}

    /**
     * Execute the job.
     */
    public function handle(EmbeddingClient $client): void
        {
            $msg = AiMessage::find($this->messageId);
            if (!$msg || !trim($msg->content ?? '')) return;
            try {
                $emb = $client->embed($msg->content);
                $msg->embedding = is_array($emb) ? array_values($emb) : [];
                $msg->save();
            } catch (\Throwable) {}
        }
}
