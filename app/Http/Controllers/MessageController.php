<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Jobs\ProcessChatChunk; 
use App\Jobs\ProcessChatData;


use DB;


class MessageController extends Controller
{
    public function dispatchChatProcessingJobs($startId = 1, $endId = 2000, $chunkSize = 100)
    {
        for ($currentStart = $startId; $currentStart <= $endId; $currentStart += $chunkSize) {
            $currentEnd = min($currentStart + $chunkSize - 1, $endId);
            ProcessChatChunk::dispatch($currentStart, $currentEnd);
        }

        return response()->json(['message' => 'Jobs dispatched successfully!']);
    }


public function fetchAndSaveChats($startId = 1, $endId = 1000)
{
    $baseUrl = 'https://solaraspekt.bitrix24.de/rest/1/cro29l2273d1p9pd/im.recent.list.json';
    $successCount = 0;

    for ($chatId = $startId; $chatId <= $endId; $chatId++) {
        $url = $baseUrl . '?DIALOG_ID=chat' . $chatId;

        // Fetch data from API
        $response = Http::get($url);

        if ($response->ok()) {
            $data = $response->json();

            // Check if items exist in the response
            if (!empty($data['result']['items'])) {
                foreach ($data['result']['items'] as $item) {
                    $this->saveChatRec($item);
                    $successCount++;
                }
            }
        }
    }

    return response()->json([
        'message' => 'Search completed.',
        'chats_processed' => $successCount,
    ]);
}
 

public function dispatchChatJobs($startId = 1, $endId = 1000, $chunkSize = 100)
{
    for ($currentStart = $startId; $currentStart <= $endId; $currentStart += $chunkSize) {
        $currentEnd = min($currentStart + $chunkSize - 1, $endId);

        // Dispatch a job for the current chunk
        ProcessChatData::dispatch($currentStart, $currentEnd);
    }

    return response()->json([
        'message' => 'Jobs dispatched successfully!',
    ]);
}


    public function index($user){
        $data['messages'] = DB::table('messages') 
                    ->select('messages.*')  
                    ->get();
        $data['chats'] = DB::table('bitrix_chats')
                        ->select('bitrix_chats.*')
                        ->get();
 
        return view('admin.chats.chat', $data);
    }
}
