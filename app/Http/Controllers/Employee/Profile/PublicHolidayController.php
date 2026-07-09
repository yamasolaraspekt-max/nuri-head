<?php

namespace App\Http\Controllers\Employee\Profile;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\PublicHoliday;

class PublicHolidayController extends Controller
{
    public function __construct()
    {
        // MASTER-01 P1-IDOR: HR-Rollen-Gate (permission:Employee), enforced mit heutigen user_rolls-Grants
        $this->middleware('permission:Employee,add')->only(['store']);
        $this->middleware('permission:Employee,delete')->only(['destroy']);
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.public_holiday.index');
    }

    public function fetch(Request $request)
    {
        $year = $request->get('year');
        $query = PublicHoliday::query();

        if ($year) {
            $query->whereYear('start_date', $year);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'country' => 'required|string|max:255',
        'state' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
    ]);

    // ✅ Duplicate check logic
    $query = PublicHoliday::where('start_date', $validated['start_date'])
                ->where('country', $validated['country']);

    if (!empty($validated['state'])) {
        $query->where('state', $validated['state']);
    }

    if (!empty($validated['city'])) {
        $query->where('city', $validated['city']);
    }

    if ($request->filled('holiday_id')) {
        $query->where('id', '!=', $request->holiday_id);
    }

    if ($query->exists()) {
        return response()->json([
            'status' => 'duplicate',
            'message' => 'Für das gewählte Datum existiert bereits ein Feiertag in diesem Bereich.'
        ], 409);
    }

    // Create or update
    if ($request->filled('holiday_id')) {
        PublicHoliday::findOrFail($request->holiday_id)->update($validated);
    } else {
        PublicHoliday::create($validated);
    }

    return response()->json(['status' => 'success']);
}



    public function show($id)
    {
        return response()->json(PublicHoliday::findOrFail($id));
    }

    public function destroy($id)
    {
        $holiday = PublicHoliday::findOrFail($id);
        $holiday->delete();

        return response()->json(['status' => 'deleted']);
    }
}
