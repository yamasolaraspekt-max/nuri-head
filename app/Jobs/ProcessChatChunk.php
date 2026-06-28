<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ProcessChatChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $startId;
    public $endId;

    public function __construct($startId, $endId)
    {
        $this->startId = $startId;
        $this->endId = $endId;
    }

    public function handle()
    {
        $baseUrl = 'https://solaraspekt.bitrix24.de/rest/1/juf43v70pf63gor6/im.dialog.messages.get.json';

        for ($chatId = $this->startId; $chatId <= $this->endId; $chatId++) {
            $url = $baseUrl . '?DIALOG_ID=chat410&LIMIT=50000';

            $response = Http::get($url);

            if ($response->ok() && !empty($response->json('result.messages'))) {
                $this->saveChatData($response->json('result'));
            } else {
                \Log::error("Failed to fetch or no data for chat ID: $chatId");
            }
        }
    }

    private function saveChatData($data)
    {
        $users = collect($data['users'])->keyBy('id');

        foreach ($data['messages'] as $message) {
            $user = $users->get($message['author_id']);
            $formattedDate = $this->formatDate($message['date'] ?? null);

            DB::table('messages')->updateOrInsert(
                ['message_id' => $message['id']],
                [
                'chat_id' => $message['chat_id'],
                'author_id' => $message['author_id'],
                'text' => $message['text'],
                'date' => $formattedDate,
                'unread' => $message['unread'] ?? false,
                'uuid' => $message['uuid'] ?? null,
                'replaces' => json_encode($message['replaces']),
                'params' => json_encode($message['params']),
                'disappearing_date' => $message['disappearing_date'] ?? null,
                'user_name' => $user['name'] ?? null,
                'user_first_name' => $user['first_name'] ?? null,
                'user_last_name' => $user['last_name'] ?? null,
                'user_work_position' => $user['work_position'] ?? null,
                'user_avatar' => $user['avatar'] ?? null,
                'user_status' => $user['status'] ?? null,
                'user_departments' => json_encode($user['departments'] ?? []),
            ]);
        }
    }

    private function formatDate($isoDate)
    {
        try {
            return \Carbon\Carbon::parse($isoDate)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}

