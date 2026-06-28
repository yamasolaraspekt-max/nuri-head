<?php

namespace App\Http\Controllers;

use App\Models\ChecklistRoom;
use App\Models\Customer;
use App\Models\HeatingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use DB;


class ChecklistRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
public function index($customer_id)
{
    try {
        // Fetch rooms based on customer_id, making sure to specify which table the customer_id comes from
        $data = DB::table('checklist_rooms')
            ->join('checklist_apartments', 'checklist_apartments.id', '=', 'checklist_rooms.story_id')
            ->select('checklist_rooms.*', 'checklist_apartments.story')
            ->where('checklist_rooms.customer_id', $customer_id)   
            ->get();

        return response()->json($data, 200);
    } catch (\Exception $e) {
        Log::error('Error fetching room data:', [
            'message' => $e->getMessage(),
            'stack' => $e->getTraceAsString(),
        ]);

        return response()->json(['error' => 'An internal server error occurred.'], 500);
    }
}



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Log the incoming request for debugging
            Log::info('Room Data:', $request->all());

            // Validate the incoming data
            $validate = $request->validate([
                'room.*.customer_id' => 'required|integer',  // Validate customer_id
                'room.*.story_id' => 'required|integer',
                'room.*.unit' => 'nullable|string',
                'room.*.room_size' => 'required|numeric', // Numeric validation for room_size
                'room.*.heating_type' => 'nullable|string',
            ]);

            // Initialize variable to track total room size
            $totalRoomSize = 0;

            // Loop through each room in the validated data and calculate total room size
            foreach ($validate['room'] as $room) {
                $totalRoomSize += $room['room_size'];

                // Fetch the checklist_apartment corresponding to the story_id
                $apartment = DB::table('checklist_apartments')
                    ->where('id', $room['story_id'])
                    ->first();

                if (!$apartment) {
                    return response()->json(['error' => 'Ungültige Etage oder Wohneinheit'], 400);  // Return error if apartment not found
                }

                // Check if the total room sizes exceed the living space of the apartment
                if ($totalRoomSize > $apartment->living_space) {
                    return response()->json([
                        'error' => 'Die gesamte Raumgröße überschreitet die verfügbare Wohnfläche von ' . $apartment->living_space . ' m² für die Etage.'
                    ], 400); // Return validation error
                }
            }

            // Store the data in the database
            foreach ($validate['room'] as $room) {
                ChecklistRoom::create([
                    'customer_id' => $room['customer_id'],  // Save customer_id
                    'story_id' => $room['story_id'],
                    'unit' => $room['unit'],
                    'room_size' => $room['room_size'],
                    'heating_type' => $room['heating_type'],
                ]);
            }

            return response()->json(['save_msg' => 'Die Raum wurde erfolgreich gespeichert']);
        } catch (\Exception $e) {
            Log::error('Error saving room data:', [
                'message' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Ein interner Fehler ist aufgetreten.'], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(ChecklistRoom $checklistRoom)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChecklistRoom $checklistRoom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function details($id, $customer_id)
    {
        $customer = Customer::find($customer_id);
              if ($customer) {
            $data['customer'] = $customer;
        } else {
            return redirect()->back()->with('delete_msg', 'Der Kunde ist nicht im System');
        }
        $data['heating_types'] = HeatingType::where('status', 'Published')->get();
         $data['electro'] = DB::table('brands')
            ->where('purpose', 'ELEKTRO')
            ->get();


        return view('admin.customer.products_details.checklist.wp.checklist', $data);


    }

    /**
     * Remove the specified resource from storage.
     */
       public function destroy($id)
    {
        $data = ChecklistRoom::find($id);

        if ($data) {
            $data->delete();
            return response()->json(['success' => 'The data deleted successfully']);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }
}
