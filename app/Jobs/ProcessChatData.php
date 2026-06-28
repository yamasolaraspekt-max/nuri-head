<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\BitrixChat;
use Illuminate\Support\Facades\DB;

class ProcessChatData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $startId;
    public $endId;

    /**
     * Create a new job instance.
     */
    public function __construct($startId, $endId)
    {
        $this->startId = $startId;
        $this->endId = $endId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $baseUrl = 'https://solaraspekt.bitrix24.de/rest/1/cro29l2273d1p9pd/im.dialog.get.json';

        for ($chatId = $this->startId; $chatId <= $this->endId; $chatId++) {
            $url = $baseUrl . '?DIALOG_ID=chat' . $chatId.'&LIMIT=50000';

            // Fetch data from API
            $response = Http::get($url);

            if ($response->ok()) {
                $data = $response->json();

                // Check if the chat data is available
                if (isset($data['result'])) {
                    $this->saveChat($data['result']);
                }
            }
        }
    }

    /**
     * Save chat data to the database.
     */
    private function saveChat($data)
    {
        DB::table('bitrix_chats')->updateOrInsert(
            ['id' => $data['id']],
            [
                'parent_chat_id' => $data['parent_chat_id'] ?? 0,
                'parent_message_id' => $data['parent_message_id'] ?? 0,
                'name' => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
                'owner' => $data['owner'] ?? null,
                'extranet' => $data['extranet'] ?? false,
                'avatar' => $data['avatar'] ?? null,
                'color' => $data['color'] ?? null,
                'type' => $data['type'] ?? null,
                'counter' => $data['counter'] ?? 0,
                'user_counter' => $data['user_counter'] ?? 0,
                'message_count' => $data['message_count'] ?? 0,
                'unread_id' => $data['unread_id'] ?? 0,
                'last_message_id' => $data['last_message_id'] ?? 0,
                'last_id' => $data['last_id'] ?? 0,
                'marked_id' => $data['marked_id'] ?? 0,
                'disk_folder_id' => $data['disk_folder_id'] ?? 0,
                'entity_type' => $data['entity_type'] ?? null,
                'entity_id' => $data['entity_id'] ?? null,
                'entity_data_1' => $data['entity_data_1'] ?? null,
                'entity_data_2' => $data['entity_data_2'] ?? null,
                'entity_data_3' => $data['entity_data_3'] ?? null,
                'restrictions' => json_encode($data['restrictions'] ?? []),
                'mute_list' => json_encode($data['mute_list'] ?? []),
                'date_create' => isset($data['date_create']) ? $this->formatDate($data['date_create']) : null,
                'message_type' => $data['message_type'] ?? null,
                'disappearing_time' => $data['disappearing_time'] ?? 0,
                'public' => $data['public'] ?? null,
                'role' => $data['role'] ?? null,
                'entity_link' => json_encode($data['entity_link'] ?? []),
                'permissions' => json_encode($data['permissions'] ?? []),
                'is_new' => $data['is_new'] ?? false,
                'readed_list' => json_encode($data['readed_list'] ?? []),
                'manager_list' => json_encode($data['manager_list'] ?? []),
            ]
        );
    }

    /**
     * Format the date to MySQL-compatible format.
     */
    private function formatDate($isoDate)
    {
        try {
            return \Carbon\Carbon::parse($isoDate)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}
