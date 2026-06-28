<?php

namespace App\Http\Controllers;

use App\Models\PVTools;
use App\Models\Customer;
use App\Models\Temperature;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\ClimateLocation;
use DB;

class PVToolsController extends Controller
{
    public function index()
    {
        return view('admin.pvgis.index');
    }

    public function fetchByPostcode(Request $request)
    {
        $request->validate([
            'postcode' => ['required', 'string', 'max:20'],
            'peakpower' => ['nullable', 'numeric', 'min:0'],
            'loss' => ['nullable', 'numeric', 'min:0'],
            'angle' => ['nullable', 'numeric', 'min:0', 'max:90'],
            'aspect' => ['nullable', 'numeric'],
        ]);

        $postcode = trim((string) $request->get('postcode', ''));
        $googleKey = (string) env('GOOGLE_MAPS_KEY', '');

        if ($googleKey === '') {
            return response()->json([
                'success' => false,
                'message' => 'Google Maps API key is missing.',
            ], 500);
        }

        $client = new Client([
            'timeout' => 60,
        ]);

        try {
            // Google Geocoding: restrict to German postal code
            $geoResponse = $client->request('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
                'query' => [
                    'components' => 'postal_code:' . $postcode . '|country:DE',
                    'language'   => 'de',
                    'key'        => $googleKey,
                ],
            ]);

            $geoPayload = json_decode((string) $geoResponse->getBody(), true);

            if (!is_array($geoPayload) || ($geoPayload['status'] ?? null) !== 'OK' || empty($geoPayload['results'][0])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Postleitzahl konnte über Google nicht gefunden werden.',
                    'google_status' => $geoPayload['status'] ?? null,
                ], 404);
            }

            $first = $geoPayload['results'][0];
            $components = $first['address_components'] ?? [];
            $geometry = $first['geometry']['location'] ?? null;

            if (!is_array($geometry) || !isset($geometry['lat'], $geometry['lng'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google hat keine Koordinaten für diese Postleitzahl zurückgegeben.',
                ], 422);
            }

            $city = null;
            $country = null;

            foreach ($components as $component) {
                $types = $component['types'] ?? [];

                if (in_array('locality', $types, true)) {
                    $city = $component['long_name'] ?? $city;
                }

                if (!$city && in_array('postal_town', $types, true)) {
                    $city = $component['long_name'] ?? $city;
                }

                if (!$city && in_array('administrative_area_level_3', $types, true)) {
                    $city = $component['long_name'] ?? $city;
                }

                if (in_array('country', $types, true)) {
                    $country = $component['short_name'] ?? $country;
                }
            }

            $lat = (float) $geometry['lat'];
            $lon = (float) $geometry['lng'];

            $pvgisParams = [
                'lat'          => $lat,
                'lon'          => $lon,
                'peakpower'    => (float) $request->get('peakpower', 45),
                'loss'         => (float) $request->get('loss', 14),
                'usehorizon'   => 1,
                'outputformat' => 'json',
            ];

            if ($request->filled('angle')) {
                $pvgisParams['angle'] = (float) $request->get('angle');
            }

            if ($request->filled('aspect')) {
                $pvgisParams['aspect'] = (float) $request->get('aspect');
            }

            $pvResponse = $client->request('GET', 'https://re.jrc.ec.europa.eu/api/v5_3/PVcalc', [
                'query' => $pvgisParams,
            ]);

            $pvPayload = json_decode((string) $pvResponse->getBody(), true);

            $monthly = data_get($pvPayload, 'outputs.monthly.fixed', []);
            $totals  = data_get($pvPayload, 'outputs.totals.fixed', []);

            return response()->json([
                'success' => true,
                'location' => [
                    'postcode' => $postcode,
                    'city'     => $city,
                    'country'  => $country ?: 'DE',
                    'lat'      => $lat,
                    'lon'      => $lon,
                ],
                'input'   => $pvgisParams,
                'monthly' => $monthly,
                'totals'  => $totals,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'PVGIS/Google Daten konnten nicht geladen werden.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
