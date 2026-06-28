<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessWeatherData;
use App\Models\Customer;
use App\Models\Temperature;
use App\Models\WeatherStation;
use DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class ToolsController extends Controller
{
    /**
     * Require authentication for all actions in this controller.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of tools.
     */
    public function index()
    {
        return view('admin.tools.index');
    }


    public function getPVData()
    {
        // Define the base URL and parameters
        $url = 'https://re.jrc.ec.europa.eu/api/PVcalc';
        $params = [
            'lat' => 45,            // Latitude
            'lon' => 8,             // Longitude
            'peakpower' => 1,       // Peak power of PV system in kW
            'loss' => 14,           // System losses in percent
            'usehorizon' => 1,      // Optional: Consider horizon shadows
            // Additional optional parameters can be added here if needed
            // 'outputformat' => 'json'  // Optional: Specify response format
        ];

        // Initialize the Guzzle client
        $client = new Client();

        try {
            // Send the GET request with the query parameters
            $response = $client->request('GET', $url, [
                'query' => $params
            ]);

            // Parse the response as JSON
            $data = json_decode($response->getBody(), true);

            // Return the data as a JSON response in Laravel
            return response()->json($data);

        } catch (\Exception $e) {
            // Handle exceptions and return error message
            return response()->json([
                'error' => 'Failed to retrieve data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Fetch PVGIS data and display details.
     */

    /**
     * Fetch weather and PVGIS data for a specific customer.
     */
    public function weather($id)
    {
        $customer = Customer::findOrFail($id);
        $temperature = Temperature::where('postcode', '=', $customer->postcode)->first();

        $lat = $customer->lat;
        $lon = $customer->lon;
        $city = $customer->city;

        $code = DB::table('weather_stations')
                ->where('latitude', 'like', "%$lat%")
                ->where('longitude', 'like', "%$lon%")
                ->orWhere('name', 'like', "%$city%")
                ->select('*')
                ->get();

        $client = new \GuzzleHttp\Client();

        // Fetch weather data
        try {
            $response = $client->request('GET', 'https://weatherbit-v1-mashape.p.rapidapi.com/forecast/3hourly', [
                'headers' => [
                    'X-RapidAPI-Host' => 'weatherbit-v1-mashape.p.rapidapi.com',
                    'X-RapidAPI-Key' => config('services.rapidapi.key'),
                ],
                'query' => [
                    'lat' => $lat,
                    'lon' => $lon
                ]
            ]);
            
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
            } else {
                return redirect()->back()->with('delete_msg', 'Failed to retrieve weather data');
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            logger()->error("Client error: " . $e->getResponse()->getBody());
            return redirect()->back()->with('delete_msg', 'Client error when calling the weather API');
        } catch (\Exception $e) {
            logger()->error("General error: " . $e->getMessage());
            return redirect()->back()->with('delete_msg', 'An error occurred while calling the weather API');
        }

        $germanMonths = [
            1 => 'Januar', 2 => 'Februar', 3 => 'März', 4 => 'April',
            5 => 'Mai', 6 => 'Juni', 7 => 'Juli', 8 => 'August',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Dezember'
        ];

        $averageTemperature = $temperature ? Temperature::where('postcode', $customer->postcode)->avg('outside_temp') : 0;
        $minTemperature = $temperature ? Temperature::where('postcode', $customer->postcode)->min('outside_temp') : 0;
        $maxTemperature = $temperature ? Temperature::where('postcode', $customer->postcode)->max('outside_temp') : 0;

        
 

        if ($temperature) {
            return view('admin.pvgis.pvgis_details', [
                'data' => $data,
                'customer' => $customer,
                'germanMonths' => $germanMonths,
                'averageTemperature' => $averageTemperature,
                'minTemperature' => $minTemperature,
                'maxTemperature' => $maxTemperature,
                'temperature' => $temperature, 
            ]);
        } else {
            return redirect()->back()->with('delete_msg', 'The Customer is outside the range of our work');
        }
    }


   private function weatherman($id){
        $customer = Customer::findOrFail($id);
        $lat = $customer->lat;
        $lon = $customer->lon;

        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('GET', 'https://weatherbit-v1-mashape.p.rapidapi.com/forecast/3hourly', [
                'headers' => [
                    'X-RapidAPI-Host' => 'weatherbit-v1-mashape.p.rapidapi.com',
                    'X-RapidAPI-Key' => config('services.rapidapi.key'),
                ],
                'query' => [
                    'lat' => $lat,
                    'lon' => $lon
                ]
            ]);
            $data = json_decode($response->getBody(), true);
            return $data;
        } catch (\Exception $e) {
            logger()->error("Weatherman error: " . $e->getMessage());
            return null;
        }
    }


    /**
     * Fetch solar data from the PVGIS API.
     */

     public function pvgis($lat, $lon)
     {
         $lat =  50.30824234102121; // Default to 0 if not provided
         $lon = 8.567471328325974; // Default to 0 if not provided 
         $response = $this->fetchSolarData($lat, $lon);
 
         return $response;
     }
 
    private function fetchSolarData($lat, $lon)
    {
        $response = Http::get('https://re.jrc.ec.europa.eu/api/PVcalc?lat=45&lon=8&peakpower=1&loss=14', [
            'lat' => $lat,
            'lon' => $lon,
            'peakpower' => 10,
            'loss' => 1,
            'angle' => 30,
            'aspect' => 0,
            'outputformat' => 'json',
        ]);

        \Log::debug('API Response:', $response->json());  // Log the response to inspect structure

        return $response->json();

    }

    /**
     * Parse the solar data JSON response.
     */
    private function parseSolarData($jsonData)
    {

        if (isset($jsonData['outputs']['monthly'])) {
            foreach ($jsonData['outputs']['monthly'] as $monthData) {
                if (! isset($monthData['month'])) {
                    \Log::error('Month key is missing', $monthData);

                    continue;  // Skip this iteration if 'month' key is missing
                }
                $data[] = [
                    'month' => $monthData['month'],
                    'E_d' => $monthData['E_d'],
                    'E_m' => $monthData['E_m'],
                    'H_i_d' => $monthData['H(i)_d'],
                    'H_i_m' => $monthData['H(i)_m'],
                    'SD_m' => $monthData['SD_m'],
                ];
            }
        } else {
            \Log::error('Monthly output is not set in the API response');
        }

        return $data;
    }


public function fetchWeatherData()
{
     
    $data = ProcessWeatherData::dispatch(); 
 
}


public function weatherAPI($city){

   
    $api = "https://s3.eu-central-1.amazonaws.com/app-prod-static.warnwetter.de/v16/forecast_mosmix_".$city.".json";

    $response = Http::get($api);

    return $response;

}

public function fetchPvgis(Request $request)
{
    $lat = $request->latitude;
    $lon = $request->longitude;
    $peakPower = $request->peakpower;
    $loss = $request->loss;
    $angle = $request->angle;
    $aspect = $request->aspect;
    $batterySize = $request->battery_size;
    $cutoff = $request->cutoff;
    $consumption = $request->consumption;

    // PV Performance
    $pvResponse = Http::get('https://re.jrc.ec.europa.eu/api/PVcalc', [
        'lat' => $lat,
        'lon' => $lon,
        'peakpower' => $peakPower,
        'loss' => $loss,
        'angle' => $angle,
        'aspect' => $aspect,
        'outputformat' => 'json',
    ]);

    // Standalone System (SHScalc)
    $shsResponse = Http::get('https://re.jrc.ec.europa.eu/api/SHScalc', [
        'lat' => $lat,
        'lon' => $lon,
        'peakpower' => $peakPower,
        'batterysize' => $batterySize,
        'consumptionday' => $consumption,
        'cutoff' => $cutoff,
        'outputformat' => 'json',
    ]);

    // Monthly Radiation (MRcalc)
    $mrResponse = Http::get('https://re.jrc.ec.europa.eu/api/MRcalc', [
        'lat' => $lat,
        'lon' => $lon,
        'horirrad' => 1,
        'outputformat' => 'json',
    ]);

    $responseDr = Http::get('https://re.jrc.ec.europa.eu/api/DRcalc', [
        'lat' => $lat,
        'lon' => $lon,
        'month' => 3,
        'global' => 1,
        'direct' => 1,
        'diffuse' => 1,
        'outputformat' => 'json',
    ]);
    
    $drData = $responseDr->json();
    

    return redirect()->back()
        ->withInput($request->all())
        ->with('pv_data', $pvResponse->json())
        ->with('shs_data', $shsResponse->json())
        ->with('mr_data', $mrResponse->json())
        ->with('dr_data', $drData)
        ->with('active_step', $request->input('active_step', 'step-1'));
}




}
