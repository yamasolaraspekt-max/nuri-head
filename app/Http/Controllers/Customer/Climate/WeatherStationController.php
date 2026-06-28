<?php

namespace App\Http\Controllers\Customer\Climate;
use App\Http\Controllers\Controller;

use App\Models\WeatherStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Exception;

class WeatherStationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.weather_station.station');
    }

    public function upload(Request $request)
    {
        try {
            // Validate uploaded file
            $request->validate([
                'csv_file' => 'required|mimes:csv,txt|max:2048',
            ]);

            // Save the uploaded file
            $file = $request->file('csv_file');
            $filePath = $file->storeAs('uploads', 'weather_stations.csv');
            
            // Read CSV
            $csv = Reader::createFromPath(storage_path('app/' . $filePath), 'r');
            $csv->setHeaderOffset(0); // Use the first row as the header

            // Insert records into the database
            foreach ($csv as $record) {
                WeatherStation::updateOrCreate(
                    ['icao' => $record['icao']],
                    [
                        'name' => $record['name'],
                        'latitude' => $record['latitude'],
                        'longitude' => $record['longitude'],
                        'elevation' => $record['elevation'],
                    ]
                );
            }

            return back()->with('success', 'CSV file uploaded and data inserted successfully!');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(WeatherStation $weatherStation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WeatherStation $weatherStation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WeatherStation $weatherStation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WeatherStation $weatherStation)
    {
        //
    }
}
