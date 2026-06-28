<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\NewsFeedController;

class SyncSolarNewsToChat extends Command
{
    protected $signature   = 'chat:sync-solar-news';
    protected $description = 'Fetch solar-related news from NewsAPI and push into the solar news chat group';

    public function handle()
    {
        // Reuse the controller logic via the service container
        /** @var NewsFeedController $controller */
        $controller = app(NewsFeedController::class);

        $response = $controller->syncSolarNews();

        $data = $response->getData(true);

        $this->info('Solar news sync finished: ' . json_encode($data));

        return 0;
    }
}
