<?php

namespace App\Http\Controllers\Employee\Calendar;
use App\Http\Controllers\Controller;

use App\Models\PersonalSettings;
use Illuminate\Http\Request;

class PersonalSettingsController extends Controller
{
 

    /**
     * Show the form for creating a new resource.
     */
    public function saveCalendarSettings(Request $request)
    {
        $userId = auth()->user()->name; // or employee_id
    
        $settings = PersonalSettings::updateOrCreate(
            ['employee_id' => $userId],
            ['calendar_settings' => $request->input('calendar_settings')]
        );
    
        return response()->json(['status' => 'success']);
    }
    
        public function getCalendarSettings()
    {
        $settings = \App\Models\PersonalSettings::where('employee_id', auth()->user()->name)->first();

        if ($settings && $settings->calendar_settings) {
            return response()->json(json_decode($settings->calendar_settings, true));
        }

        return response()->json([
            'favorites' => [],
            'calendar_view' => 'month',
            'theme' => 'default',
        ]);
    }




    public function get()
    {
        $userId = auth()->user()->name;
        $setting = PersonalSettings::where('employee_id', $userId)->first();
        return response()->json([
            'calendar_settings' => $setting->calendar_settings ?? []
        ]);
    }

    public function save(Request $request)
    {
        $userId = auth()->user()->name;
        $settings = $request->input('calendar_settings', []);

        PersonalSettings::updateOrCreate(
            ['employee_id' => $userId],
            ['calendar_settings' => $settings]
        );

        return response()->json(['status' => 'success']);
    }

    
}
