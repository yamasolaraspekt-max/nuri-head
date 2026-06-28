<?php

namespace App\Jobs;

use App\Models\WeatherStation;
use Http;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWeatherData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
   public function handle()
    {
        $url = 'https://www.dwd.de/DE/leistungen/met_verfahren_mosmix/mosmix_stationskatalog.cfg?view=nasPublication&nn=16102';
        $response = Http::get($url);
        $data = $response->body();
        $lines = explode("\n", $data);

        foreach ($lines as $line) {
            if (preg_match('/^\d{5}\s/', $line)) {
                $parts = preg_split('/\s+/', $line);
                if (count($parts) >= 6) {
                    WeatherStation::create([
                        'icao' => $parts[1] === '----' ? null : $parts[1],
                        'name' => implode(' ', array_slice($parts, 2, -3)),
                        'latitude' => floatval($parts[count($parts) - 3]),
                        'longitude' => floatval($parts[count($parts) - 2]),
                        'elevation' => intval($parts[count($parts) - 1])
                    ]);
                }
            }
        }
    }
}
