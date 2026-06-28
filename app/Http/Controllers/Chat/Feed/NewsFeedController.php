<?php

namespace App\Http\Controllers\Chat\Feed;
use App\Http\Controllers\Controller;

use App\Models\Chat;
use App\Models\ChatGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsFeedController extends Controller
{
    /**
     * Fetch latest solar-related news from NewsAPI
     * and push them as system messages into the "solar news" chat group.
     */
    public function syncSolarNews()
    {
        $apiKey = config('services.newsapi.key');
        $slug   = config('services.newsapi.slug', 'solar-news');

        if (!$apiKey) {
            return response()->json([
                'status'  => 'error',
                'message' => 'NEWSAPI_KEY not configured.',
            ], 500);
        }

        // 1) Ensure the news chat group exists
        $group = ChatGroup::firstOrCreate(
            ['slug' => $slug],
            [
                'name'       => 'Solar News',
                'created_by' => auth()->id() ?: 1, // fallback
            ]
        );

        // 2) Call NewsAPI
        $response = Http::get('https://newsapi.org/v2/everything', [
            'apiKey'   => $apiKey,
            'q'        => 'solar OR photovoltaik OR "solar energy" OR "solar energie"',
            'language' => 'de',
            'pageSize' => 10,
            'sortBy'   => 'publishedAt',
        ]);

        if (!$response->ok() || ($response->json('status') !== 'ok')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'NewsAPI request failed',
                'body'    => $response->json(),
            ], 500);
        }

        $articles = $response->json('articles', []);

        $created = 0;

        foreach ($articles as $article) {
            $title       = $article['title']       ?? null;
            $description = $article['description'] ?? '';
            $url         = $article['url']         ?? null;
            $sourceName  = $article['source']['name'] ?? '';

            if (!$title || !$url) {
                continue;
            }

            // Simple dedupe by URL + group
            $already = Chat::where('group_id', $group->id)
                ->where('type', 'system')
                ->where('message', 'like', '%' . $url . '%')
                ->exists();

            if ($already) {
                continue;
            }

            $messageText = "[Solar News] {$title}\n\n"
                . ($description ? "{$description}\n\n" : '')
                . ($sourceName ? "Quelle: {$sourceName}\n" : '')
                . $url;

            $chat = Chat::create([
                'group_id'      => $group->id,
                'from_user_id'  => null,     // system / bot
                'to_user_id'    => null,
                'type'          => 'system', // front-end shows "System:" in group
                'message'       => $messageText,
                // optional, if you have these columns:
                // 'payload'    => $article,
                // 'external_id'=> $url,
            ]);

            // OPTIONAL: if you already have a MessageSent event, you can broadcast:
            // event(new \App\Events\MessageSent($chat));

            $created++;
        }

        return response()->json([
            'status'      => 'ok',
            'group_id'    => $group->id,
            'articles_in' => count($articles),
            'created'     => $created,
        ]);
    }


    public function index()
{
    $news = \DB::table('chats')
        ->where('type', 'system')
        ->where('message', 'like', '[Solar News]%')
        ->orderBy('created_at')
        ->get(['id', 'message', 'created_at']);

    return response()->json(['messages' => $news]);
}
}
