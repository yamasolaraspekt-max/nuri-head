<?php

namespace App\Http\Controllers\Employee\Note;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\PersonalNote;
use App\Models\MainAppointment;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

class PersonalNoteReminderController extends Controller
{
    /**
     * Constructor to apply authentication middleware.
     */
    public function __construct()
    {
        $this->middleware('auth'); // Use 'auth:sanctum' for API authentication with Sanctum
    }

   
        public function getDueReminders()
        {
            // Ensure the user is authenticated
            $user = DB::table('users')->where('name', auth()->user()->name)->first();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

 

            $now = Carbon::now();
            $twentyMinutesLater = $now->copy()->addMinutes(1); // Calculate time 1 minute later (adjust if needed)

            // Use the authenticated user's ID
            $userId = $user->name; 

            // Fetch due Personal Notes
            $dueNotes = PersonalNote::where('user_id', $userId)
                ->where(function ($query) use ($now, $twentyMinutesLater) {
                    $query->whereNotNull('reminder_date')
                        ->whereDate('reminder_date', '<=', $now->toDateString())
                        ->orWhere(function ($q) use ($now, $twentyMinutesLater) {
                            $q->whereNotNull('reminder_time')
                            ->whereDate('reminder_date', $now->toDateString())
                            ->whereBetween('reminder_time', [$now->toTimeString(), $twentyMinutesLater->toTimeString()]);
                            
                        });
                })
                ->where('is_notified', false)
                ->get();

            // Fetch due Appointments
            $dueAppointments = MainAppointment::where('created_by', $userId)
                ->where(function ($query) use ($now, $twentyMinutesLater) {
                    $query->whereNotNull('reminder_date')
                        ->whereDate('reminder_date', '<=', $now->toDateString())
                        ->orWhere(function ($q) use ($now, $twentyMinutesLater) {
                            $q->whereNotNull('reminder_time')
                            ->whereDate('reminder_date', $now->toDateString())
                            ->whereBetween('reminder_time', [$now->toTimeString(), $twentyMinutesLater->toTimeString()]);
                            
                        });
                })
                ->where('is_notified', false)
                ->get();
 

            // Merge results into a single response
            return response()->json([
                'personal_notes' => $dueNotes,
                'appointments' => $dueAppointments
            ]);
        }


      public function updateReminderStatus(Request $request, $id)
    {
        

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Try to find the reminder in Personal Notes first
        $reminder = PersonalNote::where('user_id', auth()->user()->name)->find($id);

        if (!$reminder) {
            // If not found in Personal Notes, check MainAppointment
            $reminder = MainAppointment::where('created_by', auth()->user()->name)->find($id);
        }

        if (!$reminder) {
            return response()->json(['error' => 'Reminder not found'], 404);
        }

        // Update the reminder based on the action
        if ($request->action === 'complete') {
            $reminder->update(['is_notified' => true]); 
        } elseif ($request->action === 'repeat') {
            $reminder->update(['is_notified' => false]); 
        }

        return response()->json(['success' => true, 'message' => 'Reminder updated successfully.']);
    }



}
